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
    
    
    
    /************************************************/
    $family = \App\Models\TypesRate::subfamily();
    $familyTotal = [];
    foreach ($family as $k=>$v) $familyTotal[$k] = $mm;
    /************************************************/
    $uRates = \App\Models\UserRates::where('id_charges', '>', 0)
              ->where('rate_year',$year)->get();
    foreach ($uRates as $item){
      $c = $item->charges;
      if (!$c)        continue;
//    $oCharges = Charges::whereYear('date_payment','=',$year)->get();
//    foreach ($oCharges as $c){
      $rate = $c->id_rate;
      if (!isset($crLst[$rate])) $crLst[$rate] = $mm;
//      $m = intval(substr($c->date_payment,5,2));
      $m = $item->rate_month;
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
    /************************************************/
    $totals = $mm;
    foreach ($lst as $i){
      foreach ($mm as $k=>$v){
        if (isset($i[$k])){
          $totals[$k] += $i[$k];
        }
      }
    }
    /************************************************/
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
    $oType = \App\Models\TypesRate::find($rateID);
    $servic = \App\Models\Rates::getByTypeRateID($rateID)
            ->pluck('name', 'id')->toArray();
    
    ?>
<h2>Registors de <?php echo $oType->name;?></h2>
<?php
    if (count($servic)== 0){
      echo  '<p class="alert alert-warning">Sin Registros</p>';
      return '';
    }
    $oCharges = Charges::whereYear('date_payment', '=', $year)
            ->whereIn('id_rate', array_keys($servic))
            ->orderBy('date_payment')->get();
     
    if (count($oCharges)== 0){
      echo  '<p class="alert alert-warning">Sin Registros</p>';
      return '';
    }
    
    $pMeth = payMethod();
    
 ?>

<table class="table">
  <tr>
    <th>Fecha</th>
    <th>Servicio</th>
    <th>Monto</th>
    <th>Met. Pago</th>
  </tr>
    <?php
    foreach ($oCharges as $c){
      ?>
<tr>
  <td><?php echo dateMin($c->date_payment);?></td>
  <td><?php echo $servic[$c->id_rate];?></td>
  <td><?php echo moneda($c->import);?></td>
  <td><?php echo isset($pMeth[$c->type_payment]) ? $pMeth[$c->type_payment] : 'Otro';?></td>
</tr>
      <?php
    }
     ?>
</table>
    <?php
  }
}
