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
    $familyTotal['gral'] = $mm;
    $family['gral'] = 'Generales';
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
    $aBonos = \App\Models\Bonos::orderBy('name')->get();
    $aux_lst  = [];
    foreach ($aBonos as $b){
      $aux_lst[$b->id] = $mm;
      $aux_lst[$b->id]['name'] = $b->name;
      $aux_lst[$b->id]['lst']  = [];
    }
    //-------------------------------------------
    $oBonos = Charges::whereYear('date_payment', '=', $year)
              ->where('bono_id','>',0)->get();
    
    foreach ($oBonos as $item){
      $m = intval(substr($item->date_payment,5,7));
      if (!isset($aux_lst[$item->bono_id][$m])) $aux_lst[$item->bono_id][$m] = 0;
      $aux_lst[$item->bono_id][$m] += $item->import;
    }
    //-------------------------------------------
    
    $auxB = $mm;
    $auxB['name'] = 'BONOS';
    $auxB['lst']  = [];
    $auxB['slst']  = [];
    $lst['bonos'] = $auxB;
   
    //rate_type or rate_subf
    foreach ($aBonos as $k=>$v){
      $rateType = $rate_subf = 'gral';
      if (isset($lst[$v->rate_type])){
        $rateType = $v->rate_type;
      } else {
        if ($v->rate_subf){
          $rate_subf = $v->rate_subf;
          if(str_contains($v->rate_subf,'f'))
                  $rateType = 8;
          if(str_contains($v->rate_subf,'v'))
                  $rateType = 11;
          
        } else {
          //Es del item Bonos cuando no se le puede asignar a otro
          $lst['bonos']['lst'][$v->id] = $aux_lst[$v->id];
          $lst['bonos']['lst'][$v->id]['name'] = $v->name;
        }
        
      }
          
      if ($rateType && isset($lst[$rateType])){
        $lst[$rateType]['slst'][$rate_subf][$v->id] = $aux_lst[$v->id];
        $lst[$rateType]['slst'][$rate_subf][$v->id]['name'] = '*'.$v->name;
        //Actualizo el valor header del tipo de servicio
        foreach ($aux_lst[$v->id] as $b_mont => $b_val){
          if (is_numeric($b_mont)){
            $lst[$rateType][$b_mont] += $b_val;
            if (isset($familyTotal[$rate_subf]))
              $familyTotal[$rate_subf][$b_mont] += $b_val;
          }
        }
      }
    }
    foreach ($lst['bonos']['lst'] as $id => $bono){
      foreach ($bono as $b_mont => $b_val){
        if (is_numeric($b_mont)){
          $lst['bonos'][$b_mont] += $b_val;
        }
      }
    }
    //----------------------------------//
    //INGRESOS TOTAL ANUAL
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
