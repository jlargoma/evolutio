<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;
use App\Models\Expenses;
use App\Models\Charges;
use App\Services\CoachLiqService;
use App\Models\User;
use App\Models\UserRates;
use App\Models\DistribBenef;
use App\Models\Rates;
use App\Models\Bonos;
use App\Models\TypesRate;
use App\Models\UsersSuscriptions;

class PyGController extends Controller {

  public function index() {
    //---------------------------------------------------------//
    $year = getYearActive();
    $lstMonths = lstMonthsSpanish(false);
    unset($lstMonths[0]);
    $months_empty = array();
    for ($i = 0; $i < 13; $i++)  $months_empty[$i] = 0;

    
    //---------------------------------------------------------//
    // $gastos = Expenses::whereYear('date', '=', $year)->get();
    // $gType = Expenses::getTypes();
    $gTypeGroup = Expenses::getTypesGroup();
    $gTypeGroup_g = $gTypeGroup['groups'];
    $ggMonth = [];
    $crLst = [];
    foreach ($gTypeGroup_g as $k=>$v) $ggMonth[$v] = $months_empty;
    $ggMonth['otros'] = $months_empty;
    //---------------------------------------------------------//
    $oRateTypes = TypesRate::orderBy('name')->pluck('name','id')->toArray();
    $aRates = Rates::orderBy('name')->pluck('type','id')->toArray();
    foreach ($oRateTypes as $k=>$v) $crLst[$k] = $months_empty;
   
    //---------------------------------------------------------//
    $incomesYear = $expensesYear = [];
    $currentY = [];
    //---------------------------------------------------------//
    for ($i = 2; $i >= 0; $i--) {
      $yAux = $year - $i;
//      $incomesYear[$yAux] = Charges::getSumYear($yAux);
      $incomesYear[$yAux] = UserRates::getSumYear($yAux);
      
    }
    //----------------------------------------------------------//
    $uRates = UserRates::withCharges()->where('rate_year',$year)->get();
      
    $aux = $months_empty;
    $pay_method = ['c'=>$months_empty,'b'=>$months_empty,'v'=>$months_empty,'np'=>$months_empty,'i'=>$months_empty];

    $cashDepto = ['esthetic'=>$months_empty,'fisio'=>$months_empty,'other'=>$months_empty];
    $rIDsEsth = Rates::where('type',12)->orderBy('name')->pluck('id')->toArray();
    $bIDsEsth = Bonos::where('rate_type',12)->orWhereIn('rate_id',$rIDsEsth)->orWhere('rate_subf', 'LIKE', "%e%")->pluck('id')->toArray();
    $rIDsFisio = Rates::where('type',8)->orderBy('name')->pluck('id')->toArray();
    $bIDsFisio = Bonos::where('rate_type',8)->orWhereIn('rate_id',$rIDsFisio)->orWhere('rate_subf', 'LIKE', "%f%")->pluck('id')->toArray();

    $tPay = 0;
    foreach ($uRates as $item){
      $m = $item->rate_month;
      if ($item->ch_id){
        switch ($item->ch_type_payment){
          case 'cash':
            $pay_method['c'][$m] += $item->ch_import;
            if(in_array($item->ch_id_rate,$rIDsEsth)) $cashDepto['esthetic'][$m] += $item->ch_import;
              else if(in_array($item->ch_id_rate,$rIDsFisio)) $cashDepto['fisio'][$m] += $item->ch_import;
                else $cashDepto['other'][$m] += $item->ch_import;
            break;
          case 'card':
            $pay_method['v'][$m] += $item->ch_import;
            break;
          case 'banco':
            $pay_method['b'][$m] += $item->ch_import;
            break;
          case 'invita':
            $pay_method['i'][$m] += $item->ch_import;
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
    $lstBonos = Bonos::listBonos();
    //--------------------------------------------------------------------//
    $oBonos = Charges::whereYear('date_payment', '=', $year)
              ->where('bono_id','>',0)->get();
    $oRateTypes['bono'] = 'BONOS SUELTOS';
    $crLst['bono'] = $months_empty;
    foreach ($oBonos as $c){
      $m = intval(substr($c->date_payment,5,7));
      $rateType = isset($lstBonos[$c->bono_id]) ? $lstBonos[$c->bono_id] : null;
      
      if (isset($crLst[$rateType])){
        $crLst[$rateType][$m] += $c->import;
      } else {
        $crLst['bono'][$m] += $c->import;
      }
      
      switch ($c->type_payment){
        case 'cash':
          $pay_method['c'][$m] += $c->import;
          if(in_array($c->bono_id,$bIDsEsth)) $cashDepto['esthetic'][$m] += $c->import;
            else if(in_array($c->bono_id,$bIDsFisio)) $cashDepto['fisio'][$m] += $c->import;
              else $cashDepto['other'][$m] += $c->import;
          break;
        case 'card':
          $pay_method['v'][$m] += $c->import;
          break;
        case 'banco':
          $pay_method['b'][$m] += $c->import;
          break;
        case 'invita':
          $pay_method['i'][$m] += $c->import;
          break;
      }
    }
    //--------------------------------------------------------------------//
    $aux = $months_empty;
    foreach ($crLst as $k=>$v){
      for ($i = 0; $i < 13; $i++){
        $aux[$i] += $v[$i];
      }
    }
    $aux[0] = array_sum($aux);
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
      $expensesYear[$yAux] = Expenses::whereYear('date', '=', $yAux)->where('type','!=','distribucion')->sum('import');
      $repartoYear[$yAux] = DistribBenef::whereYear('date', '=', $yAux)->where('type','=','distribucion')->sum('import');
    }
    /******************* */
    $oRepartos = DistribBenef::whereYear('date', '=', $year)->where('type','=','distribucion')->get();
    $aux = $months_empty;
    $repartoYear[$year] = 0;
    if ($oRepartos) {
      foreach ($oRepartos as $e) {
        $m = intval(substr($e->date, 5, 2));
     
        $aux[$m] += $e->import;
        $aux[0] += $e->import;
        $repartoYear[$year] += $e->import;
      }
    }
    $currentY['Repartos'] = $aux;

    /******************* */

    $oExpenses = Expenses::whereYear('date', '=', $year)->where('type','!=','distribucion')->get();
    $aux = $months_empty;
    $expensesYear[$year] = 0;
    if ($oExpenses) {
      foreach ($oExpenses as $e) {
        $m = intval(substr($e->date, 5, 2));
     
        $aux[$m] += $e->import;
        $aux[0] += $e->import;
        $expensesYear[$year] += $e->import;
        
        $g = isset($gTypeGroup_g[$e->type]) ? $gTypeGroup_g[$e->type] : 'otros';
        if (isset($ggMonth[$g])){
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
    for($i=0;$i<3;$i++){
      $auxYear = $year-$i;
      $sCoachLiq = $CoachLiqService->liqByMonths($auxYear);
      
      foreach ($sCoachLiq['aLiq'] as $liq){
        foreach ($liq as $m=>$t){
          $expensesYear[$auxYear]  += $t;
          if ($i == 0){
            $ggMonth['pt'][$m] += $t;
            $aux[$m] += $t;
            $aux[0] += $t;
          }
        }
      }
    }
    //---------------------------------------------------------//
    $currentY['Gastos'] = $aux;
    
    /***************************************/
    $subscs = UsersSuscriptions::count();
    $uActivs = User::where('status',1)->count();
    $subscsFidelity = UsersSuscriptions::where('tarifa','fidelity')->count();
    $uActivsFidelity = UsersSuscriptions::getCountBySuscipt("fidelity");
    $subscsBasic = UsersSuscriptions::where('tarifa','basic')->count();
    $uActivsBasic = UsersSuscriptions::getCountBySuscipt("basic");
    /***************************************/
    $aux_i = $aux_e = $months_empty; 
    /***************************************/
    return view('admin.contabilidad.pyg.index',[
        'year'=>$year,
        'lstMonths'=>$lstMonths,
        'currentY'=>$currentY,
        'incomesYear'=>$incomesYear,
        'expensesYear'=>$expensesYear,
        'repartoYear'=>$repartoYear,
        'subscs'=>$subscs,
        'uActivs'=>$uActivs,
        'ggMonth'=>$ggMonth,
        'ggNames'=>$gTypeGroup['names'],
        'oRateTypes'=>$oRateTypes,
        'crLst'=>$crLst,
        'aux_i'=>$aux_i,
        'aux_e'=>$aux_e,
        'tIncomes'=>$tIncomes,
        'tPay'=>$tPay,
        'pay_method'=>$pay_method,
        'subscsFidelity'=>$subscsFidelity,
        'uActivsFidelity'=>$uActivsFidelity,
        'subscsBasic'=>$subscsBasic,
        'uActivsBasic'=>$uActivsBasic,
        'cashDepto'=>$cashDepto,
        'tExpenType'=>'e'
  ]);
  }


  function distrBeneficios(){
    $year = getYearActive();
    $lst = DistribBenef::whereYear('date', '=', $year)->get();
    $listResume = array();
    if ($lst) {
      foreach ($lst as $g) {
        if (!isset($listResume[$g->to_concept])) $listResume[$g->to_concept] = 0;
        $listResume[$g->to_concept] += $g->import;
      }
    }
    return view('admin.contabilidad.distrib_benef.index', [
        'year' => $year,
        'lst' => $lst,
        'listResume' => $listResume,
        'typePayment' => DistribBenef::getTypeCobro(),
        'concepts' =>DistribBenef::getConcepto(),
        
    ]);
  }

  function distrBeneficiosStore(Request $request) {

    $messages = [
        'concept.required' => 'El Concepto es requerido.',
        'import.required' => 'El Importe es requerido.',
        'fecha.required' => 'La Fecha es requerida.',
        'import.min' => 'El Importe debe ser mayor de :min.',
        'import.max' => 'El Importe no debe ser mayor de :max.',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'concept' => 'required|min:3',
                'import' => 'required|min:1|max:3000',
                'fecha' => 'required',
                    ], $messages);

    if ($validator->fails()) {
      return $validator->errors()->first();
    }
    $comment = $request->input('comment','');

    $oObject = new DistribBenef();
    $oObject->concept = $request->input('concept');
    $oObject->date = Carbon::createFromFormat('d-m-Y', $request->input('fecha'))->format('Y-m-d');
    $oObject->import = $request->input('import');
    $oObject->type_payment = $request->input('type_payment');
    $oObject->type = $request->input('type');
    $oObject->to_user = $request->input('to_user');
    $oObject->to_concept = $request->input('to_concept');
    $oObject->comment = $comment ? $comment : '';
    if ($oObject->save()) {
      return 'ok';
    }

 

    return 'error';
  }

  public function distrBeneficiosDelete(Request $request) {
    $id = $request->input('idToDelete');
    DistribBenef::where('id', $id)->delete();
    return back()->with(['success'=>'Registro eliminado']);
  }
}
