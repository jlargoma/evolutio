<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;
use App\Models\Expenses;
use App\Models\Charges;

class PyGController extends Controller {

  public function index() {
    /*     * ********************************************* */
    $year = getYearActive();
    $lstMonths = lstMonthsSpanish(false);
    unset($lstMonths[0]);
    $months_empty = array();
    for ($i = 0; $i < 13; $i++)  $months_empty[$i] = 0;

    
    /*     * ********************************************************* */
    $gastos = Expenses::whereYear('date', '=', $year)->get();
    $gType = Expenses::getTypes();
    $gTypeGroup = Expenses::getTypesGroup();
    $gTypeGroup_g = $gTypeGroup['groups'];
    $ggMonth = [];
    $crLst = [];
    foreach ($gTypeGroup_g as $k=>$v) $ggMonth[$v] = $months_empty;
    $ggMonth['otros'] = $months_empty;
    /*     * ********************************************************* */
    
        
    $oRateTypes = \App\Models\TypesRate::orderBy('name')->pluck('name','id')->toArray();
    $aRates = \App\Models\Rates::orderBy('name')->pluck('type','id')->toArray();
    foreach ($oRateTypes as $k=>$v) $crLst[$k] = $months_empty;

    /*     * ********************************************************* */
    $incomesYear = $expensesYear = [];
    $currentY = [];

    /*     * ********************************************************* */
    
    for ($i = 2; $i >= 0; $i--) {
      $yAux = $year - $i;
      $incomesYear[$yAux] = Charges::whereYear('date_payment', '=', $yAux)->sum('import');
    }
    
    //----------------------------------------------------------//
    $uRates = \App\Models\UserRates::where('id_charges', '>', 0)
              ->where('rate_year',$year)->get();
      
    $aux = $months_empty;
    $pay_method = ['c'=>$months_empty,'b'=>$months_empty];
    foreach ($uRates as $item){
//    $oCharges = Charges::whereYear('date_payment', '=', $year)->get();
//    foreach ($oCharges as $c) {
      $c = $item->charges;
      if (!$c)        continue;
//      $m = intval(substr($c->date_payment, 5, 2));
      $m = $item->rate_month;
      $aux[0] += $c->import;
      $aux[$m] += $c->import;
      if ($c->type_payment == 'cash'){
        $pay_method['c'][0] += $c->import;
        $pay_method['c'][$m] += $c->import;
      } else {
        $pay_method['b'][0] += $c->import;
        $pay_method['b'][$m] += $c->import;
      }
      $rateGr = isset($aRates[$c->id_rate]) ? $aRates[$c->id_rate] : 3;
      $crLst[$rateGr][$m] += $c->import;
    }
    $currentY['Ingresos'] = $aux;
    
    $tIncomes = 0;
    foreach ($crLst as $k=>$v){
        $t=0;
        foreach ($v as $k1=>$v1){
          if (is_numeric($k1)){
            $t += $v1;
          }
        }
        $crLst[$k][0] = $t;
        $tIncomes += $t;
    }


    /********************************************************** */
    for ($i = 2; $i > 0; $i--) {
      $yAux = $year - $i;
      $expensesYear[$yAux] = Expenses::whereYear('date', '=', $yAux)->sum('import');
    }
    
    $oExpenses = Expenses::whereYear('date', '=', $year)->get();
    $aux = $months_empty;
    $expensesYear[$year] = 0;
    if ($oExpenses) {
      foreach ($oExpenses as $e) {
        $m = intval(substr($e->date, 5, 2));
        $aux[$m] += $e->import;
        $aux[0] += $e->import;
        $expensesYear[$year] += $e->import;
        $g = $gTypeGroup_g[$e->type];
        if (isset($ggMonth[$g])){
          $ggMonth[$g][$m] += $e->import;
        } else {
          $ggMonth['otros'][$m] += $e->import;
        }
      }
    }
    $currentY['Gastos'] = $aux;
    /***************************************/
    $subscs = \App\Models\UsersSuscriptions::count();
    $uActivs = \App\Models\User::where('status',1)->count();
    /***************************************/
        
    $aux_i = $aux_e = $months_empty; 
    /***************************************/
    return view('admin.contabilidad.pyg.index',[
        'year'=>$year,
        'monts'=>$lstMonths,
        'currentY'=>$currentY,
        'incomesYear'=>$incomesYear,
        'expensesYear'=>$expensesYear,
        'subscs'=>$subscs,
        'uActivs'=>$uActivs,
        'ggMonth'=>$ggMonth,
        'ggNames'=>$gTypeGroup['names'],
        'oRateTypes'=>$oRateTypes,
        'crLst'=>$crLst,
        'aux_i'=>$aux_i,
        'aux_e'=>$aux_e,
        'tIncomes'=>$tIncomes,
        'pay_method'=>$pay_method
  ]);
  }

}
