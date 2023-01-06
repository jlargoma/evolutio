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
    if ($uID == 2844 ){ //estetica
      $expensesLst = ['gto_mat_esthetic','renting_estetica'];
      $coachRole = 'esthetic';
      $rType = [12];

      $rIDs = \App\Models\Rates::whereIn('type',$rType)->orderBy('name')->pluck('id'); 
      $bIDs = Bonos::whereIn('rate_type',$rType)->orWhereIn('rate_id',$rIDs)->orWhere('rate_subf', 'LIKE', "%e%")->pluck('id');
    }
    if ($uID == 2858){ // fisio
      $expensesLst = ['gto_mat._fisio','renting_fisioterapia'];
      $coachRole = 'fisio';
      $rType = [8];
      $rIDs = \App\Models\Rates::whereIn('type',$rType)->orderBy('name')->pluck('id'); 
      $bIDs = Bonos::whereIn('rate_type',$rType)->orWhereIn('rate_id',$rIDs)->orWhere('rate_subf', 'LIKE', "%f%")->pluck('id');
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
    $uRates = UserRates::withCharges()->whereIn('users_rates.id_rate',$rIDs)->where('rate_year', $year)->get();

    $aux = $months_empty;
    $pay_method = ['c' => $months_empty, 'b' => $months_empty, 'v' => $months_empty, 'np' => $months_empty];
    $tPay = 0;
    foreach ($uRates as $item) {
      $m = $item->rate_month;

      if ($item->ch_id){
        switch ($item->ch_type_payment){
          case 'cash':
            $pay_method['c'][$m] += $item->ch_import;
          case 'card':
            $pay_method['v'][$m] += $item->ch_import;
            break;
          case 'banco':
            $pay_method['b'][$m] += $item->ch_import;
            break;
        }
        $rateGr = isset($aRates[$item->ch_id_rate]) ? $aRates[$item->ch_id_rate] : 3;
        $crLst[$rateGr][$m] += $item->ch_import;
        $tPay += $item->ch_import;
      } else {
        $rateGr = isset($aRates[$item->id_rate]) ? $aRates[$item->id_rate] : 3;
        $crLst[$rateGr][$m] += $item->price;
        $pay_method['np'][$m] += $item->price;
      }
    }
    //--------------------------------------------------------------------//
    $lstBonos = \App\Models\Bonos::listBonos();
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

  public function informeClienteMes(Request $request, $f_month = null, $f_rate = null, $f_method = null, $f_coach = null) {

        //---------------------------------------------------------//
        $dpto = $this->getDepto();
        $coachRole = $dpto[1];
        $rType = $dpto[2];
        $rIDs = $dpto[3];
        $bIDs = $dpto[4];
        //---------------------------------------------------------//
        
    $year = getYearActive();
    if (!$f_month)
      $f_month = date('m');
    $day = 'all';

    if ($f_rate) {
      if ($f_rate == 'all') {
        $f_rate = $rType[0];
      } else {
        $filerRate = explode('-', $f_rate);
        if (!in_array($filerRate[0],$rType))
          $f_rate = $rType[0];
      }
    } else $f_rate = $rType[0];
    $data = $this->getCharges($year, $f_month, $day, null, $f_method, $f_rate, $f_coach, $f_month);
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

    $chargesIDs = [];
    foreach ($data['charges'] as $c) {
      $chargesIDs[] = $c->id;
    }
    $data['aURates'] = \App\Models\UserRates::whereIn('id_charges', $chargesIDs)->whereIn('id_rate',$rIDs)
                    ->pluck('rate_month', 'id_charges')->toArray();

    /*     * ************************************************************** */
    $rateFilter = [];
    $oTypes = \App\Models\TypesRate::whereIn('id',$rType)->get();
    foreach ($oTypes as $item) {
      $aux = \App\Models\Rates::whereIn('id', $rIDs)->where('type', $item->id)->get();
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
    $data['aCoachs'] = User::getCoachs()->where('role',$coachRole)->pluck('name', 'id');


    //--------------------------------------------------------------------//
    $aBonos = \App\Models\Bonos::all()->pluck('name','id');
    $oLstBonos = Charges::select('charges.*', 'users.name as username')
    ->join('users', 'users.id', '=', 'charges.id_user')
    ->whereYear('date_payment', '=', $year)
    ->whereMonth('date_payment', '=', $f_month)
    ->whereIn('bono_id',$bIDs)->get();
    $cTotalBonos = ['cash'=>0,'card'=>0,'banco'=>0];
    foreach ($oLstBonos as $c) {
      $cTotalBonos[$c->type_payment]  += $c->import;
    }
    $data['oLstBonos'] = $oLstBonos;
    $data['aBonos'] = $aBonos;
    $data['cTotalBonos'] = $cTotalBonos;
    //--------------------------------------------------------------------//
    return view('admin.informes.informeClientesMes_dpto', $data);
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

  private function getCharges($year, $month, $day, $search = null, $type_payment = null, $rate = null, $f_coach = null,$f_month = null) {
    $sql_charges = Charges::where('import', '!=', 0);
    if ($search) {
      $search = trim($search);
      $cliIDs = User::where('name', 'LIKE', "%" . $search . "%")->pluck('id');
      $sql_charges->whereIn('id_user', $cliIDs);
      //$sql_charges->join('Users', 'Users.id', '=', 'charges.id_user')->where('name', 'LIKE', "%" . $search . "%");
    }

    if ($day == "all") {
      $starDate = "$year-$month-01";
      $endDate = date("Y-m-t", strtotime($starDate));
      $sql_charges->where('date_payment', '>=', $starDate)->where('date_payment', '<=', $endDate);
    } else {
      $starDate = "$year-$month-$day";
      $sql_charges->where('date_payment', '=', $starDate);
    }

    if ($type_payment && $type_payment != 'all') {
      $sql_charges->where('type_payment', $type_payment);
    }

    if ($rate) {
      if ($rate != 'all') {
        $filerRate = explode('-', $rate);
        if (count($filerRate) == 2) {
          $sql_charges->where('id_rate', $filerRate[1]);
        } else {
          $sql_charges->where('type_rate', $filerRate[0]);
        }
      }
    }
    //------------------------------------------------------------//
    if ($f_coach){
      $sql_charges->join('users_rates', 'users_rates.id_charges', '=', 'charges.id');
      if ($f_coach) $sql_charges->where('users_rates.coach_id', $f_coach);
    }
    
    //------------------------------------------------------------//
    $charges = $sql_charges->select('charges.*')->orderBy('date_payment')->get();
    //------------------------------------------------------------//
    $CoachsService = new \App\Services\CoachsService();
    $aCargesCoachs = $CoachsService->getCoachsCharge($sql_charges->pluck('charges.id'));
    //------------------------------------------------------------//

    $bank = 0;
    $cash = 0;
    $card = 0;
    $clients = [];
    $rates = $bonos = [];
    foreach ($charges as $charge) {
      $clients[] = $charge->id_user;
      if ($charge->id_rate > 0)
        $rates[] = $charge->id_rate;
      if ($charge->bono_id > 0)
        $bonos[] = $charge->bono_id;
      switch ($charge->type_payment) {
        case 'banco':
          $bank += $charge->import;
          break;
        case 'cash':
          $cash += $charge->import;
          break;
        case 'card':
          $card += $charge->import;
          break;
      }
    }
    $endDay = date("t", strtotime($starDate));
    $aUsers = User::whereIn('id', $clients)->get()
                    ->pluck('name', 'id')->toArray();
    $aRates = \App\Models\Rates::whereIn('id', $rates)->get()
                    ->pluck('name', 'id')->toArray();

    $aBonos = \App\Models\Bonos::whereIn('id', $bonos)->get()
                    ->pluck('name', 'id')->toArray();
    return [
        'charges' => $charges,
        'aCargesCoachs' => $aCargesCoachs,
        'cash' => $cash,
        'card' => $card,
        'bank' => $bank,
        'clients' => $clients,
        'rates' => $rates,
        'year' => $year,
        'month' => $month,
        'day' => $day,
        'endDay' => $endDay,
        'aUsers' => $aUsers,
        'aRates' => $aRates,
        'aBonos' => $aBonos,
    ];
  }

}
