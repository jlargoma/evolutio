<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;

class IncomesController extends Controller {

  public function index($year = "") {
    $year = getYearActive();
    $monts = lstMonthsSpanish();
    unset($monts[0]);
    $mm = [];
    $crLst = [];
    foreach ($monts as $k=>$v) $mm[$k] = 0;
    
    
    
    /************************************************/
    $family = \App\Models\TypesRate::subfamily();
    $familyTotal = [];
    foreach ($family as $k=>$v) $familyTotal[$k] = $mm;
    /************************************************/
    $oCharges = \App\Models\Charges::whereYear('date_payment','=',$year)->get();
    foreach ($oCharges as $c){
      $rate = $c->id_rate;
      if (!isset($crLst[$rate])) $crLst[$rate] = $mm;
      $m = intval(substr($c->date_payment,5,2));
      $crLst[$rate][$m] += $c->import;
    }
    
    /************************************************/
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
    /************************************************/
    return view('admin.contabilidad.incomes.index',[
        'year'=>$year,
        'monts'=>$monts,
        'lst'=>$lst,
        'family'=>$family,
        'familyTotal'=>$familyTotal
    ]);
  }

}
