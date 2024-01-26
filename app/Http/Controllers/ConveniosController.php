<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\Convenios;
use App\Models\User;
use App\Models\UserRates;
use App\Models\Rates;
use App\Models\TypesRate;
use \App\Traits\ClientesTraits;

class ConveniosController extends Controller
{

  use ClientesTraits;

  public function index()
  {
    $lstObjs = Convenios::all();
    $year = getYearActive();

    $lstRates = Rates::orderBy('name', 'asc')->pluck('type', 'id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    $lstRateTypes['-1'] = 'Otros';
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $aMonth = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
    $tConvenio = [];
    $convLstRates = [];
    $totals = $aMonth;
    foreach ($lstObjs as $item) {
      $crLst = [];
      $tConvenio[$item->id] = $aMonth;
      $convLstRates[$item->id] = null;
      $uConvenios = User::where('convenio', $item->id)->get()->pluck('id');
      if ($uConvenios) {
        $uRates = UserRates::where('rate_year', $year)->whereIn('id_user', $uConvenios)->get();
        foreach ($uRates as $ur) {
          $rt_id = array_key_exists($ur->id_rate, $lstRates) ? $lstRates[$ur->id_rate] : -1;
          $c = $ur->charged;
          if (!isset($crLst[$rt_id]))  $crLst[$rt_id] = $aMonth;
          $m = $ur->rate_month;
          $p = ($c) ? ($c) : $ur->price;

          $crLst[$rt_id][$m] += $p;
          $totals[$m] += $p;
          $tConvenio[$item->id][$m] += $p;
        }
        $convLstRates[$item->id] = $crLst;
      }
    }


    return view('/convenios/index', [
      'year' => $year,
      'lstObjs' => $lstObjs,
      'convLstRates' => $convLstRates,
      'totals' => $totals,
      'tConvenio' => $tConvenio,
      'lstMonths' => $lstMonths,
      'lstRates' => $lstRates,
      'lstRateTypes' => $lstRateTypes,
    ]);
  }

  public function newItem(Request $request)
  {
    if($request->input('comision') > 100 || $request->input('comision') < 0){
      return redirect()->back()->with(['error' => 'Comisión inválida']);
    }
    $oObj = new Convenios();
    $oObj->name = $request->input('name');
    $oObj->comision_porcentaje = $request->input('comision') * 100;
    $oObj->save();
    return redirect()->back()->with(['success' => 'Convenio agregado']);
  }

  public function update(Request $request)
  {

    $id = $request->input('id');
    $oObj = Convenios::find($id);
    $oObj->name = $request->input('name');

    if ($oObj->save()) {
      die('OK');
    }
    die('error');
  }

  public function delete(Request $request)
  {
    $id = $request->input('convenio');
    $oObj = Convenios::find($id);
    if (!$oObj) return redirect()->back()->withErrors(['Convenio no encontrado']);
    if ($oObj->delete()) {
      return redirect()->back()->with(['success' => 'Convenio eliminado']);
    }
    return redirect()->back()->withErrors(['Convenio no encontrado']);
  }

  ///////////////////////////////////////////////////////////////////////////


  /*public function informeConveniosOld(Request $request, $month = null, $convenio_id = null, $rateID=null)
  {

    $year = getYearActive();
    if (!$month)
      $month = date('m');

    $lstObjs = Convenios::all();
    $urlPubl = null;
    $oConveniosId = [];

    if($convenio_id && $convenio_id != 'all'){
      $auxConv = Convenios::where('id',$convenio_id)->first();
      if (!$auxConv->token){
        $auxConv->token = str_random(150);
        $auxConv->save();
      }
      $urlPubl = \URL::to('/informes-convenio/'.$year.'/'.$auxConv->token);
      $oConvenios[] = $auxConv;
    } 
    else $oConvenios = $lstObjs;

    foreach($lstObjs as $conv) {
      $oConveniosId[$conv->id] = $conv;
    }
   
    $convenioNames = [];
    $year = getYearActive();

    $lstRates = Rates::orderBy('name', 'asc')->pluck('type', 'id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    $lstRateTypes['-1'] = 'Otros';
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $convLstUsers = [];
    $totals = 0;
    $totalsComision = 0;
    $lstRatesNames = $lstUsers = [];
    foreach ($oConvenios as $item) {
      $convLstRates[$item->id] = null;
      $uConvenios = User::where('convenio', $item->id)->get();
      $convenioNames[$item->id] = $item->name;
      if ($uConvenios) {
        foreach ($uConvenios as $uc) {
          $convLstUsers[$uc->id] = ['name' => $uc->name, 'cID' => $uc->convenio, 'rates' => []];
          $uRates = UserRates::where('rate_year', $year)->where('rate_month', $month)->where('id_user', $uc->id)->get();
          foreach ($uRates as $ur) {
            $rt_id = array_key_exists($ur->id_rate, $lstRates) ? $lstRates[$ur->id_rate] : -1;
            
            if(!$rateID || $rateID == $rt_id){
              $c = $ur->charged;
              $p = ($c) ? ($c) : $ur->price;
              
              $convLstUsers[$uc->id]['rates'][] = ['price' => $p, 'date' => $ur->created_at, 'rGroup' => (isset($lstRateTypes[$rt_id]) ? $rt_id : -1 ), 'rateID' => $ur->id_rate];
              $totals += $p;
              if($item->comision_porcentaje){
                $totalsComision += $p * $item->comision_porcentaje / 10000;
              }
              
              $lstUsers[$uc->id] = $convLstUsers[$uc->id];
            }
            if(array_key_exists($rt_id,$lstRateTypes))  $lstRatesNames[$rt_id] = $lstRateTypes[$rt_id];
              else $lstRatesNames[-1] = 'Otros';
          }
        }
        $lstRateTypes = $lstRatesNames;//Aca se esta pisando el arreglo de nombres haciendo que se resuelvan nombres de rates_type incorrectos
        $convLstUsers = $lstUsers;
      }
    }
    
    return view('/convenios/informeMes', [
      'year' => $year,
      'month' => $month,
      'lstObjs' => $lstObjs,
      'convenio' => $convenio_id,
      'oConveniosId' => $oConveniosId,
      'urlPubl' => $urlPubl,
      'convenioNames' => $convenioNames,
      'totals' => $totals,
      'totalsComision' => $totalsComision,
      'lstMonths' => $lstMonths,
      'lstRates' => $lstRates,
      'lstRateTypes' => $lstRateTypes,
      'convLstUsers' => $convLstUsers,
      'rateID' => $rateID,
    ]);
  }*/

  public function informeConvenios(Request $request, $month = null, $convenio_id = null, $rateTypeID=null)
  {

    $year = getYearActive();
    if (!$month)
      $month = date('m');

    $lstObjs = Convenios::all();
    $urlPubl = null;
    $oConveniosId = [];

    if($convenio_id && $convenio_id != 'all'){
      $auxConv = Convenios::where('id',$convenio_id)->first();
      if (!$auxConv->token){
        $auxConv->token = str_random(150);
        $auxConv->save();
      }
      $urlPubl = \URL::to('/informes-convenio/'.$year.'/'.$auxConv->token);
      $oConvenios[] = $auxConv;
    } 
    else $oConvenios = $lstObjs;

    foreach($lstObjs as $conv) {
      $oConveniosId[$conv->id] = $conv;
    }
   
    $year = getYearActive();

    $lstRates = Rates::orderBy('name', 'asc')->pluck('type', 'id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $totals = 0;
    $totalsComision = 0;

    $uRatesBuilder = UserRates::leftJoin('users', 'users.id', '=', 'users_rates.id_user')
                      ->leftJoin('appointment', 'users_rates.id', '=', 'appointment.id_user_rates')
                      ->leftJoin('rates', 'users_rates.id_rate', '=', 'rates.id')
                      ->select(
                        'users_rates.*', 
                        'appointment.date', 
                        'users.convenio', 
                        'users.name', 
                        'rates.type as type_rate'
                      )
                      ->where('users_rates.rate_year', $year)
                      ->where('users_rates.rate_month', $month)
                      ->where('users.convenio', '>', 0)
                      ->whereNotNull('users.convenio');
           
    if(isset($rateTypeID)) {
      $uRatesBuilder->where('rates.type', $rateTypeID);
    }

    if(isset($convenio_id) && $convenio_id != 'all') {
      $uRatesBuilder->where('users.convenio', $convenio_id);
    }
                      
    $uRates = $uRatesBuilder->orderBy('appointment.date','desc')->get();
    
    if($uRates->isNotEmpty()){
      foreach($uRates as $rate){
        $priceAux = $rate->charged ? $rate->charged : $rate->price;
        $totals += $priceAux;
        if(
          $rate->convenio && 
          isset($oConveniosId[$rate->convenio]) && 
          isset($oConveniosId[$rate->convenio]->comision_porcentaje)
        ){
          $totalsComision += $priceAux *  $oConveniosId[$rate->convenio]->comision_porcentaje / 10000;
        }
        
      }
    }

    
    return view('/convenios/informeMes', [
      'year' => $year,
      'month' => $month,
      'lstObjs' => $lstObjs,
      'convenio' => $convenio_id,
      'oConveniosId' => $oConveniosId,
      'urlPubl' => $urlPubl,
      'totals' => $totals,
      'totalsComision' => $totalsComision,
      'lstMonths' => $lstMonths,
      'lstRates' => $lstRates,
      'lstRateTypes' => $lstRateTypes,
      'convLstUsers' => $uRates,
      'rateTypeID' => $rateTypeID,
    ]);
  }

  /*public function informeConveniosPublicOld(Request $request,$year, $toke, $month = null, $rateID=null)
  {

    if (!$month)
      $month = date('m');

    $oConvenio = Convenios::where('token',$toke)->first();
    if(!$oConvenio) {
      abort(404);
      exit();
    }
    $lstRates = Rates::orderBy('name', 'asc')->pluck('type', 'id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    $lstRateTypes['-1'] = 'Otros';
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $convLstUsers = [];
    $totals = 0;
    $uConvenios = User::where('convenio', $oConvenio->id)->get();
    $lstRatesNames = $lstUsers = [];
    if ($uConvenios) {
      foreach ($uConvenios as $uc) {
        $convLstUsers[$uc->id] = ['name' => $uc->name, 'cID' => $uc->convenio, 'rates' => []];
        $uRates = UserRates::where('rate_year', $year)->where('rate_month', $month)->where('id_user', $uc->id)->get();
        foreach ($uRates as $ur) {
          $rt_id = array_key_exists($ur->id_rate, $lstRates) ? $lstRates[$ur->id_rate] : -1;
          if(!$rateID || $rateID == $rt_id){
            $c = $ur->charged;
            $p = ($c) ? ($c) : $ur->price;
            $convLstUsers[$uc->id]['rates'][] = ['price' => $p, 'date' => $ur->created_at, 'rGroup' => $rt_id, 'rateID' => $ur->id_rate];
            $totals += $p;
            $lstUsers[$uc->id] = $convLstUsers[$uc->id];
          }
          if(array_key_exists($rt_id,$lstRateTypes))  $lstRatesNames[$rt_id] = $lstRateTypes[$rt_id];
            else $lstRatesNames[-1] = 'Otros';
        }
      }
    }
    $lstRateTypes = $lstRatesNames;
    $convLstUsers = $lstUsers;
    return view('/convenios/informeMesPublic', [
      'year' => $year,
      'month' => $month,
      'oConvenio' => $oConvenio,
      'totals' => $totals,
      'lstMonths' => $lstMonths,
      'lstRates' => $lstRates,
      'rateID' => $rateID,
      'lstRateTypes' => $lstRateTypes,
      'convLstUsers' => $convLstUsers,
    ]);
  }*/

  public function informeConveniosPublic(Request $request,$year, $toke, $month = null, $rateTypeID=null)
  {

    if (!$month)
      $month = date('m');

    $oConvenio = Convenios::where('token',$toke)->first();
    if(!$oConvenio) {
      abort(404);
      exit();
    }
    $lstRates = Rates::orderBy('name', 'asc')->pluck('type', 'id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    $lstRateTypes['-1'] = 'Otros';
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $totals = 0;
    
    $uRatesBuilder = UserRates::leftJoin('users', 'users.id', '=', 'users_rates.id_user')
                      ->leftJoin('appointment', 'users_rates.id', '=', 'appointment.id_user_rates')
                      ->leftJoin('rates', 'users_rates.id_rate', '=', 'rates.id')
                      ->select(
                        'users_rates.*', 
                        'appointment.date', 
                        'users.convenio', 
                        'users.name', 
                        'rates.type as type_rate'
                      )
                      ->where('users_rates.rate_year', $year)
                      ->where('users_rates.rate_month', $month)
                      ->where('users.convenio', $oConvenio->id)
                      ->where('users.convenio', '>', 0)
                      ->whereNotNull('users.convenio');
           
    if(isset($rateTypeID)) {
      $uRatesBuilder->where('rates.type', $rateTypeID);
    }
                      
    $uRates = $uRatesBuilder->orderBy('appointment.date','desc')->get();
    
    if($uRates->isNotEmpty()){
      foreach($uRates as $rate){
        $priceAux = $rate->charged ? $rate->charged : $rate->price;
        $totals += $priceAux;        
      }
    }

    return view('/convenios/informeMesPublic', [
      'year' => $year,
      'month' => $month,
      'oConvenio' => $oConvenio,
      'totals' => $totals,
      'lstMonths' => $lstMonths,
      'lstRates' => $lstRates,
      'rateTypeID' => $rateTypeID,
      'lstRateTypes' => $lstRateTypes,
      'convLstUsers' => $uRates,
    ]);
  }

  public function actualizarConvenioUsuario(Request $request) {

    try {
      if(!$request->usuario || !$request->convenio){
        throw new \Exception('Faltan datos');
      }

      $usuario = User::find($request->usuario);

      if(!$usuario->id) {
        throw new \Exception('Usuario no encontrado');
      }

      $usuario->convenio = $request->convenio;
      if ($usuario->save()){
        return 'OK';
      } else {
        throw new \Exception('Error al actualizar usuario');
      }
    } catch (\Exception $e) {
      return $e->getMessage();
    }

  }

  public function updateConvenio(Request $request) {
    try {
      if(!$request->id || !$request->comision || !$request->name){
        throw new \Exception('Faltan datos');
      }

      if($request->comision > 100 || $request->comision < 0){
        throw new \Exception('Comisión inválida');
      }

      $convenio = Convenios::find($request->id);

      if(!$convenio->id) {
        throw new \Exception('Convenio no encontrado');
      }

      $convenio->name = $request->input('name');
      $convenio->comision_porcentaje = $request->input('comision') * 100;

      if ($convenio->save()){
        return redirect('admin/convenios/listado')->with(['success' => 'Convenio actualizado']);
      } else {
        throw new \Exception('No se pudo actualizar convenio');
      }
    } catch (\Exception $e) {
      return redirect('admin/convenios/listado')->with(['error' => $e->getMessage()]);
    }
  }
 
}
