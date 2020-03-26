<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\Rates;
use App\Models\RateTypes;

class RatesController extends Controller {

  public function index() {
    return view('backend.rates.index', [
        'rates' => Rates::where('status', 1)->orderBy('order', 'asc')->orderBy('name', 'asc')->get(),
        'services' => RateTypes::all(),
        'lstCodes' => rates_codes()
    ]);
  }

  public function newRate() {
    return view('backend.rates.new', [
        'taxes' => Rates::all(),
        'typesRate' => \App\RateTypes::all(),
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

    if ($rates->save()) {
      return redirect()->back()->with('success','Tarifa creada.');
    }
    
    return redirect()->back()->with('warning','Tarifa no creada.');
  }

  public function actualizar($id) {

    return view('backend.rates.update', [
        'rate' => Rates::find($id),
        'typesRate' => \App\RateTypes::all(),
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
      return 'ok';
    }
      return 'error';
  }

  public function destroy($id) {
    $rate = Rates::find($id);
    if ($rate->delete()) {
     return redirect()->back()->with('success','Tarifa eliminada.');
    }

    return redirect()->back()->with('warning','Tarifa no eliminada.');
  }

  public function unassignedRate($idUser, $idRate, $date) {
    $month = Carbon::createFromFormat('Y-m-d', $date);
    $userRate = \App\UserRates::where('id_user', $idUser)
                    ->where('id_rate', $idRate)
                    ->whereYear('created_at', '=', $month->copy()->format('Y'))
                    ->whereMonth('created_at', '=', $month->copy()->format('m'))
                    ->orderBy('created_at', 'DESC')->first();

    if ($userRate->delete()) {
      return redirect()->action('UsersController@clientes');
    }
  }

}
