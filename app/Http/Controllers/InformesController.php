<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Bonos;
use Carbon\Carbon;
use DB;
use \App\Models\User;
use App\Models\Charges;
use App\Models\Rates;
use App\Models\UserBonosLogs;
use App\Exports\ServicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class InformesController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    //
  }

  /**
   * 
   * @param type $year
   * @param type $month
   * @param type $day
   * @param type $search
   * @param type $type_payment
   * @return type
   */
  private function getCharges($year, $month, $day, $search = null, $type_payment = null, $rate = null, $f_coach = null,$f_month = null) {
    $sql_charges = Charges::where('import', '!=', 0);
    if ($search) {
      $search = trim($search);
      $cliIDs = User::where('name', 'LIKE', "%" . $search . "%")->pluck('id');
      $sql_charges->whereIn('id_user', $cliIDs);
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
          
          $rType = intVal($filerRate[0]);
          $bTypes = Bonos::listBonos();
          $idBonosSel = [];
          foreach($bTypes as $k=>$v){
            if( $v == $rType ) $idBonosSel[] = $k;
          }

          $sql_charges->where(function ($query) use ($rType, $idBonosSel) {
              $query->where("type_rate", $rType)
                ->orWhereIn('bono_id', $idBonosSel);
            });

        }
      }
    }
    //------------------------------------------------------------//
    // $sqlURate = \App\Models\UserRates::where('id','>',0);
    // if ($f_coach) $sqlURate->where('coach_id', $f_coach);
    // if ($f_month) $sqlURate->where('rate_month', $f_month);
    // if ($f_coach || $f_month){
    //   $sql_charges->whereIn('id', $sqlURate->pluck('id_charges'));
    // }
    if ($f_coach){
      $sql_charges->join('users_rates', 'users_rates.id_charges', '=', 'charges.id');
      $sql_charges->where('users_rates.coach_id', $f_coach);
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
    $invita = 0;
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
        case 'invita':
          $invita += $charge->import;
          break;
      }
    }
    $rates = array_unique($rates);
    $bonos = array_unique($bonos);
    $clients = array_unique($clients);

    $endDay = date("t", strtotime($starDate));
    $aUsers = User::whereIn('id', $clients)->get()
                    ->pluck('name', 'id')->toArray();
    $aRates = \App\Models\Rates::whereIn('id', $rates)->get()
                    ->pluck('name', 'id')->toArray();

    $aBonos = Bonos::whereIn('id', $bonos)->get()
                    ->pluck('name', 'id')->toArray();

    return [
        'charges' => $charges,
        'aCargesCoachs' => $aCargesCoachs,
        'cash' => $cash,
        'card' => $card,
        'bank' => $bank,
        'invita' => $invita,
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

  public function getChargesRates($year, $month, $day, $search = null) {

    $sqlURates = \App\Models\UserRates::where('id_charges', '>', 0)
                    ->where('rate_month', $month)->where('rate_year', $year);
    if ($search) {
      $search = trim($search);
      $cliIDs = User::where('name', 'LIKE', "%" . $search . "%")->pluck('id');
      $sqlURates->whereIn('id_user', $cliIDs);
    }
    $uRates = $sqlURates->orderBy('created_at')->get();
    $bank = 0;
    $cash = 0;
    $card = 0;
    $invita = 0;
    $clients = [];
    $rates = [];
    $charges = [];
    foreach ($uRates as $item) {
      $clients[] = $item->id_user;
      $rates[] = $item->id_rate;

      $charge = $item->charges;
      if ($charge) {
        $charges[] = $charge;
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
          case 'invita':
            $invita += $charge->import;
            break;
        }
      }
    }

    $extrasCharges = [];
    $endDay = date("t", strtotime("$year-$month-01"));
    $aUsers = User::whereIn('id', $clients)->get()
                    ->pluck('name', 'id')->toArray();
    $aRates = \App\Models\Rates::whereIn('id', $rates)->get()
                    ->pluck('name', 'id')->toArray();

    return [
        'charges' => $charges,
        'extrasCharges' => $extrasCharges,
        'cash' => $cash,
        'bank' => $bank,
        'card' => $card,
        'invita' => $invita,
        'clients' => $clients,
        'rates' => $rates,
        'year' => $year,
        'month' => $month,
        'day' => $day,
        'endDay' => $endDay,
        'aUsers' => $aUsers,
        'aRates' => $aRates,
    ];
  }

  public function informeClienteMes(Request $request, $month = null,$f_month = null, $f_rate = null, $f_method = null, $f_coach = null) {

    $year = getYearActive();
    if (!$month)
      $month = date('m');

    $day = 'all';

    $data = $this->getCharges($year, $month, $day, null, $f_method, $f_rate, $f_coach,$f_month);
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

  public function informeCuotaMes(Request $request, $month = null, $day = null) {

    $year = getYearActive();
    if (!$month)
      $month = date('m');
    if (!$day)
      $day = 'all';

    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

    $byRate = [];
    $byRateT = [];
    $aRates = \App\Models\Rates::pluck('name', 'id')->toArray();
    $aRType = \App\Models\TypesRate::orderBy('name')->get()->pluck('name', 'id')->toArray();
    $aRrt = \App\Models\Rates::all()->pluck('type', 'id')->toArray();
    $typePay = [
      'toPay' => [],
      'banco' => [],
      'cash' => [],
      'invita' => [],
      'card' => [],
    ];
    $typePayTotal = [];
      $uRates = \App\Models\UserRates::where('rate_month', $month)->where('rate_year', $year)->get();
      $charges = [];
      foreach ($uRates as $item) {
        $charge = $item->charges;
        $rID = $item->id_rate;
        $rtID = isset($aRrt[$rID]) ? $aRrt[$rID] : -1;
        if (!array_key_exists($rID,$byRate)) $byRate[$rID] = 0;

        $auxTypePay = 'toPay';
        $price = $item->price;
        if ($charge) {
          $auxTypePay = $charge->type_payment;
          $price = $charge->import;
        }


          if (!array_key_exists($auxTypePay,$typePay)) $typePay[$auxTypePay] = [];
          if (!array_key_exists($rtID,$typePay[$auxTypePay])) $typePay[$auxTypePay][$rtID] = 0;
          $typePay[$auxTypePay][$rtID] += $price;
          $byRate[$rID] += $price;
          if (!array_key_exists($rtID,$typePayTotal)) $typePayTotal[$rtID] = 0;
          $typePayTotal[$rtID] += $price;

      }
    //----------------------------------------------------------//
    //----  BEGIN: BONOS        --------------------------------//
    $byBono = [];
    $aBonos = Bonos::all()->pluck('name', 'id')->toArray();
    $lstBonosRate = Bonos::listBonos();
    $oCharges = Charges::where('bono_id', '>', 0)
                    ->whereYear('date_payment', '=', $year)
                    ->whereMonth('date_payment', '=', $month)->get();
    foreach ($oCharges as $c) {
      if (!isset($byBono[$c->bono_id])) $byBono[$c->bono_id] = 0;


      $rtID = isset($lstBonosRate[$c->bono_id]) ? $lstBonosRate[$c->bono_id] : null;
      $byBono[$c->bono_id] += $c->import;
      if (!array_key_exists($c->type_payment,$typePay)) $typePay[$c->type_payment] = [];
      if (!array_key_exists($rtID,$typePay[$c->type_payment])) $typePay[$c->type_payment][$rtID] = 0;
      $typePay[$c->type_payment][$rtID] += $c->import;
      if (!array_key_exists($rtID,$typePayTotal)) $typePayTotal[$rtID] = 0;
      $typePayTotal[$rtID] += $c->import;
    }
    //----  END: BONOS        --------------------------------//
    //----------------------------------------------------------//
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data = [
      'year' => $year,
      'month' => $month,
      'months'=>$lstMonthsSpanish,
      'day' => $day,
      'byRate'=>$byRate,
      'aRType'=>$aRType,
      'byBono'=>$byBono,
      'aBonos'=>$aBonos,
      'typePay'=>$typePay,
      'typePayTotal'=>$typePayTotal,
      //'endDay' => $endDay,$endDay = date("t", strtotime("$year-$month-01"));
      'aRates' => $aRates,
    ];

    return view('admin.informes.informeCuotaMes', $data);
  }

  public function informeCobrosMes(Request $request, $month = null, $day = null) {

    $year = getYearActive();
    if ($month == null)   $month = date('m');


    $aRType = \App\Models\TypesRate::all()->pluck('name', 'id')->toArray();
    //rate types
    $aRrt = \App\Models\Rates::all()->pluck('type', 'id')->toArray();
    $aRname = \App\Models\Rates::all()->pluck('name', 'id')->toArray();

    $rByCoach = [];
    $countByCoach = [];
    $tCoachs = [];
    $uIDs = [];
    $sql_uRates = \App\Models\UserRates::select(
                            'users_rates.*', 'charges.type_payment',
                            'charges.import', 'charges.discount')
                    ->where('rate_year', $year);
                    
    if ($month>0) $sql_uRates->where('rate_month', $month);
                    
    $uRates = $sql_uRates->join('charges', 'id_charges', '=', 'charges.id')->get();
    if ($uRates) {
      foreach ($uRates as $uR) {
        if (!isset($rByCoach[$uR->coach_id]))
          $rByCoach[$uR->coach_id] = [];

        $rByCoach[$uR->coach_id][] = [
            $uR->id_user,
            $uR->id_rate,
            $uR->type_payment,
            $uR->import,
            $uR->discount,
            isset($aRrt[$uR->id_rate]) ? $aRrt[$uR->id_rate] : null,
            isset($aRname[$uR->id_rate]) ? $aRname[$uR->id_rate] : null,
        ];

        if (!isset($tCoachs[$uR->coach_id]))
          $tCoachs[$uR->coach_id] = 0;
        if (!isset($countByCoach[$uR->coach_id]))
          $countByCoach[$uR->coach_id] = 0;

        $tCoachs[$uR->coach_id] += $uR->import;
        $countByCoach[$uR->coach_id]++;
        $uIDs[] = $uR->id_user;
      }
    }


    $aLstCoachs = array_keys($tCoachs);

    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);

    /*     * ********************************* */

    $auxCount = ['nutri' => 0, 'fisio' => 0, 'fisioG' => 0, 'suscrip' => 0, 'bonos' => 0, 'otros' => 0];
    $countCoachs = [null => $auxCount];
    $sql_lstDates = \App\Models\Dates::whereIn('date_type', ['nutri', 'fisio', 'fisioG'])
            ->whereYear('date', '=', $year)
            ->where('id_user_rates', '>', 1);
    if ($month>0) $sql_lstDates->whereMonth('date', '=', $month);
    $lstDates = $sql_lstDates->get();
    if ($lstDates) {
      foreach ($lstDates as $item) {
        if (!isset($countCoachs[$item->id_coach]))
          $countCoachs[$item->id_coach] = $auxCount;
        $countCoachs[$item->id_coach][$item->date_type]++;
        $aLstCoachs[] = $item->id_coach;
      }
    }

    $lstUsrSuscript = \App\Models\UsersSuscriptions::all();
    if ($lstUsrSuscript) {
      foreach ($lstUsrSuscript as $item) {
        if (!isset($countCoachs[$item->id_coach]))
          $countCoachs[$item->id_coach] = $auxCount;
        $countCoachs[$item->id_coach]['suscrip']++;
        $aLstCoachs[] = $item->id_coach;
      }
    }

    $sql_lstBonos = \App\Models\UserBonosLogs::whereNotNull('bono_id')
            ->whereYear('created_at', '=', $year)
            ->with('ubonos', 'charge');
    if ($month>0) $sql_lstBonos->whereMonth('created_at', '=', $month);
    $lstBonos = $sql_lstBonos->get();
    if ($lstBonos) {
      foreach ($lstBonos as $item) {
        if (!isset($countCoachs[$item->coach_id]))
          $countCoachs[$item->coach_id] = $auxCount;
        $countCoachs[$item->coach_id]['bonos']++;
        $aLstCoachs[] = $item->coach_id;
        $price = $item->charge->import;
        if ($item->ubonos) {
          $rByCoach[$item->coach_id][] = [
              $item->ubonos->user_id,
              $item->text,
              null,
              $price,
              $item->discount,
              'bono',
              ''
          ];

          if (!isset($tCoachs[$item->coach_id]))
            $tCoachs[$item->coach_id] = 0;
          $tCoachs[$item->coach_id] += $price;
          $uIDs[] = $item->ubonos->user_id;

          if (!isset($countByCoach[$item->coach_id]))
            $countByCoach[$item->coach_id] = 0;
          $countByCoach[$item->coach_id]++;
        }
      }
    }

    $data['countCoachs'] = $countCoachs;

    /*     * *************************************************** */

    $aCoachs = User::whereCoachs()->where('status',1)
                    ->pluck('name', 'id')->toArray();

    /* -------------------------------- */
    $cLiq = [];
    $sCoachLiqService = new \App\Services\CoachLiqService();
    if ($month>0){
      foreach ($aCoachs as $cid=>$name){
        $cLiq[$cid] = $sCoachLiqService->payToCoachMonths($cid,$year,$month);
        //$cLiq[$cid] = $aux['salary'] + array_sum($aux['totalExtr'])+ $aux['commision'];
      }
    } else {
      foreach ($aCoachs as $cid=>$name) $cLiq[$cid] = $sCoachLiqService->payToCoachMonths($cid,$year);
    }
    
    /* -------------------------------- */
    $aCustomers = User::whereIn('id', $uIDs)
                    ->pluck('name', 'id')->toArray();

    $data['aCoachs'] = $aCoachs;
    $data['cLiq'] = $cLiq;
    $data['aCust'] = $aCustomers;
    $data['months'] = $lstMonthsSpanish;

    $data['year'] = $year;
    $data['month'] = $month;
    $data['aRname'] = $aRname;
    $data['aRType'] = $aRType;
    $data['rByCoach'] = $rByCoach;
    $data['countByCoach'] = $countByCoach;

    $data['tCoachs'] = $tCoachs;
    /*     * **************************************************** */
    return view('admin.informes.informeCobrosMes', $data);
  }

  public function searchClientInform(Request $request, $month = null) {
    $year = getYearActive();
    if (!$month)
      $month = date('m');

    $search = trim($request->input('search', ''));
    $data = $this->getChargesRates($year, $month, 'all', $search);
    return view('admin/informes/_table_informes', $data);
  }

  public function informeCierreDiario(Request $request, $month = null, $day = null) {


    $year = getYearActive();
    if (!$month)
      $month = date('m');
    if (!$day)
      $day = 'all';

    $data = $this->getCharges($year, $month, $day);
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $months = $lstMonthsSpanish;

    $totalBank = $totalCash = 0;
    $arrayDays = [];
    if ($day == 'all') {
      for ($i = 1; $i <= $data['endDay']; $i++)
        $arrayDays[$i] = ['cash' => 0, 'bank' => 0];
    } else
      $arrayDays[$day] = ['cash' => 0, 'bank' => 0];

    foreach ($data['charges'] as $i) {
      $j = date('j', strtotime($i->date_payment));
      if ($i->type_payment == 'cash') {
        $arrayDays[$j]['cash'] += $i->import;
        $totalCash += $i->import;
      } else {
        $arrayDays[$j]['bank'] += $i->import;
        $totalBank += $i->import;
      }
    }
    if (isset($data['extrasCharges']))
      foreach ($data['extrasCharges'] as $i) {
        $j = date('j', strtotime($i->date));
        $arrayDays[$j]['cash'] += $i->import;
        $totalCash += $i->import;
      }

    $auxTime = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-';
    if ($day == 'all') {
      $auxTime .= '01';
      $yesterday = date('n/j', strtotime($auxTime . ' -1 days'));
      $tomorrow = date('n/j', strtotime($auxTime));
    } else {
      $auxTime .= str_pad($day, 2, "0", STR_PAD_LEFT);
      $yesterday = date('n/j', strtotime($auxTime . ' -1 days'));
      $tomorrow = date('n/j', strtotime($auxTime . ' +1 days'));
    }

    return view('.admin.informes.cierresDiarios', [
        'arrayDays' => $arrayDays,
        'totalBank' => $totalBank,
        'totalCash' => $totalCash,
        'month' => $month,
        'months' => $months,
        'year' => $year,
        'day' => $day,
        'clients' => $data['clients'],
        'endDay' => $data['endDay'],
        'total' => $totalBank + $totalCash,
        'yesterday' => $yesterday,
        'tomorrow' => $tomorrow,
    ]);
  }

  public function informeCaja(Request $request) {

    $date = date('Y-m-d');
    $charges = Charges::where('import', '!=', 0)
            ->where('date_payment', '=', $date)
            ->where('type_payment', 'cash')
            ->sum('import');
    $oCashBox = \App\Models\CashBox::where('import', '!=', 0)
                    ->where('date', '=', $date)->get();
    $total = $charges;
    foreach ($oCashBox as $item) {
      if ($item->typePayment == "INGRESO") {
        $total += $item->import;
      } else {
        $total -= $item->import;
      }
    }


    return view('.admin.informes.caja', [
        'totalCash' => $total,
        'date' => $date
    ]);
  }

  public function informeCajaMes(Request $request, $month = null, $day = null) {

    $year = getYearActive();
    if (!$month)
      $month = date('m');
    if (!$day)
      $day = 'all';

    $data = $this->getCharges($year, $month, $day, null, 'cash');
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

    $chargesIDs = [];
    foreach ($data['charges'] as $c) {
      $chargesIDs[] = $c->id;
    }
    $data['aURates'] = \App\Models\UserRates::whereIn('id_charges', $chargesIDs)
                    ->pluck('rate_month', 'id_charges')->toArray();

    /*     * ************************************************************ */
    $date = date('Y-m-d');
    $data['totalCash'] = Charges::where('import', '!=', 0)
            ->where('date_payment', '=', $date)
            ->where('type_payment', 'cash')
            ->sum('import');
    $data['date'] = $date;
    /*     * ************************************************************ */
    $data['showFilter'] = true;
    return view('admin.informes.informeCajaMes', $data);
  }

  public function informeCajaDiaria() {

    $year = getYearActive();
    $month = date('m');
    $day = date('d');

    $data = $this->getCharges($year, $month, $day, null, 'cash');
    $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

    $chargesIDs = [];
    foreach ($data['charges'] as $c) {
      $chargesIDs[] = $c->id;
    }
    $data['aURates'] = \App\Models\UserRates::whereIn('id_charges', $chargesIDs)
                    ->pluck('rate_month', 'id_charges')->toArray();

    /*     * ************************************************************ */
    $date = date('Y-m-d');
    $data['totalCash'] = Charges::where('import', '!=', 0)
            ->where('date_payment', '=', $date)
            ->where('type_payment', 'cash')
            ->sum('import');
    $data['date'] = $date;
    /*     * ************************************************************ */
    $data['showFilter'] = false;
    return view('admin.informes.informeCajaMes', $data);
  }




  public function informeServiciosMes(Request $request, $f_month = null, $f_rate = null, $f_method = null, $f_coach = null) {

    $oUser = Auth::user();
    if ($oUser->role !== "admin" && $oUser->id !== 3370 ){ //admin && pauperezol
      abort(401);
      exit();
    }

    $year = getYearActive();
    if (!$f_month)
      $f_month = date('m');


      $sqlURates = \App\Models\UserRates::where('rate_month', $f_month)->where('rate_year', $year);
      if ($f_coach) $sqlURates->where('coach_id', $f_coach);
      $rType = null;
      if ($f_rate) {
        if ($f_rate != 'all') {
          $filerRate = explode('-', $f_rate);
          if (count($filerRate) == 2) {
            $sqlURates->where('id_rate', $filerRate[1]);
          } else {
            $rType = intVal($filerRate[0]);
            $sqlURates->whereIn('id_rate', Rates::where('type',$filerRate[0])->pluck('id'));
          }
        }
      }

      $uRates = $sqlURates->orderBy('created_at')->get();


      $aCoachs = User::getCoachs()->pluck('name', 'id');

      $toPay = 0;
      $bank = 0;
      $cash = 0;
      $invita = 0;
      $card = 0;
      $bono = 0;
      $charges = [];
      $uCount = [];
      foreach ($uRates as $item) {
        $charge = $item->charges;
        $uCount[] =  $item->id_user;
        if ($charge) {
          $inport = $item->charged;
          $type_payment = '';
          switch ($charge->type_payment) {
            case 'banco':
            $bank += $inport;
            $type_payment = 'BANCO';
            break;
            case 'cash':
            $cash += $inport;
            $type_payment = 'METALICO';
            break;
            case 'card':
            $card += $inport;
            $type_payment = 'TARJETA';
            break;
            case 'invita':
            $invita += $inport;
            $type_payment = 'Inv. Evolutio';
            break;
            case 'bono':
            $bono += $inport;
            $type_payment = 'BONO';
            break;
          }
          $charges[$item->id] = [
            'id'=> $charge->id,
            'date'=> dateMin($charge->date_payment),
            'import'=> moneda($inport,true,1),
            'type_payment'=> $type_payment,
          ];
          
        } else {
          $toPay += $item->price;
        }
      }
  
      $aCustomers = User::pluck('name', 'id')->toArray();

      $aRates = \App\Models\Rates::pluck('name', 'id')->toArray();

      $data = [
        'uRates' => $uRates,
        'bank' => $bank,
        'cash' => $cash,
        'card' => $card,
        'invita' => $invita,
        'bono' => $bono,
        'toPay' => $toPay,
        'chargesData' => $charges,
        'aCustomers' => $aCustomers,
        'aRates' => $aRates,
        'tCustomer' => count(array_unique($uCount)),
      ];
      $lstMonthsSpanish = lstMonthsSpanish();
    unset($lstMonthsSpanish[0]);
    $data['months'] = $lstMonthsSpanish;

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
    $data['year'] = $year;
    /*     * ************************************************************** */
    $data['f_coach'] = $f_coach;
    $data['aTRates'] = \App\Models\Rates::getRatesTypeRates();
    $data['aCoachs'] = $aCoachs;






    //--------------------------------------------------------------------//
    $aBonos = Bonos::all()->pluck('name','id');
    $sqlBonos = Charges::select('charges.*', 'users.name as username')
    ->join('users', 'users.id', '=', 'charges.id_user')
    ->whereYear('date_payment', '=', $year)
    ->whereMonth('date_payment', '=', $f_month);
    if ($rType){
      $bTypes = Bonos::listBonos();
      $idBonosSel = [];
      foreach($bTypes as $k=>$v) 
        if( $v == $rType ) $idBonosSel[] = $k;
        
      $sqlBonos->whereIn('bono_id', $idBonosSel);
    } else {
      $sqlBonos->where('bono_id','>',0);
    }
    
    $oLstBonos = $sqlBonos->get();
    $cTotalBonos = ['cash'=>0,'card'=>0,'banco'=>0,'bono'=>0,'invita'=>0];
    $chargesID = [];
    foreach ($oLstBonos as $c) {
      $cTotalBonos[$c->type_payment]  += $c->import;
      $chargesID[]  = $c->id;
    }
    $data['oLstBonos'] = $oLstBonos;
    $data['aBonos'] = $aBonos;
    $data['cTotalBonos'] = $cTotalBonos;
    $data['aCargesCoachs'] =  UserBonosLogs::whereIn('charge_id',(array_unique($chargesID)))->pluck('coach_id','charge_id')->toArray();
    return view('admin.informes.informeServiciosMes', $data);
  }




  public function informeServiciosAll(Request $request) {
    return Excel::download(new ServicesExport, 'SERVICIOS_' . date('Y_m_d_s') . '.xlsx');
  }
}
