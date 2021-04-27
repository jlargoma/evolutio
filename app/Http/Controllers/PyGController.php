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
    
    for ($i = 2; $i > 0; $i--) {
      $yAux = $year - $i;
      $incomesYear[$yAux] = Charges::whereYear('date_payment', '=', $yAux)->sum('import');
    }
    
    $oCharges = Charges::whereYear('date_payment', '=', $year)->get();
    $aux = $months_empty;
    $incomesYear[$year] = 0;
    foreach ($oCharges as $c) {
      $m = intval(substr($c->date_payment, 5, 2));
      $aux[$m] += $c->import;
      $incomesYear[$year] += $c->import;
      
      $rateGr = isset($aRates[$c->id_rate]) ? $aRates[$c->id_rate] : 3;
      $crLst[$rateGr][$m] += $c->import;
    }
    $currentY['Ingresos'] = $aux;


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
        'aux_e'=>$aux_e
  ]);
  }

}
