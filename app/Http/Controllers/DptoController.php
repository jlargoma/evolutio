<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Bonos;
use \Carbon\Carbon;
use DB;
use App\Models\Expenses;
use App\Models\Charges;
use App\Services\CoachLiqService;
use App\Models\User;
use App\Models\UserRates;
use App\Models\DistribBenef;

class DptoController extends Controller {

  private function getDepto(){
    $expensesLst = [];
    $coachRole = '-';
    $rType = [];
    $bIDs = [];
    $rIDs = [];

    $uID = Auth()->id();
    if ($uID == 2844 || $uID == 2801){ //estetica
      $expensesLst = ['gto_mat_esthetic','renting_estetica'];
      $coachRole = 'esthetic';
      $rType = [12];

      $rIDs = \App\Models\Rates::whereIn('type',$rType)->orderBy('name')->pluck('id'); 
      $bIDs = Bonos::whereIn('rate_type',$rType)->orWhereIn('rate_id',$rIDs)->orWhere('rate_subf', 'LIKE', "%e%")->pluck('id');
    }
    return [ $expensesLst,$coachRole,$rType,$rIDs,$bIDs];
  }

  public function perdidas_ganacias() {
    //---------------------------------------------------------//
    $year = getYearActive();
    $lstMonths = lstMonthsSpanish(false);
    unset($lstMonths[0]);
    $months_empty = array();
    for ($i = 0; $i < 13; $i++)
      $months_empty[$i] = 0;


    //---------------------------------------------------------//
    $dpto = $this->getDepto();
    $expensesLst = $dpto[0];
    $coachRole = $dpto[1];
    $rType = $dpto[2];
    $rIDs = $dpto[3];
    $bIDs = $dpto[4];
    //---------------------------------------------------------//

    $gType = Expenses::getTypes();
    $gTypeGroup = Expenses::getTypesGroup();
    $gTypeGroup_g = $gTypeGroup['groups'];

    foreach ($gTypeGroup_g as $k => $v){
      if (!in_array($k,$expensesLst)){
        unset($gTypeGroup_g[$k]);
      }
    }
    foreach ($gTypeGroup['names'] as $k => $v){
      if (!in_array($k,$gTypeGroup_g)){
        unset($gTypeGroup['names'][$k]);
      }
    }
    $gTypeGroup['names']['otros'] = 'RESTO DE GASTOS';


    $ggMonth = [];
    $crLst = [];
    foreach ($gTypeGroup_g as $k => $v)
      $ggMonth[$v] = $months_empty;
    $ggMonth['otros'] = $months_empty;
    //---------------------------------------------------------//
    $oRateTypes = \App\Models\TypesRate::whereIn('id',$rType)->orderBy('name')->pluck('name', 'id')->toArray();
    $aRates = \App\Models\Rates::whereIn('id',$rIDs)->orderBy('name')->pluck('type', 'id')->toArray(); 
    foreach ($oRateTypes as $k => $v){
      $crLst[$k] = $months_empty;
    } 
    //---------------------------------------------------------//
    $incomesYear = $expensesYear = [];
    $currentY = [];
    //---------------------------------------------------------//
    for ($i = 2; $i >= 0; $i--) {
      $yAux = $year - $i;
//      $incomesYear[$yAux] = Charges::getSumYear($yAux);
      $incomesYear[$yAux] = UserRates::getSumYear($yAux,$rIDs,$bIDs);
    }
    //----------------------------------------------------------//
    $uRates = UserRates::whereIn('id_rate',$rIDs)->where('rate_year', $year)->get();

    $aux = $months_empty;
    $pay_method = ['c' => $months_empty, 'b' => $months_empty, 'v' => $months_empty, 'np' => $months_empty];
    $tPay = 0;
    foreach ($uRates as $item) {
      $c = $item->charges;
      $m = $item->rate_month;
      if ($c) {
        switch ($c->type_payment) {
          case 'cash':
            $pay_method['c'][$m] += $c->import;
            break;
          case 'card':
            $pay_method['v'][$m] += $c->import;
            break;
          case 'banco':
            $pay_method['b'][$m] += $c->import;
            break;
        }
        $rateGr = isset($aRates[$c->id_rate]) ? $aRates[$c->id_rate] : 3;
        $crLst[$rateGr][$m] += $c->import;
        $tPay += $c->import;
      } else {
        $rateGr = isset($aRates[$item->id_rate]) ? $aRates[$item->id_rate] : 3;
        $crLst[$rateGr][$m] += $item->price;
        $pay_method['np'][$m] += $item->price;
      }
    }
    //--------------------------------------------------------------------//
    $lstBonos = \App\Models\Bonos::listBonos();
    //--------------------------------------------------------------------//
    $oBonos = Charges::whereYear('date_payment', '=', $year)->whereIn('bono_id',$bIDs)->get();
    $oRateTypes['bono'] = 'BONOS SUELTOS';
    $crLst['bono'] = $months_empty;
    foreach ($oBonos as $c) {
      $m = intval(substr($c->date_payment, 5, 7));
      $rateType = isset($lstBonos[$c->bono_id]) ? $lstBonos[$c->bono_id] : null;

      if (isset($crLst[$rateType])) {
        $crLst[$rateType][$m] += $c->import;
      } else {
        $crLst['bono'][$m] += $c->import;
      }

      switch ($c->type_payment) {
        case 'cash':
          $pay_method['c'][$m] += $c->import;
          break;
        case 'card':
          $pay_method['v'][$m] += $c->import;
          break;
        case 'banco':
          $pay_method['b'][$m] += $c->import;
          break;
      }
    }
    //--------------------------------------------------------------------//
    $aux = $months_empty;
    foreach ($crLst as $k => $v) {
      for ($i = 0; $i < 13; $i++) {
        $aux[$i] += $v[$i];
      }
    }
    $aux[0] = array_sum($aux);
    $currentY['Ingresos'] = $aux;

    $tIncomes = 0;
    foreach ($crLst as $k => $v) {
      $t = 0;
      foreach ($v as $k1 => $v1) {
        if (is_numeric($k1)) {
          $t += $v1;
        }
      }
      $crLst[$k][0] = $t;
      $tIncomes += $t;
    }


    // -----------------------------------------------------------------------//
    
    for ($i = 2; $i > 0; $i--) {
      $yAux = $year - $i;
      $expensesYear[$yAux] = Expenses::whereYear('date', '=', $yAux)->whereIn('type', $expensesLst)->sum('import');
    }
    $oExpenses = Expenses::whereYear('date', '=', $year)->whereIn('type', $expensesLst)->get();
    $aux = $months_empty;
    $expensesYear[$year] = 0;
    if ($oExpenses) {
      foreach ($oExpenses as $e) {
        $m = intval(substr($e->date, 5, 2));

        $aux[$m] += $e->import;
        $aux[0] += $e->import;
        $expensesYear[$year] += $e->import;

        $g = isset($gTypeGroup_g[$e->type]) ? $gTypeGroup_g[$e->type] : 'otros';
        if (isset($ggMonth[$g])) {
          $ggMonth[$g][$m] += $e->import;
        } else {
          $ggMonth['otros'][$m] += $e->import;
        }
      }
    }
    //---------------------------------------------------------//
    $ggMonth['pt'] = $months_empty;
    $gTypeGroup['names']['pt'] = 'SUELDOS Y SALARIOS';
    $CoachLiqService = new \App\Services\CoachLiqService();
    for ($i = 0; $i < 3; $i++) {
      $auxYear = $year - $i;
      $sCoachLiq = $CoachLiqService->liqByMonths($auxYear,null,$coachRole);

      foreach ($sCoachLiq['aLiq'] as $liq) {
        foreach ($liq as $m => $t) {
          $expensesYear[$auxYear] += $t;
          if ($i == 0) {
            $ggMonth['pt'][$m] += $t;
            $aux[$m] += $t;
            $aux[0] += $t;
          }
        }
      }
    }
    //---------------------------------------------------------//
    $currentY['Gastos'] = $aux;

    //---------------------------------------------------------//
    $oUser = new User();
    $subscs = 0;
    $uActivs = User::where('status', 1)->count();
    $subscsFidelity = 0;
    $uActivsFidelity = 0;
    $subscsBasic = 0;
    $uActivsBasic =  0;
    //---------------------------------------------------------//
    $aux_i = $aux_e = $months_empty;
    //---------------------------------------------------------//
    return view('admin.contabilidad.pyg.indexDpto', [
        'year' => $year,
        'lstMonths' => $lstMonths,
        'currentY' => $currentY,
        'incomesYear' => $incomesYear,
        'expensesYear' => $expensesYear,
        'subscs' => $subscs,
        'uActivs' => $uActivs,
        'ggMonth' => $ggMonth,
        'ggNames' => $gTypeGroup['names'],
        'oRateTypes' => $oRateTypes,
        'crLst' => $crLst,
        'aux_i' => $aux_i,
        'aux_e' => $aux_e,
        'tIncomes' => $tIncomes,
        'tPay' => $tPay,
        'pay_method' => $pay_method,
        'subscsFidelity' => $subscsFidelity,
        'uActivsFidelity' => $uActivsFidelity,
        'subscsBasic' => $subscsBasic,
        'uActivsBasic' => $uActivsBasic,
        'tExpenType'=>'e2'
    ]);
  }

  public function informeClienteMes(Request $request, $month = null, $f_month = null, $f_rate = null, $f_method = null, $f_coach = null) {

    $year = getYearActive();
    if (!$month)
      $month = date('m');

    $day = 'all';

    $data = $this->getCharges($year, $month, $day, null, $f_method, $f_rate, $f_coach, $f_month);
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

    $chargesIDs = [];
    foreach ($data['charges'] as $c) {
      $chargesIDs[] = $c->id;
    }
    $data['aURates'] = \App\Models\UserRates::whereIn('id_charges', $chargesIDs)
                    ->pluck('rate_month', 'id_charges')->toArray();

    /*     * ************************************************************** */
    $rateFilter = [];
    $oTypes = \App\Models\TypesRate::all();
    foreach ($oTypes as $item) {
      $aux = \App\Models\Rates::where('type', $item->id)->get();
      $aux2 = [];
      foreach ($aux as $a) {
        $aux2[$a->id] = $a->name;
      }
      $rateFilter[$item->id] = ['n' => $item->name, 'l' => $aux2];
    }
    $data['rateFilter'] = $rateFilter;
    $data['filt_rate'] = $f_rate;
    $data['filt_method'] = $f_method;
    $data['filt_month'] = $f_month;
    /*     * ************************************************************** */
    $data['f_coach'] = $f_coach;
    $data['aTRates'] = \App\Models\Rates::getRatesTypeRates();
    $data['aCoachs'] = User::getCoachs()->pluck('name', 'id');
    return view('admin.informes.informeClientesMes', $data);
  }


  function ExpensesbyType($type){
    $year = getYearActive();
    $dpto = $this->getDepto();
    $expensesLst = $dpto[0];
    $coachRole = $dpto[1];


    if ($type == 'pt' || $type == 'sueldos_y_salarios'){
      $sCoachLiq = new \App\Services\CoachLiqService();
      $data = $sCoachLiq->liqByCoachMonths($year,$coachRole);
      include_once app_path().'/Blocks/PyG_Coachs.php';
    } else {
      $gTypeGroup = Expenses::getTypesGroup();
      $aTypeLst = Expenses::getTypes();
      if (!isset($gTypeGroup['names'][$type])){
        echo  '<p class="alert alert-warning">Sin Registros</p>';
        return '';
      }
      $gTypesNames = $gTypeGroup['names'];
      $gTypeGroup = $gTypeGroup['groups'];
      $auxTypes = [];
      foreach ($gTypeGroup as $k=>$v){
        if ($v == $type && in_array($k,$expensesLst)) $auxTypes[] = $k;
      }
      $payType = Expenses::getTypeCobro();

      $items = Expenses::whereYear('date', '=', $year)
              ->whereIn('type',$auxTypes)->orderBy('date')->get();
      if (count($items)== 0){
        echo  '<p class="alert alert-warning">Sin Registros</p>';
        return '';
      }
//      dd($gTypeGroup);
      include_once app_path().'/Blocks/PyG_Expenses.php';
    }
  }


}
