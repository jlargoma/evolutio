<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use Stripe;
use App\Models\Rates;
use App\Models\User;
use App\Models\Dates;

class ApiController extends Controller {

  private $items = [
      17 => 'cita-nutricion',
      23 => 'fisio-sesion',
      25 => 'fisio-ecografia',
      27 => 'fisio-sesion-suelo-pelvico'
  ];
  private $itemsType = [
      17 => 'nutri',
      23 => 'fisio',
      25 => 'fisio',
      27 => 'fisio',
  ];
  private $itemsCoachs = [
      'nutri' => 1715,
      'fisio' => 1971,
  ];

  public function index() {
    
  }

  public function getItems(Request $request) {
    $type = $request->input('type');
    switch ($type) {
      case 'fisioterapia':
        $type = 'fisio';
        break;
    }

    $IDs = [];
    foreach ($this->itemsType as $k => $v)
      if ($v == $type)
        $IDs[] = $k;



    $oRates = Rates::whereIN('id', $IDs)
                    ->where('status', 1)->get();
    $response = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        if (isset($this->items[$r->id]))
          $response[] = [
              $this->items[$r->id],
              $r->name,
              $r->price
          ];
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
    $ecograf = ($rID == 25);
    $response = [
        'name' => $oRate->name,
        'price' => $oRate->price,
        'availables' => $sCitas->datesAvails($rType, date('Y-m-d', strtotime('+1 days')), date('Y-m-d', strtotime('+31 days')), $ecograf)
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

    $coachID = $this->getCoachID($rType, $date);

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
    $uRate->coach_id = $coachID;
    $uRate->save();

    $oDates = new \App\Models\Dates();
    $oDates->id_rate = $rID;
    $oDates->id_user = $oUser->id;
    $oDates->id_coach = $coachID;
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

  function getCoachID($type = 'fisio', $date = null) {

    $date = '2022-04-07 19:00:00';
    $dateTime = strtotime($date);
    $week = date('w', $dateTime);
    $hour = date('H', $dateTime);

    $IDs = [];
    $aCoachs = User::whereCoachs($type)->where('status', 1)->orderBy('priority')->pluck('id')->toArray();
    
    $coachTimes = \App\Models\CoachTimes::whereIn('id_coach', $aCoachs)->get();
    if ($coachTimes) {
      foreach ($coachTimes as $i) {
        unset($aCoachs[array_search($i->id_coach, $aCoachs)]);
        $cTimes = json_decode($i->times, true);
        if (isset($cTimes[$week]) && isset($cTimes[$week][$hour]) && $cTimes[$week][$hour] == 1
        ) {
          $IDs[] = $i->id_coach;
        }
      }
    }
    if (count($IDs) > 0) {
      if (count($aCoachs) > 0)
        $IDs = array_merge($IDs, $aCoachs);
    } else
      $IDs = $aCoachs;

    foreach ($IDs as $cId) {
      $alreadyExit = Dates::where('date', $date)->where('id_coach', $cId)->count();
      if ($alreadyExit < 2)
        return $cId;
    }
    
    return 'dd'. $this->itemsCoachs[$type];
    
  }

}
