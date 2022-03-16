<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use Stripe;
use App\Models\Rates;
use App\Models\User;

class ApiController extends Controller {

  private $items = [
      17 => 'cita-nutricion',
      25 => 'fisio-ecografia',
//      27 => 'fisio-sesion-suelo-pelvico'
  ];
  private $itemsType = [
      17 => 'nutri',
      25 => 'fisio',
      27 => 'fisio'
  ];
  private $itemsCoachs = [
      17 => 1715,
      25 => 1971,
      27 => 1971
  ];

  public function index() {
    
  }

  public function getItems(Request $request) {
    $oRates = Rates::whereIN('id', array_keys($this->items))
                    ->where('status', 1)->get();
    $response = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        $response[$this->items[$r->id]] = $r->price;
      }
    }

    return response()->json($response);
  }

  public function getItem(Request $request) {
    $name = $request->input('slug', null);
//    $name = 'cita-nutricion';
    if (!$name)
      return null;

    $rID = array_search($name, $this->items);
    if (!$rID)
      return null;

    $oRate = Rates::find($rID);
    if (!$oRate)
      return null;

    $rType = $this->itemsType[$rID];

    $sCitas = new \App\Services\CitasService();
//    $lst = $sCitas->datesAvails($rType, date('Y-m-d', strtotime('+1 days')), date('Y-m-d', strtotime('+31 days')));

    $response = [
        'price' => $oRate->price,
        'availables' => $sCitas->datesAvails($rType, date('Y-m-d', strtotime('+1 days')), date('Y-m-d', strtotime('+31 days')))
    ];

    return response()->json($response);
  }

  public function addAppointment(Request $request) {

    $name = $request->input('name', null);
    $email = $request->input('email', null);
    $phone = $request->input('phone', null);
    $date = $request->input('date', null);
    $service = $request->input('service', null);
    $price = $request->input('price', null);
    $stripe = $request->input('stripe', null);

    $rID = array_search($service, $this->items);
    if (!$rID)
      return null;

    $oRate = Rates::find($rID);
    if (!$oRate)
      return null;

    $rType = $this->itemsType[$rID];

    $id_coach = $this->itemsCoachs[$rID];

    $sCitas = new \App\Services\CitasService();
    $response = [
        'price' => $oRate->price,
        'availables' => $sCitas->datesAvails($rType, date('Y-m-d', strtotime('+1 days')), date('Y-m-d', strtotime('+31 days')))
    ];

    $oUser = User::where('email', $email)->first();
    if (!$oUser) {
      $oUser = new User();
      $oUser->name = $request->input('name', null);
      $oUser->email = $email;
      $oUser->password = str_random(60); //bcrypt($request->input('password'));
      $oUser->remember_token = str_random(60);
      $oUser->role = 'user';
      $oUser->telefono = $phone;
      $oUser->save();
    }

    $dateTime = strtotime($date);
    $uRate = new \App\Models\UserRates();
    $uRate->id_user = $oUser->id;
    $uRate->id_rate = $rID;
    $uRate->rate_year = date('Y', $dateTime);
    $uRate->rate_month = date('n', $dateTime);
    $uRate->price = $price;
    $uRate->coach_id = $id_coach;
    $uRate->save();

    $oDates = new \App\Models\Dates();
    $oDates->id_rate = $rID;
    $oDates->id_user = $oUser->id;
    $oDates->id_coach = $id_coach;
    $oDates->id_user_rates = $uRate->id;
    $oDates->date_type = $rType;
    $oDates->date = $date;
    $oDates->updated_at = date('Y-m-d H:i:s');
    $oDates->save();
    $oDates->setMetaContent('fromWeb', 'evolutio.tv');

    $sStripe = new \App\Services\StripeService();
    $cID = $sStripe->getUser_byemail($email);

    \App\Models\Stripe3DS::addNew($oUser->id, $stripe, $cID, 'cita', ['dID' => $oDates->id]);
    return 'OK';
  }

}
