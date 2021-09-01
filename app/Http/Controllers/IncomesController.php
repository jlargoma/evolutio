<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;
use App\Models\Charges;

class IncomesController extends Controller {

  public function index($year = "") {
    $year = getYearActive();
    $monts = lstMonthsSpanish();
    unset($monts[0]);
    $mm = [];
    $crLst = [];
    foreach ($monts as $k=>$v) $mm[$k] = 0;
    
    
    
    //----------------------------------//
    $family = \App\Models\TypesRate::subfamily();
    $familyTotal = [];
    foreach ($family as $k=>$v) $familyTotal[$k] = $mm;
    //----------------------------------//
    $uRates = \App\Models\UserRates::where('id_charges', '>', 0)
              ->where('rate_year',$year)->get();
    foreach ($uRates as $item){
      $c = $item->charges;
      if (!$c)        continue;
      $rate = $c->id_rate;
      if (!isset($crLst[$rate])) $crLst[$rate] = $mm;
      $m = $item->rate_month;
      $crLst[$rate][$m] += $c->import;
    }
    
    //----------------------------------//
    $oRateTypes = \App\Models\TypesRate::orderBy('name')->get();
    $lst = [];
    foreach ($oRateTypes as $t){
      $lst[$t->id] = $mm;
      $lst[$t->id]['name'] = $t->name;
      $lst[$t->id]['slst']  = [];
      $lst[$t->id]['lst']  = [];
     
    }
    $oRates = \App\Models\Rates::orderBy('subfamily')->orderBy('name')->get();
    foreach ($oRates as $r){
      if (!isset($lst[$r->type])) continue;
      /*******************************/
      if (isset($crLst[$r->id])){
        $rData =  $crLst[$r->id];
        foreach ($rData as $k=>$v){
          $lst[$r->type][$k] += $v;
        }
      } else $rData = $mm;
      $rData['name']='';
      /*******************************/
      
      if ($r->subfamily){
        if (!isset($lst[$r->type]['slst'][$r->subfamily])) {
          $lst[$r->type]['slst'][$r->subfamily] = [];
          //check subfamily
          if (!isset($family[$r->subfamily])) $family[$r->subfamily] = $r->subfamily;
        }
        
        $lst[$r->type]['slst'][$r->subfamily][$r->id] = $rData;
        $lst[$r->type]['slst'][$r->subfamily][$r->id]['name'] = $r->name;
//        $lst[$r->type]['slst'][$r->subfamily][] = $r->name;
        
        foreach ($rData as $k=>$v){
          if ($k != 'name')
          $familyTotal[$r->subfamily][$k] += $v;
        }
        
      } else {
        $lst[$r->type]['lst'][$r->id] = $rData;
        $lst[$r->type]['lst'][$r->id]['name'] = $r->name;
      }
    }
    //----------------------------------//
    //BONOS
    $oBonos = Charges::whereYear('date_payment', '=', $year)
              ->where('bono_id','>',0)->get();
    
    $auxB = $mm;
    $auxB['name'] = 'BONOS';
    $auxB['slst']  = [];
    $aux_lst  = [];
    $aBonos = \App\Models\Bonos::pluck('name','id');
    foreach ($aBonos as $k=>$v){
      $aux_lst[$k] = $mm;
      $aux_lst[$k]['name'] = $v;
    }
    foreach ($oBonos as $item){
      $m = intval(substr($item->date_payment,5,7));
      $auxB[$m] += $item->import;
      if (!isset($aux_lst[$item->bono_id][$m])) $aux_lst[$item->bono_id][$m] = 0;
      $aux_lst[$item->bono_id][$m] += $item->import;
    }
    $auxB['lst'] = $aux_lst;
    $lst['bonos'] = $auxB;
    //----------------------------------//
    
    $byYears = $tByYears = [];
    for($i=2;$i>=0;$i--){
      $yAux = $year-$i;
      $byYears[$yAux] = $mm;
      $tByYears[$yAux] = 0;
      $oCharges = Charges::whereYear('date_payment','=',$yAux)->get();
      foreach ($oCharges as $c){
        $m = intval(substr($c->date_payment,5,2));
        $byYears[$yAux][$m] += $c->import;
        $tByYears[$yAux] += $c->import;
      }
    }
    //----------------------------------//
    $totals = $mm;
    foreach ($lst as $i){
      foreach ($mm as $k=>$v){
        if (isset($i[$k])){
          $totals[$k] += $i[$k];
        }
      }
    }
    //----------------------------------//
    return view('admin.contabilidad.incomes.index',[
        'year'=>$year,
        'monts'=>$monts,
        'lst'=>$lst,
        'family'=>$family,
        'familyTotal'=>$familyTotal,
        'totals'=>$totals,
        'byYears'=>$byYears,
        'tByYears'=>$tByYears,
    ]);
  }

  function byRate($rateID){
    $year = getYearActive();
    $pMeth = payMethod();
    
    if ($rateID == 'bono'){
      $oBonoCharges = Charges::whereYear('date_payment', '=', $year)
              ->where('bono_id','>',0)->get();
      $aBonos = \App\Models\Bonos::pluck('name','id')->toArray();
      
      include_once app_path().'/Blocks/BonosDetail.php';
      return '';
    }
    
    $oType = \App\Models\TypesRate::find($rateID);
    $servic = \App\Models\Rates::getByTypeRateID($rateID)
            ->pluck('name', 'id')->toArray();
    
    $oCharges = null;
    if (count($servic)>0){
      $oCharges = Charges::whereYear('date_payment', '=', $year)
            ->whereIn('id_rate', array_keys($servic))
            ->orderBy('date_payment')->get();
    }
     
    
    include_once app_path().'/Blocks/IncomesDetail.php';

  }
}
