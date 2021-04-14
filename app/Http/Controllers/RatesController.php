<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use Stripe;
use App\Models\Rates;
use App\Models\TypesRate;

class RatesController extends Controller {

  public function index() {
    return view('/admin/rates/index', [
        'types' => TypesRate::all(),
        'newRates' => Rates::where('status', 1)->orderBy('order', 'asc')->orderBy('name', 'asc')->get(),
        'oldRates' => Rates::where('status', 0)->orderBy('order', 'asc')->orderBy('name', 'asc')->get(),
    ]);
  }

  public function newRate() {
    return view('/admin/rates/new', [
        'taxes' => Rates::all(),
        'typesRate' => TypesRate::all(),
    ]);
  }

  public function create(Request $request) {
    $rates = new Rates();

    $rates->name = $request->input('name');
    $rates->max_pax = $request->input('max_pax');
    $rates->type = $request->input('type');
    $rates->price = $request->input('price');
    $rates->mode = $request->input('mode');
    $rates->status = $request->input('status');
    $rates->cost = $request->input('cost');
    $rates->tarifa = $request->input('tarifa');
    $rates->order = 99;

    return redirect()->back()->with(['success'=>'Servicio agregado']);
  }

  public function actualizar($id) {

    return view('/admin/rates/update', [
        'rate' => Rates::find($id),
        'typesRate' => TypesRate::all(),
    ]);
  }

  public function update(Request $request) {

    $id = $request->input('id');
    $rateUpadate = Rates::find($id);
    $rateUpadate->name = $request->input('name');
    $rateUpadate->max_pax = $request->input('max_pax');
    $rateUpadate->type = $request->input('type');
    $rateUpadate->price = $request->input('price');
    $rateUpadate->mode = $request->input('mode');
    $rateUpadate->order = $request->input('order');
    $rateUpadate->cost = $request->input('cost');
    $rateUpadate->tarifa = $request->input('tarifa');
    if ($rateUpadate->save()) {


//      if ($rateUpadate->planStripe != "") {
//        $stripe = new Stripe;
//        $stripe = Stripe::make(HomeController::$stripe['key']);
//
//        $plan = $stripe->plans()->update($rateUpadate->planStripe, [
//            'name' => $rateUpadate->name,
//                // 'amount'               => floatval($rateUpadate->price),
//                // 'currency'             => 'EUR',
//                // 'interval'             => 'month',
//                // 'interval_count'       => $rateUpadate->mode,
//        ]);
//      }

      echo "Cambiada!!";
    }
  }

  public function delete($id) {
    $rate = Rates::find($id);

//    $stripe = new Stripe;
//    $stripe = Stripe::make(HomeController::$stripe['key']);
//    $plan = $stripe->plans()->delete($rate->planStripe);
    $rate->status = 0;
    if ($rate->save()) {
        return redirect()->back()->with(['success'=>'Servicio eliminado']);
    }
  }

  public function unassignedRate($idUser, $idRate, $date) {
    $aDate = explode('-', $date);
    if (count($aDate) != 2){
        return redirect()->back()->withErrors(['Periodo inválido']);
    }
    $userRate = UserRates::where('id_user', $idUser)
                    ->where('id_rate', $idRate)
                    ->where('rate_year',$aDate[0])
                    ->where('rate_month',$aDate[1])
                    ->orderBy('created_at', 'DESC')->first();

    if (!$userRate){
        return redirect()->back()->withErrors(['Tarifa no encontrada']);
    }
    if ($userRate->delete()) {
      return redirect()->back()->with('success','Servicio removido para el perdiodo '.$date);
    }
  }

}
