<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\Convenios;
use App\Models\User;
use App\Models\UserRates;
use App\Models\Rates;

class ConveniosController extends Controller {

  
  
  public function index() {
    $lstObjs = Convenios::all();
    $year = getYearActive();

    $lstRates = Rates::orderBy('name', 'asc')->pluck('type','id')->toArray();
    $lstRateTypes = \App\Models\TypesRate::orderBy('name', 'asc')->pluck('name','id')->toArray();
    $lstRateTypes['-1'] = 'Otros';
    $lstMonths = lstMonthsSpanish();
    unset($lstMonths[0]);
    $aMonth = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
    $tConvenio = [];
    $convLstRates = [];
    foreach($lstObjs as $item){
      $crLst = [];
      $totals = $aMonth;
      $tConvenio[$item->id] = $aMonth;
      $convLstRates[$item->id] = null;
      $uConvenios = User::where('convenio',$item->id)->get()->pluck('id');
      if($uConvenios){
        $uRates = UserRates::where('rate_year', $year)->whereIn('id_user',$uConvenios)->get();
        foreach ($uRates as $ur) {
          $rt_id = array_key_exists($ur->id_rate,$lstRates) ? $lstRates[$ur->id_rate] : -1;
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
  
  public function newItem(Request $request) {
    $oObj = new Convenios();
    $oObj->name = $request->input('name');
    $oObj->save();
    return redirect()->back()->with(['success'=>'Convenio agregado']);
  }

  public function update(Request $request) {

    $id = $request->input('id');
    $oObj = Convenios::find($id);
    $oObj->name = $request->input('name');
   
    if ($oObj->save()) {
      die('OK');
    }
    die('error');
  }

  public function delete(Request $request) {
    $id = $request->input('convenio');
    $oObj = Convenios::find($id);
    if(!$oObj) return redirect()->back()->withErrors(['Convenio no encontrado']);
    if ($oObj->delete()) {
        return redirect()->back()->with(['success'=>'Convenio eliminado']);
    }  
    return redirect()->back()->withErrors(['Convenio no encontrado']);
  }
  
  ///////////////////////////////////////////////////////////////////////////
  
 
}
