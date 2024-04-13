<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\StripeController;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Rates;
use App\Models\TypesRate;
use App\Models\Dates;
use App\Models\UsersNotes;
use App\Models\UserRates;
use App\Models\Charges;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\UserBonos;
use App\Models\Settings;
use App\Models\UsersSuscriptions;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use App\Models\Convenios;

trait ClientesTraits
{

  public function clientes(Request $request, $month = false)
  {

    $year = getYearActive();
    if (str_contains($month, '-')) {
      $aux = explode('-', $month);
      $year = $aux[0];
      $month = $aux[1];
    }
    if (!$month)
      $month = date('n');


    $months = lstMonthsSpanish(false);
    unset($months[0]);
    $oUser = new User();
    $oUser->create_altaBajas($year, $month);
    $oRates = Rates::orderBy('type', 'desc')->orderBy('name', 'desc')->get();
    $detail = [];
    $payments = $noPay = 0;
    $status = isset($request->status) ? $request->status : 1;
    $fFamily = isset($request->fFamily) ? $request->fFamily : null;
    $fRate = isset($request->fRate) ? $request->fRate : null;
    $tit = '';
    switch ($status) {
      case 'all':
        $sqlUsers = User::where('role', 'user');
        $tit = 'Todos los usuarios';
        break;
      case 'new':
        $tit = 'Nuevos usuarios ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month)->where('status', 1);
        break;
      case 'unsubscribeds':
        $tit = 'Usuarios dados de baja ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month)->where('status', 0);
        break;
      case 'new_unsubscribeds':
        $tit = 'Usuarios Nuevos ó Dados de Baja ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month, $fFamily);
        break;
      case 2:
        $tit = 'Usuarios fidelity';
        $uPlan = DB::table('user_meta')
          ->where('meta_key', 'plan')
          ->where('meta_value', 'fidelity')
          ->pluck('user_id');

        $sqlUsers = User::select('users.*')->where('role', 'user')
          ->where('status', 1)
          ->whereIntegerInRaw('users.id', $uPlan);
        break;
      default:
        if ($status == 0) $tit = 'Usuarios Inactivos';
        if ($status == 1) $tit = 'Usuarios Activos';
        $sqlUsers = User::where('role', 'user')
          ->where('status', $status);
        break;
    }

    $sqlUsers->with('userCoach');
    if ($fRate) {
      $sqlUsers->join('users_rates', function ($join) {
        $join->on('users.id', '=', 'users_rates.id_user');
      })->where('users_rates.id_rate', $fRate)
        ->where('users_rates.rate_year', $year)
        ->where('users_rates.rate_month', $month)
        ->whereNull('users_rates.deleted_at')
        ->select('users.*');
    }

    if ($fFamily && $status != 'new_unsubscribeds') {

      $lstIDs = User::usersRatesFamilyMonths($year, $month, $fFamily);
      $sqlUsers->whereIN('users.id', $lstIDs);
    } 

    $users = $sqlUsers->orderBy('name', 'asc')->get();
    $userIDs =  $sqlUsers->pluck('users.id');
    //---------------------------------------------//
    $aRates = [];
    $typeRates = TypesRate::pluck('name', 'id');
    $aRateType = [];
    //    $oRates = Rates::whereIn('type', $typeRates)->get();
    $rPrices = $rNames = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        $aRates[$r->id] = $r;
        $rPrices[$r->id] = $r->price;
        $rNames[$r->id] = $r->name;
        if (isset($typeRates[$r->type])) {
          $rNames[$r->id] = $typeRates[$r->type] . '<br>' . $r->name;
          $aRateType[$r->id] = $typeRates[$r->type];
        }
      }
    }

    //---------------------------------------------//
    $arrayPaymentMonthByUser = array();
    $date = date('Y-m-d', strtotime($year . '-' . $month . '-01' . ' -1 month'));
    $toPay = $uRates = $uCobros = [];
    $total_pending = 0;
    $monthAux = date('m', strtotime($date));
    $yearAux = date('Y', strtotime($date));
    for ($i = 0; $i < 3; $i++) {
      $resp = $this->getRatesByMonth($monthAux, $yearAux, $userIDs, $rPrices, $rNames);
      $uRates[$i] = $resp[0];
      $toPay[$i] = $resp[2];
      $noPay += $resp[2];
      $detail[] = $resp[3];
      $next = strtotime($date . ' +1 month');
      $date = date('Y-m-d', $next);
      $monthAux = date('m', $next);
      $yearAux = date('Y', $next);
    }

    if (count($detail) > 0) {
      $aux = '';
      foreach ($detail as $item) {
        foreach ($item as $k => $d) {
          $aux .= $k . ':{';
          foreach ($d as $k2 => $i2) {
            $aux .= "$k2: '$i2',";
          }
          $aux .= '},';
        }
      }
      $detail = "{ $aux }";
    } else {
      $detail = null;
    }
    $aCoachs = $oUser->whereCoachs('teach')->orderBy('name')->pluck('name', 'id')->toArray();

    /**/
    $uPlan = $oUser->getMetaUserID_byKey('plan', 'fidelity');
    $sql = DB::table('user_meta')
      ->where('meta_key', 'plan')
      ->where('meta_value', 'basic')
      ->where('created_at', '>=', date('Y-m-d', strtotime('-12 months')));

    $uPlanPenal =  $sql->pluck('user_id')->toArray();
    //    dd($uPlanPenal);
    $rFamilyName = [
      -1 => 'Suelo Pélvico',
      1 => 'Membresias',
      2 => 'PT',
      4 => 'Bungee',
      8 => 'Fisioterapia',
      14 => 'Fisio Getafe',
      10 => 'Nutrición',
      15 => 'Nutrición Getafe',
      12 => 'Estetica',
      16 => 'Estetica Getafe',
    ];
    $unsubscribeds = 0;
    $selectYear = $year;
    $year = getYearActive();

    global $cFRates;
    $customFamilyRates = Settings::getContent('customFamilyRates');
    $cFRates = ($customFamilyRates) ? json_decode($customFamilyRates) : [];
    foreach($cFRates as $k=>$item){
      $cFRates[$k]->ids = explode(',',$item->ids);
    }

    ob_start();
    $this->getAltasBajas($month);
    $altasBajas = ob_get_contents();
    ob_clean();


    $newUsers = 0;
    //$lstAltBaj = DB::select('SELECT rate_type, active, user_id FROM `user_alta_baja` WHERE rate_type IN (1,2) AND `year_month` ="'.$year . '-' . $month.'" ORDER BY active');
    $lstAltBaj = [];

    if($fFamily) {


      if($fFamily == -1){

        $sueloPelvico  = Rates::where('name', 'like', '%pelvico%')->pluck('id')->toArray();

        $lstAltBaj = UsersSuscriptions::where(function($query) use ($year, $month, $sueloPelvico) {
          $query->whereYear('deleted_at', $year)
          ->whereMonth('deleted_at',$month)
          ->whereIn('id_rate', $sueloPelvico);
        })->orWhere(function($query) use ($year, $month, $sueloPelvico) {
          $query->whereYear('created_at', $year)
          ->whereMonth('created_at',$month)
          ->whereIn('id_rate', $sueloPelvico);
        })->withTrashed()->get();

      } else {

        $lstAltBaj = UsersSuscriptions::leftjoin('rates', 'rates.id', 'users_suscriptions.id_rate')
        ->where(function($query) use ($year, $month, $fFamily) {
          $query->whereYear('users_suscriptions.deleted_at', $year)
          ->whereMonth('users_suscriptions.deleted_at',$month)
          ->where('rates.type', $fFamily);
        })->orWhere(function($query) use ($year, $month, $fFamily) {
          $query->whereYear('users_suscriptions.created_at', $year)
          ->whereMonth('users_suscriptions.created_at',$month)
          ->where('rates.type', $fFamily);
        })->withTrashed()->get();

      }      
      
      
    } else {

      $lstAltBaj = UsersSuscriptions::where(function($query) use ($year, $month) {
        $query->whereYear('deleted_at', $year)->whereMonth('deleted_at',$month);
      })->orWhere(function($query) use ($year, $month) {
        $query->whereYear('created_at', $year)->whereMonth('created_at',$month);
      })->withTrashed()->get();
      
    }

    $aLstAltBaj = [];
    foreach ($lstAltBaj as $item) {
      if (isset($aRateType[$item->id_rate])){
        $auxRate = $aRateType[$item->id_rate];
        if (!array_key_exists($item->id_user,$aLstAltBaj)) $aLstAltBaj[$item->id_user] = [];
        $aLstAltBaj[$item->id_user][] = [
          'rt'=>$auxRate,
          'active'=>($item->deleted_at ? 0 : 1 )];
        }
    }

    $newUsers = count($lstAltBaj);


    if(is_null($fFamily)){
      $createdUsers = User::whereYear('created_at', $year)->whereMonth('created_at',$month)->where('role', 'user')->get();

      foreach($createdUsers as $createdUser) {
        if(!isset($aLstAltBaj[$createdUser->id])) {
          $newUsers++;
        }
      }
    }

    
    return view('/admin/usuarios/clientes/index', [
      'users' => $users,
      'month' => $month,
      'year' => $year,
      'selectYear' => $selectYear,
      'status' => $status,
      'toPay' => $toPay,
      'noPay' => $noPay,
      'uRates' => $uRates,
      'detail' => $detail,
      'months' => $months,
      'aCoachs' => $aCoachs,
      'uPlan' => $uPlan,
      'uPlanPenal' => $uPlanPenal,
      'rNames' => $rNames,
      'rFamilyName' => $rFamilyName,
      'fRate' => $fRate,
      'fFamily' => $fFamily,
      'aRatesIds' => [],
      'newUsers' => $newUsers,
      'unsubscribeds' => $unsubscribeds,
      'tit' => $tit,
      'total_pending' => array_sum($arrayPaymentMonthByUser),
      'altasBajas' => $altasBajas,
      'aLstAltBaj' => $aLstAltBaj,
    ]);
  }

  public function clientesConvenio(Request $request, $month = false)
  {
    $convenios = Convenios::all();
    $year = getYearActive();
    if (str_contains($month, '-')) {
      $aux = explode('-', $month);
      $year = $aux[0];
      $month = $aux[1];
    }
    if (!$month)
      $month = date('n');


    $months = lstMonthsSpanish(false);
    unset($months[0]);
    $oUser = new User();
    $oUser->create_altaBajas($year, $month);
    $oRates = Rates::orderBy('type', 'desc')->orderBy('name', 'desc')->get();
    $detail = [];
    $payments = $noPay = 0;
    $status = isset($request->status) ? $request->status : 1;
    $fFamily = isset($request->fFamily) ? $request->fFamily : null;
    $fRate = isset($request->fRate) ? $request->fRate : null;
    $tit = '';
    switch ($status) {
      case 'all':
        $sqlUsers = User::where('role', 'user');
        $tit = 'Todos los usuarios';
        break;
      case 'new':
        $tit = 'Nuevos usuarios ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month)->where('status', 1);
        break;
      case 'unsubscribeds':
        $tit = 'Usuarios dados de baja ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month)->where('status', 0);
        break;
      case 'new_unsubscribeds':
        $tit = 'Usuarios Nuevos ó Dados de Baja ' . $month . '/' . $year;
        $sqlUsers = User::altaBajas($year, $month);
        break;
      case 2:
        $tit = 'Usuarios fidelity';
        $uPlan = DB::table('user_meta')
          ->where('meta_key', 'plan')
          ->where('meta_value', 'fidelity')
          ->pluck('user_id');

        $sqlUsers = User::select('users.*')->where('role', 'user')
          ->where('status', 1)
          ->whereIntegerInRaw('users.id', $uPlan);
        break;
      default:
        if ($status == 0) $tit = 'Usuarios Inactivos';
        if ($status == 1) $tit = 'Usuarios Activos';
        $sqlUsers = User::where('role', 'user')
          ->where('status', $status);
        break;
    }

    $sqlUsers->with('userCoach');
    if ($fRate) {
      $sqlUsers->join('users_rates', function ($join) {
        $join->on('users.id', '=', 'users_rates.id_user');
      })->where('users_rates.id_rate', $fRate)
        ->where('users_rates.rate_year', $year)
        ->where('users_rates.rate_month', $month)
        ->whereNull('users_rates.deleted_at')
        ->select('users.*');
    }

    if ($fFamily) {

      $lstIDs = User::usersRatesFamilyMonths($year, $month, $fFamily);
      $sqlUsers->whereIN('users.id', $lstIDs);
    }

    $users = $sqlUsers->orderBy('name', 'asc')->get();
    $userIDs =  $sqlUsers->pluck('users.id');
    //---------------------------------------------//
    $aRates = [];
    $typeRates = TypesRate::pluck('name', 'id');
    $aRateType = [];
    //    $oRates = Rates::whereIn('type', $typeRates)->get();
    $rPrices = $rNames = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        $aRates[$r->id] = $r;
        $rPrices[$r->id] = $r->price;
        $rNames[$r->id] = $r->name;
        if (isset($typeRates[$r->type])) {
          $rNames[$r->id] = $typeRates[$r->type] . '<br>' . $r->name;
          $aRateType[$r->id] = $typeRates[$r->type];
        }
      }
    }

    //---------------------------------------------//
    $arrayPaymentMonthByUser = array();
    $date = date('Y-m-d', strtotime($year . '-' . $month . '-01' . ' -1 month'));
    $toPay = $uRates = $uCobros = [];
    $total_pending = 0;
    $monthAux = date('m', strtotime($date));
    $yearAux = date('Y', strtotime($date));
    for ($i = 0; $i < 3; $i++) {
      $resp = $this->getRatesByMonth($monthAux, $yearAux, $userIDs, $rPrices, $rNames);
      $uRates[$i] = $resp[0];
      $toPay[$i] = $resp[2];
      $noPay += $resp[2];
      $detail[] = $resp[3];
      $next = strtotime($date . ' +1 month');
      $date = date('Y-m-d', $next);
      $monthAux = date('m', $next);
      $yearAux = date('Y', $next);
    }

    if (count($detail) > 0) {
      $aux = '';
      foreach ($detail as $item) {
        foreach ($item as $k => $d) {
          $aux .= $k . ':{';
          foreach ($d as $k2 => $i2) {
            $aux .= "$k2: '$i2',";
          }
          $aux .= '},';
        }
      }
      $detail = "{ $aux }";
    } else {
      $detail = null;
    }
    $aCoachs = $oUser->whereCoachs('teach')->orderBy('name')->pluck('name', 'id')->toArray();

    /**/
    $uPlan = $oUser->getMetaUserID_byKey('plan', 'fidelity');
    $sql = DB::table('user_meta')
      ->where('meta_key', 'plan')
      ->where('meta_value', 'basic')
      ->where('created_at', '>=', date('Y-m-d', strtotime('-12 months')));

    $uPlanPenal =  $sql->pluck('user_id')->toArray();
    //    dd($uPlanPenal);
    $rFamilyName = [
      -1 => 'Suelo Pélvico',
      1 => 'Membresias',
      2 => 'PT',
      8 => 'Fisioterapia',
      10 => 'Nutrición',
      12 => 'Estetica',
    ];
    $unsubscribeds = 0;
    $selectYear = $year;
    $year = getYearActive();

    global $cFRates;
    $customFamilyRates = Settings::getContent('customFamilyRates');
    $cFRates = ($customFamilyRates) ? json_decode($customFamilyRates) : [];
    foreach($cFRates as $k=>$item){
      $cFRates[$k]->ids = explode(',',$item->ids);
    }

    ob_start();
    $this->getAltasBajas($month);
    $altasBajas = ob_get_contents();
    ob_clean();


    $newUsers = 0;
    //$lstAltBaj = DB::select('SELECT rate_type, active, user_id FROM `user_alta_baja` WHERE rate_type IN (1,2) AND `year_month` ="'.$year . '-' . $month.'" ORDER BY active');

    $lstAltBaj = UsersSuscriptions::where(function($query) use ($year, $month) {
            $query->whereYear('deleted_at', $year)->whereMonth('deleted_at',$month);
        })->orWhere(function($query) use ($year, $month) {
          $query->whereYear('created_at', $year)->whereMonth('created_at',$month);
      })->withTrashed()->get();
    $aLstAltBaj = [];
    foreach ($lstAltBaj as $item) {
      if (isset($aRateType[$item->id_rate])){
        $auxRate = $aRateType[$item->id_rate];
        if (!array_key_exists($item->id_user,$aLstAltBaj)) $aLstAltBaj[$item->id_user] = [];
        $aLstAltBaj[$item->id_user][$auxRate] = [
          'rt'=>$auxRate,
          'active'=>($item->deleted_at ? 0 : 1 )];
        }
    }
    $newUsers = count($aLstAltBaj);


    return view('/admin/usuarios/clientes/indexConvenio', [
      'users' => $users,
      'month' => $month,
      'year' => $year,
      'selectYear' => $selectYear,
      'status' => $status,
      'toPay' => $toPay,
      'noPay' => $noPay,
      'uRates' => $uRates,
      'detail' => $detail,
      'months' => $months,
      'aCoachs' => $aCoachs,
      'uPlan' => $uPlan,
      'uPlanPenal' => $uPlanPenal,
      'rNames' => $rNames,
      'rFamilyName' => $rFamilyName,
      'fRate' => $fRate,
      'fFamily' => $fFamily,
      'aRatesIds' => [],
      'newUsers' => $newUsers,
      'unsubscribeds' => $unsubscribeds,
      'tit' => $tit,
      'convenios' => $convenios,
      'total_pending' => array_sum($arrayPaymentMonthByUser),
      'altasBajas' => $altasBajas,
      'aLstAltBaj' => $aLstAltBaj,
    ]);
  }

  public function getRatesByMonth($month, $year, $userIDs, $rPrices, $rNames)
  {

    $detail = [];
    $RateIDs = array_keys($rPrices);
    $uRates = UserRates::whereIntegerInRaw('id_user', $userIDs)
      ->where('rate_year', $year)
      ->where('rate_month', $month)
      ->whereIntegerInRaw('id_rate', $RateIDs)
      ->with('charges')
      ->get();

    $payments = $noPay = 0;
    $uLstRates = [];
    if ($uRates) {
      /******************************** */
      $aDates = Dates::whereIntegerInRaw('id_user_rates', $uRates->pluck('id'))
        ->pluck('date', 'id_user_rates')->toArray();

      foreach ($uRates as $k => $v) {
        $idRate = $v->id_rate;
        $idUser = $v->id_user;
        if (!isset($uLstRates[$idUser])) {
          $uLstRates[$idUser] = [];
        }
        if (!isset($uLstRates[$idUser][$idRate])) {
          $uLstRates[$idUser][$idRate] = [];
        }



        $dateCita = '';
        if (isset($aDates[$v->id])) {
          $auxDate  = explode(' ', $aDates[$v->id]);
          $dateCita = dateMin($auxDate[0]);
        }


        $detail[$v->id] = [
          'n' => '',
          'p' => moneda($rPrices[$idRate]),
          's' => $rNames[$idRate],
          'mc' => '', //Metodo pago
          'dc' => '', // fecha pago
          'date' => $dateCita
        ];
        // si esta pagado
        $auxCharges = $v->charges;
        if ($auxCharges) {
          $uLstRates[$idUser][$idRate][] = [
            'price' => $auxCharges->import,
            'id' => $v->id,
            'paid' => true,
            'cid' => $v->id_charges
          ];
          $payments += $auxCharges->import;
          $detail[$v->id]['mc'] = payMethod($auxCharges->type_payment);
          $detail[$v->id]['dc'] = dateMin($auxCharges->date_payment);
        } else {
          $importe = $v->price;
          //          $importe = ($v->price === null) ? $rPrices[$idRate]:$v->price;
          $noPay += $importe;

          $uLstRates[$idUser][$idRate][] = [
            'price' => $importe,
            'id' => $v->id,
            'paid' => false,
            'cid' => -1,
          ];
        }
      }
    }
    return [$uLstRates, $payments, $noPay, $detail];
  }

  public function clienteRateCharge($uRateID)
  {
    $uRates = UserRates::find($uRateID);

    if (!$uRates) {
      return view('admin.popup_msg', ['msg' => 'Servicio no asignada']);
    }
    $oUser = $uRates->user;
    $oRates = $uRates->rate;
    /*     * *************************** */
    /** BEGIN: STRIPE              ***** */
    $pStripe = null;
    $card = null;
    $paymentMethod = $oUser->getPayCard();
    if ($paymentMethod) {
      $aux = $paymentMethod->toArray();
      $card['brand'] = $aux['card']['brand'];
      $card['exp_month'] = $aux['card']['exp_month'];
      $card['exp_year'] = $aux['card']['exp_year'];
      $card['last4'] = $aux['card']['last4'];
    }

    /** END: STRIPE              ***** */
    /*     * *************************** */

    $uPlan = $oUser->getPlan();

    return view('/admin/usuarios/clientes/cobro', [
      'rate' => $oRates,
      'user' => $oUser,
      'importe' => ($uRates->price == null) ? $oRates->price : $uRates->price,
      'year' => $uRates->rate_year,
      'month' => $uRates->rate_month,
      'id_appointment' => $uRates->id_appointment,
      'pStripe' => $pStripe,
      'card' => $card,
      'uRate' => $uRates->id,
      'coach_id' => $uRates->coach_id,
      'coachs' => User::getCoachs(),
      'uPlan' => $uPlan
    ]);
  }

  public function informe($id, $tab = 'datos')
  {
    $year = getYearActive();
    $months = lstMonthsSpanish(false);
    unset($months[0]);
    $user = User::find($id);
    $userID = $user->id;

    $typeRates = TypesRate::pluck('name', 'id');
    $aRates = $rPrices = $rNames = [];
    //    $oRates = Rates::where('status', 1)->get();
    $oRates = Rates::all();

    if ($oRates) {
      foreach ($oRates as $k => $v) {
        $aRates[$v->id] = $v;
        $rPrices[$v->id] = $v->price;
        $rNames[$v->id] = $v->name;
        if (isset($typeRates[$v->type])) {
          $rNames[$v->id] = $typeRates[$v->type] . '<br>' . $v->name;
        }
      }
    }
    //----------------------//
    foreach ($months as $k => $v) {
      $totalUser[$k] = 0;
      $totalUserNPay[$k] = 0;
    }

    //----------------------//
    $oDates = Dates::where('id_user', $userID)->OrderBy('date')->get();
    //----------------------//
    $oNotes = UsersNotes::where('id_user', $userID)->OrderBy('created_at','DESC')->get();
    //----------------------//
    $oCharges = Charges::where('id_user', $userID)
      ->pluck('import', 'id')->toArray();

    $uLstRates = [];
    $usedRates = $detail = [];
    $uRateIds = UserRates::where('id_user', $userID)
      ->where('rate_year', $year)
      ->pluck('id_rate')->toArray();
    if ($uRateIds) {
      $uRateIds = array_unique($uRateIds);
      foreach ($uRateIds as $rid)
        if (isset($aRates[$rid]))
          $usedRates[$rid] = $aRates[$rid]->name;
    }

    $uLstRates =  [];
    if ($uRateIds) {
      for ($i = 1; $i < 13; $i++) {
        $resp = $this->getRatesByMonth($i, $year, [$id], $rPrices, $rNames);
        $uLstRates[$i] = (count($resp[0])) ? $resp[0][$id] : [];
        $totalUser[$i] = $resp[1];
        $totalUserNPay[$i] = $resp[2];
        $detail[] = $resp[3];
        $uLstRates[$i]['bonos'] = [];
      }
    }

    //*************************************************//
    //******  AGREGO LA COMPRA DE BONOS ***************//
    $usedRates['bonos'] = "BONOS";
    $lstBonos = \App\Models\Bonos::all()->pluck('name', 'id')->toArray();
    $bonoCharges = Charges::where('id_user', $userID)
      ->where('bono_id', '>', 0)
      ->whereYear('date_payment', '=', $year)
      ->get();
    foreach ($bonoCharges as $item) {
      $mounth = intVal(substr($item->date_payment, 5, 2));
      $uLstRates[$mounth]['bonos'][] =  ['price' => $item->import, 'paid' => 1, 'cid' => $item->id, 'id' => 'bono'];
    }
    //*************************************************//
    //----------------------//
    $oRatesSubsc = Rates::select('rates.*', 'types_rate.type')
      ->join('types_rate', 'rates.type', '=', 'types_rate.id')
      ->whereIn('types_rate.type', ['gral', 'pt'])->get();

    $subscrLst = $user->suscriptions;
    //----------------------//
    $aCoachs = User::whereCoachs('teach')->orderBy('name')->pluck('name', 'id')->toArray();
    $allCoachs = User::getCoachs()->pluck('name', 'id');
    //----------------------//
    // TARIFAS FIDELITY
    $uPlan = $user->getPlan();
    // Already Signed  -------------------------------------------
    $sing_contrato = false;
    if ($uPlan !== null) {
      $fileName = $user->getMetaContent('contrato_FIDELITY_' . $uPlan);
      $path = storage_path('app/' . $fileName);
      if ($fileName && File::exists($path)) {
        $sing_contrato = true;
      }
    }
    //END: Already Signed  -------------------------------------------
    //----------------------//
    //Invoices
    $invoices = \App\Models\Invoices::whereYear('date', '=', $year)
      ->where('user_id', $userID)
      ->orderBy('date', 'DESC')->get();
    $totalInvoice = $invoices->sum('total_price');
    $invoiceModal = true;
    //----------------------//
    //----------------------//
    //Invoices
    $valoracion = $this->get_valoracion($user);
    //----------------------//

    if (count($detail) > 0) {
      $aux = '';
      foreach ($detail as $item) {
        foreach ($item as $k => $d) {
          $aux .= $k . ':{';
          foreach ($d as $k2 => $i2) {
            $aux .= "$k2: '$i2',";
          }
          $aux .= '},';
        }
      }
      $detail = "{ $aux }";
    } else {
      $detail = null;
    }
    /*-------------------------------------*/
    $sEncNutri = new \App\Services\EncuestaNutriService();

    $seeClinicalHistory = null;
    if ($user->getMetaContent('hclinic_q1') != '') {
      $seeClinicalHistory = '/admin/ver-historia-clinica/' . \App\Services\LinksService::getLinkBasic($user->id);
    }
    $seeClinicalHistorySP = null;
    if ($user->getMetaContent('hclinicSP_q1') != '') {
      $seeClinicalHistorySP = '/admin/ver-historia-clinica-suelo-pelvico/' . \App\Services\LinksService::getLinkBasic($user->id);
    }

    /*-------------------------------------*/
    $photo = $user->getMetaContent('photo');
    $hasPhoto = false;
    if ($photo) {
      $path = storage_path('/app/photos/' . $photo);
      if (File::exists($path)) {
        $hasPhoto = true;
      }
    }
    /********************************/
    $lstConvenios = \App\Models\Convenios::all();
    /********************************/
    return view('/admin/usuarios/clientes/informe', [
      'aRates' => $aRates,
      'atypeRates' => $typeRates,
      'rNames' => $rNames,
      'usedRates' => $usedRates,
      'uLstRates' => $uLstRates,
      'totalUser' => $totalUser,
      'totalUserNPay' => $totalUserNPay,
      'subscrLst' => $subscrLst,
      'subscrRates' => $oRatesSubsc,
      'months' => $months,
      'detail' => $detail,
      'year' => $year,
      'user' => $user,
      'aCoachs' => $aCoachs,
      'allCoachs' => $allCoachs,
      'oDates' => $oDates,
      'oNotes' => $oNotes,
      'tab' => $tab,
      'fisioIndiba' => $this->getFileSigned($user, 'sign_fisioIndiba'),
      'sueloPelvico' => $this->getFileSigned($user, 'sign_sueloPelvico'),
      'autoInfantil' => $this->getFileSigned($user, 'contrato_autorizacion'),
      'esthetic_esthetic' => $this->getFileSigned($user, 'contrato_autorizacion_esthetic'),
      'esthetic_leform' => $this->getFileSigned($user, 'contrato_autorizacion_leform'),
      'esthetic_peeling' => $this->getFileSigned($user, 'contrato_autorizacion_peeling'),
      'esthetic_presoterapia' => $this->getFileSigned($user, 'contrato_autorizacion_presoterapia'),
      'invoices' => $invoices,
      'totalInvoice' => $totalInvoice,
      'invoiceModal' => $invoiceModal,
      'valora' => $valoracion,
      'uPlan' => $uPlan,
      'sing_contrato' => $sing_contrato,
      'u_current' => Auth::user()->id,
      'encNutr' => $sEncNutri->get_enc($user),
      'filesNutri' => $this->getFileLst($user->id, 'nutri'),
      'filesNutriG' => $this->getFileLst($user->id, 'nutriG'),
      'filesFisio' => $this->getFileLst($user->id, 'fisio'),
      'filesFisioG' => $this->getFileLst($user->id, 'fisioG'),
      'filesEsthetic' => $this->getFileLst($user->id, 'esthetic'),
      'filesEstheticG' => $this->getFileLst($user->id, 'estheticG'),
      'filesPT' => $this->getFileLst($user->id, 'pt'),
      'seeClinicalHistory' => $seeClinicalHistory,
      'seeClinicalHistorySP' => $seeClinicalHistorySP,
      'photo' => $photo,
      'hasPhoto' => $hasPhoto,
      'lstConvenios' => $lstConvenios,
    ]);
  }

  private function getFileSigned($user, $file)
  {
    $fileName = $user->getMetaContent($file);
    $already = false;
    if ($fileName) {
      $path = storage_path('/app/' . $fileName);
      $already = File::exists($path);
    }
    return $already;
  }

  public function addSubscr(Request $request)
  {
    $uID = $request->input('id');
    $rID = $request->input('id_rate');
    $price = $request->input('r_price');
    $oUser = User::find($uID);
    $oRate = Rates::find($rID);
    if (!$oUser) {
      return redirect()->action('UsersController@clientes')->withErrors(['Usuario no encontrado']);
    }
    if (!$oRate) {
      return redirect('/admin/usuarios/informe/' . $uID . '/servic')->withErrors(['Servicio no encontrada']);
    }

    $uPlan = $oUser->getPlan();
    $tarifa = $uPlan;
    if ($uPlan == 'fidelity' && $oRate->tarifa != 'fidelity') {
      $tarifa = '';
    }


    $oObj = new UserRates();
    $oObj->id_user = $uID;
    $oObj->id_rate = $rID;
    $oObj->rate_year = date('Y');
    $oObj->rate_month = date('m');
    $oObj->tarifa = $tarifa;
    $oObj->price = $price;
    $oObj->coach_id = $request->input('id_rateCoach');
    $oObj->save();

    $oObj = new UsersSuscriptions();
    $oObj->id_user = $uID;
    $oObj->id_rate = $rID;
    $oObj->price = $price;
    $oObj->tarifa = $tarifa;
    $oObj->id_coach = $request->input('id_rateCoach');
    $oObj->save();

    return redirect('/admin/usuarios/informe/' . $uID . '/servic')->with('success', $oRate->name . ' asignado a ' . $oUser->name . '.');
  }

  /**
   * 
   * @param Request $request
   * @return type
   */
  public function changeSubscr(Request $request)
  {
    $subscr_id = $request->input('subscr_id');
    $price = $request->input('price');
    $oObj = UsersSuscriptions::find($subscr_id);
    if (!is_numeric($price) || $price < 0)
      return response()->json(['error', 'El valor debe ser mayor o igual a 0€']);

    if (!$oObj || $oObj->id != $subscr_id)
      return response()->json(['error', 'Suscripcion no encontrada']);

    $oObj->price = $price;
    if ($oObj->save())  return response()->json(['OK', 'Suscripcion cambiada']);

    return response()->json(['error', 'Error al cambiar la Suscripcion']);
  }
  /**
   * Remove subscription
   * @param Request $request
   */
  public function rmSubscr($uID, $id)
  {
    $oObj = UsersSuscriptions::find($id);
    if (!$oObj || $oObj->id_user != $uID) {
      return redirect('/admin/usuarios/informe/' . $uID . '/servic')->withErrors(['Servicio no encontrada']);
    }
    $oRate = $oObj->rate;
    $oObj->delete();
    $msg = ' Se eliminó la suscripción.';
    if ($oRate) {
      $msg = 'Se eliminó la suscripción a ' . $oRate->name . '.';
    }
    return redirect('/admin/usuarios/informe/' . $uID . '/servic')->with('success', $msg);
  }

  public function unassignedMontly($idUser, $idRate, $date)
  {
    $aDate = explode('-', $date);
    if (count($aDate) != 2) {
      return redirect()->action('UsersController@clientes')->withErrors(['Periodo inválido']);
    }
    $userRate = UserRates::where('id_user', $idUser)
      ->where('id_rate', $idRate)
      ->where('active', 1)->first();

    if ($userRate) {
      $userRate->active = 0;
      $userRate->save();
      return redirect()->action('UsersController@clientes')->with('success', 'Cliente desuscripto del Servicio ' . $date);
    }

    return redirect()->action('UsersController@clientes')->withErrors(['No se ha podido desuscribir']);
  }

  public function exportClients($status = 'all', Request $request)
  {
    global $filterStatus;
    global $filterFamily;
    global $filterMonth;

    $year = getYearActive();

    $month = date('n');
    if($request->month){
      $month = $request->month;
    }

    $filterMonth = $month;
    $filterStatus = $status;
    $filterFamily = $request->family;

    //return Excel::download(new UsersExport, 'clientes_' . $year . '_' . $month . '_' . date('d') . '.xlsx');
    return Excel::download(new UsersExport, 'clientes_' . date('Y_m_d_H_i_s') . '.xlsx');
  }

  public function rateCharge(Request $request)
  {
    $stripe = null;
    $oUser = User::find($request->id_user);

    $card = null;
    $paymentMethod = $oUser->getPayCard();
    if ($paymentMethod) {
      $aux = $paymentMethod->toArray();
      $card['brand'] = $aux['card']['brand'];
      $card['exp_month'] = $aux['card']['exp_month'];
      $card['exp_year'] = $aux['card']['exp_year'];
      $card['last4'] = $aux['card']['last4'];
    }

    $rateFamily = \App\Models\Rates::getTypeRatesGroups(false);
    $uPlan = $oUser->getPlan();

    return view('admin.usuarios.clientes._rate_charge', [
      'user' => $oUser,
      'coachs' => User::getCoachs(),
      'rates' => Rates::orderBy('status', 'desc')->orderBy('name', 'asc')->get(),
      'rateFamily' => $rateFamily,
      'stripe' => $stripe,
      'card' => $card,
      'uPlan' => $uPlan
    ]);
  }

  public function addNotes(Request $request)
  {
    $uID = $request->input('uid');
    $id = $request->input('id');
    $note = $request->input('note');
    $type = $request->input('type', 'gral');
    $idCoach = $request->input('coach', Auth::user()->id);
    $oCoach = User::find($idCoach);
    $oNote = null;
    if ($id > 0)
      $oNote = UsersNotes::find($id);
    if (!$oNote) {
      $oNote = new UsersNotes();
      $oNote->id_user = $uID;
    }

    $oNote->id_coach = $idCoach;
    $oNote->profile = ($oCoach) ? $oCoach->role : '';
    $oNote->type = $type;
    $oNote->note = $note;
    $oNote->save();

    switch ($type) {
      case 'nutri':
        $urlBack = '/nutricion';
        break;
      case 'nutriG':
        $urlBack = '/nutricionG';
        break;
      case 'fisio':
        $urlBack = '/fisio';
        break;
      case 'fisioG':
          $urlBack = '/fisioG';
          break;
      case 'esthetic':
        $urlBack = '/esthetic';
        break;
      case 'estheticG':
        $urlBack = '/estheticG';
        break;
      default:
        $urlBack = '/notes';
        break;
    }

    return redirect('/admin/usuarios/informe/' . $uID . $urlBack)->with(['success' => 'Nota Guardada']);
  }

  public function delNotes(Request $request)
  {
    $uID = $request->input('uid');
    $id = $request->input('id');
    $oNote = UsersNotes::find($id);
    if ($oNote) {
      //      $urlBack = ($oNote->type == 'nutri') ? '/nutricion' : '/notes';
      switch ($oNote->type) {
        case 'nutriG':
        case 'nutri':
          $urlBack = '/nutricion';
          break;
        case 'fisio':
          $urlBack = '/fisio';
          break;
        case 'fisioG':
          $urlBack = '/fisio';
          break;
        case 'estheticG':
        case 'esthetic':
          $urlBack = '/esthetic';
          break;
        default:
          $urlBack = '/notes';
          break;
      }



      if ($oNote->delete()) {
        return redirect('/admin/usuarios/informe/' . $uID . $urlBack)->with(['success' => 'Nota eliminada']);
      }
    }

    return back()->withErrors(['Nota no eliminada']);
  }



  function downlConsent($uid, $type)
  {
    $view = $this->seeConsent($uid, $type);

    //            $fileName = str_replace(' ','-','liquidacion '.$aData['mes'].' '. strtoupper($user->name));
    //        $routePdf = storage_path('/app/liquidaciones/'. urlencode($fileName).'.pdf');
    //        $pdf = PDF::loadView('pdfs.liquidacion', $aData);
    //        $pdf->save($routePdf);

    $pdf = \Barryvdh\DomPDF\Facade::loadHTML($view);
    return $pdf->download('invoice.pdf');
  }

  function downlAutorizacion($uid, $type = '')
  {

    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    if ($type == 'autoInfantil')  $fileName = $oUser->getMetaContent('contrato_autorizacion');
    else $fileName = $oUser->getMetaContent('contrato_autorizacion_' . $type);

    if ($fileName) {
      $path = storage_path('/app/' . $fileName);
      if (File::exists($path)) {
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
      }
    }

    abort(404);
  }
  function seeConsent($uid, $type)
  {

    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $u_name = $oUser->name;
    $fileName = $oUser->getMetaContent('sign_' . $type);
    $sign = false;
    if ($fileName) {
      $path = storage_path('/app/' . $fileName);
      $sign = File::exists($path);
      if ($sign) {
        $fileName = str_replace('signs/', '', $fileName);
      }
    }
    if (!$sign) {
      die('firma');
    }
    switch ($type) {
      case 'fisioIndiba':
        $file = 'CONSENTIMIENTO-FISIOTERAPIA-CON-INDIBA';
        break;
      case 'sueloPelvico':
        $file = 'CONSENTIMIENTO-SUELO-PELVICO';
        break;
      default:
        $file = '';
        break;
    }



    return view('docs.concentimientos', [
      'fileName' => $fileName,
      'file' => $file,
      'u_name' => $u_name,
      'sign' => $sign
    ]);
  }
  function getSign($file)
  {

    $path = storage_path('/app/signs/' . $file);
    if (!File::exists($path)) {
      abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = \Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
  }


  function lstByBonos(Request $req)
  {

    $rate_type = null;
    $rate_subf = null;
    $status = isset($req->status) ? $req->status : 1;
    if ($status == 'all') {
      $sqlUsers = User::where('role', 'user');
    } else {
      $sqlUsers = User::where('role', 'user')
        ->where('status', $status);
    }

    $oUsers = $sqlUsers->orderBy('name', 'asc')->get();

    $today = date('Y-m-d');
    $uBonos = [];
    $uBonoRate = [];
    $cantByRate = [];
    if ($oUsers) {
      foreach ($oUsers as $k => $u) {
        $sqlBonos = UserBonos::where('user_id', $u->id);
        if ($rate_type) $sqlBonos->where('rate_type', $rate_type);
        if ($rate_subf) $sqlBonos->where('rate_subf', $rate_subf);
        $oBonos = $sqlBonos->get();
        if ($oBonos) {
          $aux = $aux2 = [];
          foreach ($oBonos as $b) {
            if ($b->qty < 1) continue;

            $lastPurch =  DB::select('SELECT DATEDIFF("' . $today . '", created_at) AS DateDiff FROM users_bonos_logs where user_bonos_id = ' . $b->id . ' and incr > 0 ORDER BY created_at DESC LIMIT 1;');
            if ($lastPurch && isset($lastPurch[0])) $lastPurch = $lastPurch[0]->DateDiff;
            else $lastPurch = 0;
            $aux[] = ['q' => $b->qty, 'rtype' => $b->rate_type, 'rsubf' => $b->rate_subf, 'last' => $lastPurch, 'bu_id' => $b->id];
            if ($b->rate_type) {
              $aux2[] = $b->rate_type;
              if (isset($cantByRate[$b->rate_type])) $cantByRate[$b->rate_type]++;
              else $cantByRate[$b->rate_type] = 1;
            }
            if ($b->rate_subf) {
              $aux2[] = $b->rate_subf;
              if (isset($cantByRate[$b->rate_subf])) $cantByRate[$b->rate_subf]++;
              else $cantByRate[$b->rate_subf] = 1;
            }
          }
          $uBonos[$u->id] = $aux;
          $uBonoRate[$u->id] = array_unique($aux2);
        } else {
          unset($oUsers[$k]);
        }
      }
    }
    //---------------------------------------------//

    $oUser = new User();
    $sql = DB::table('user_meta')
      ->where('meta_key', 'plan')
      ->where('meta_value', 'basic')
      ->where('created_at', '>=', date('Y-m-d', strtotime('-12 months')));

    $uPlanPenal =  $sql->pluck('user_id')->toArray();

    //---------------------------------------------//
    $aRatesIds = [];
    $typeRates = TypesRate::pluck('name', 'id');

    $oTRates = TypesRate::getWithsubfamily();
    $oFamily = TypesRate::subfamily();
    return view('/admin/usuarios/clientes/lstByBonos', [
      'users' => $oUsers,
      'uBonoRate' => $uBonoRate,
      'uBonos' => $uBonos,
      'aRatesIds' => $aRatesIds,
      'typeRates' => $typeRates,
      'cantByRate' => $cantByRate,
      'oTRates' => $oTRates,
      'oFamily' => $oFamily,
      'status' => $status,
      'uPlanPenal' => $uPlanPenal,
      'uPlan' => $oUser->getMetaUserID_byKey('plan', 'fidelity'),

    ]);
  }


  function getAltasBajas($month)
  {
    $year = getYearActive();
    $lstUsers = User::altaBajas($year, $month)->get();
    if ($lstUsers) {

      $sueloPelvico  = Rates::where('name', 'like', '%pelvico%')->pluck('id')->toArray();
      $sueloPelvico = implode(',', $sueloPelvico);
      $aUsersAlt = $aUsersBaja = [
        1 => 0, //Membresias
        2 => 0, //pt
        8 => 0, // Fisioterapia  (tood menos suelo pelvico)
        0 => 0,
        10 => 0, // nutricion
        12 => 0, // Estetica
        99 => 0 // suelo pelvico
      ];
      $rTypes =  [
        1 => 'Membresias',
        2 => 'P.T.', //
        8 => 'Fisioterapia',
        0 => 0,
        10 => 'Nutrición', // nutricion
        12 => 'Estética', // Estetica
        99 => 'Suelo Pelvico' // suelo pelvico
      ];

      $sql = 'SELECT rate_type, active, count(*) as cant FROM `user_alta_baja` WHERE `year_month`="'.$year . '-' . $month.'" GROUP BY rate_type, active;';

      $lstItems = DB::select($sql);
      foreach ($lstItems as $item) {
        if ($item->active == 0){
          $aUsersBaja[$item->rate_type] = $item->cant;
        } else {
          $aUsersAlt[$item->rate_type] = $item->cant;
        }
      }
      include_once dirname(dirname(__FILE__)) . '/Helps/AltasBajasRates.php';
    }
  }

  function getFamilyCount($month, $status)
  {
    $sql =  '';
    switch ($status) {
      case 'all':
        $sqlStatus = ' AND role = "user" ';
        $tit = 'Todos los usuarios';
        break;
      case 2:
        $tit = 'Usuarios fidelity';

        $uPlan = DB::table('user_meta')
          ->where('meta_key', 'plan')
          ->where('meta_value', 'fidelity')
          ->pluck('user_id')->toArray();
        $sqlStatus = ' AND role = "user" and users.status = 1 AND users.id IN (' . implode(',', $uPlan) . ')';
        break;
      default:
        if ($status == 0) $tit = 'Usuarios Inactivos';
        if ($status == 1) $tit = 'Usuarios Activos';
        $sqlStatus = ' AND role = "user" and users.status = ' . $status;
        break;
    }

    $year = getYearActive();
   
      $sueloPelvico  = Rates::where('name', 'like', '%pelvico%')->pluck('id')->toArray();
      $sueloPelvico = implode(',', $sueloPelvico);
      $rUsers =  [
        1 => 0, //Membresias
        2 => 0, //pt
        8 => 0, // Fisioterapia  (tood menos suelo pelvico)
        0 => 0,
        10 => 0, // nutricion
        12 => 0 // Estetica
      ];

      $date = date('Y-m-d', strtotime($year . '-' . $month . '-01' . ' -1 month'));

      $sqlDate = [];
      $monthAux = date('m', strtotime($date));
      $yearAux = date('Y', strtotime($date));
      for ($i = 0; $i < 3; $i++) {

        $sqlDate[] = "( rate_month = $monthAux AND rate_year = $yearAux)";
        $next = strtotime($date . ' +1 month');
        $date = date('Y-m-d', $next);
        $monthAux = date('m', $next);
        $yearAux = date('Y', $next);
      }

      $sqlDate = '(' . implode(' OR ', $sqlDate) . ')';


      $sql = 'SELECT users.id,rates.type 
    FROM `users`
    LEFT JOIN users_rates ON users_rates.id_user = users.id
    INNER JOIN rates ON rates.id = users_rates.id_rate 
    WHERE `users_rates`.`deleted_at` is null 
    AND users_rates.id_rate NOT  IN (' . $sueloPelvico . ') 
    AND ' . $sqlDate . $sqlStatus . '
    GROUP BY users.id,rates.type ';


      $lstUsrTypeRate = DB::select($sql);
      foreach ($lstUsrTypeRate as $u) {
        if (isset($rUsers[$u->type])) {
          $rUsers[$u->type]++;
        }
      }




      /** sueloPelvico */
      $sql = 'SELECT users.id,users.status 
      FROM `users`
      LEFT JOIN users_rates ON users_rates.id_user = users.id
      WHERE `users_rates`.`deleted_at` is null 
      AND users_rates.id_rate IN (' . $sueloPelvico . ') 
      AND ' . $sqlDate . $sqlStatus . '
      GROUP BY users.id,users.status';


      $lstUsrTypeRate = DB::select($sql);
      foreach ($lstUsrTypeRate as $u) {
        $rUsers[0]++;
      }
      /** sueloPelvico */
      include_once dirname(dirname(__FILE__)) . '/Helps/RatesFamilyCount.php';
    
  }

  public function cuotasPendientes(Request $request)
  {

    if (is_numeric($request->year)){
      $current = date('Y')+3;
      if ($request->year<=$current && $request->year>($current-6)){
          setcookie('ActiveYear', $request->year, time() + (86400 * 30), "/"); // 86400 = 1 day

          $urlRedirect = url()->current();
          if($request->fFamily){
            $urlRedirect .= '?fFamily=' . $request->fFamily;
          }

          return redirect($urlRedirect);
      }
    }

    $fFamily = null;

    if($request->fFamily){
      $fFamily = $request->fFamily;
    }

    $year = getYearActive();
    $month = date('n');

    $months = lstMonthsSpanish(false);
    unset($months[0]);
    $oUser = new User();
    $oUser->create_altaBajas($year, $month);
    $qryRates = Rates::orderBy('name', 'desc');

    if(!is_null($fFamily)) {
      $qryRates->where('type', $fFamily);
    }

    $oRates = $qryRates->get();

    $detail = [];
    $noPay = 0;
    
    $sqlUsers = User::where('role', 'user');
    

    $sqlUsers->with('userCoach');


    //$users = $sqlUsers->orderBy('name', 'asc')->get();
    $userIDs =  $sqlUsers->pluck('users.id');
    $userIDNames =  $sqlUsers->pluck('users.name', 'users.id');

    //---------------------------------------------//
    $aRates = [];
    $typeRates = TypesRate::pluck('name', 'id');
    $aRateType = [];
    //    $oRates = Rates::whereIn('type', $typeRates)->get();
    $rPrices = $rNames = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        $aRates[$r->id] = $r;
        $rPrices[$r->id] = $r->price;
        $rNames[$r->id] = $r->name;
        if (isset($typeRates[$r->type])) {
          $rNames[$r->id] = $typeRates[$r->type] . '<br>' . $r->name;
          $aRateType[$r->id] = $typeRates[$r->type];
        }
      }
    }

    //---------------------------------------------//
    $date = $year . '-01-01';
    $toPay = $uRates = [];
    $monthAux = date('m', strtotime($date));
    $yearAux = date('Y', strtotime($date));

    $data = [];

    for ($i = 1; $i <= 12; $i++) {

      if(!isset($data[$i])){
        $data[$i] = [
          'name'  => $months[$i],
          'users' => [],
          'totalDebt' => 0
        ];
      }

      $resp = $this->getRatesByMonth($monthAux, $yearAux, $userIDs, $rPrices, $rNames);
      
      foreach($resp[0] as $userId => $userSubs){
        foreach($userSubs as $userClasses){
          foreach($userClasses as $userPayments){
            if(!$userPayments['paid'] && $userPayments['price'] > 0){
              if(isset($data[$i]['users'][$userId])){
                $data[$i]['totalDebt'] += $userPayments['price'];
                $data[$i]['users'][$userId]['totalDebt'] += $userPayments['price'];
                $auxDebtDetails = $userPayments;
                $auxDebtDetails['details'] = $resp[3][$auxDebtDetails['id']];
                
                $data[$i]['users'][$userId]['debtDetails'][] = $auxDebtDetails;
              }else{
                $auxDebtDetails = $userPayments;
                $auxDebtDetails['details'] = $resp[3][$auxDebtDetails['id']];
                
                $data[$i]['totalDebt'] += $userPayments['price'];
                $data[$i]['users'][$userId] = [
                  'name'      => $userIDNames[$userId],
                  'totalDebt' => $userPayments['price'],
                  'debtDetails' => [
                    $auxDebtDetails
                  ]
                ];

              }
            }
          }
        }
      }

      $uRates[$i] = $resp[0];
      $toPay[$i] = $resp[2];
      $noPay += $resp[2];
      $detail[] = $resp[3];
      $next = strtotime($date . ' +1 month');
      $date = date('Y-m-d', $next);
      $monthAux = date('m', $next);
      $yearAux = date('Y', $next);
    }
    
    
    return view('/admin/usuarios/clientes/indexCuotasPendientes', [
      'fFamily'   => $fFamily,
      'typeRates' => $typeRates,
      'year'      => $year,
      'data'      => $data
    ]);
  }
}
