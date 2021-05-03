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

trait ClientesTraits {

  public function clientes(Request $request, $month = false) {
    if (!$month)
      $month = date('n');

    $year = getYearActive();
    $months = lstMonthsSpanish(false);
    unset($months[0]);

    $payments = $noPay = 0;
    $status = isset($request->status) ? $request->status : 1;
    if ($status == 'all') {
      $sqlUsers = User::where('role', 'user');
    } else {
      $sqlUsers = User::where('role', 'user')
              ->where('status', $status);
    }
    $sqlUsers->with('userCoach');
    $users = $sqlUsers->orderBy('name', 'asc')->get();
    $userIDs = $sqlUsers->pluck('id');
    /*     * **************************************************** */
    $aRates = [];
    $typeRates = TypesRate::whereIn('type', ['gral', 'pt'])->pluck('id');
    $oRates = Rates::all();
//    $oRates = Rates::whereIn('type', $typeRates)->get();
    $rPrices = [];
    if ($oRates) {
      foreach ($oRates as $r) {
        $aRates[$r->id] = $r;
        $rPrices[$r->id] = $r->price;
      }
    }

    /*     * **************************************************** */
    $arrayPaymentMonthByUser = array();
    $date = date('Y-m-d', strtotime($year . '-' . $month . '-01' . ' -1 month'));
    $toPay = $uRates = $uCobros = [];
    $monthAux = date('m', strtotime($date));
    for ($i = 0; $i < 3; $i++) {
      $resp = $this->getRatesByMonth($monthAux, $year, $userIDs, $rPrices);
      $uRates[$i] = $resp[0];
      $toPay[$i] = $resp[2];
      $noPay += $resp[2];
      $next = strtotime($date . ' +1 month');
      $date = date('Y-m-d', $next);
      $monthAux = date('m', $next);
    }


    $aCoachs = User::where('role', 'teach')->orderBy('name')->pluck('name', 'id')->toArray();
    return view('/admin/usuarios/clientes/index', [
        'users' => $users,
        'month' => $month,
        'year' => $year,
        'status' => $status,
        'toPay' => $toPay,
        'uRates' => $uRates,
        'months' => $months,
        'aCoachs' => $aCoachs,
        'total_pending' => array_sum($arrayPaymentMonthByUser),
    ]);
  }

  public function getRatesByMonth($month, $year, $userIDs, $rPrices) {

    $RateIDs = array_keys($rPrices);
    $uRates = UserRates::whereIN('id_user', $userIDs)
            ->where('rate_year', $year)
            ->where('rate_month', $month)
            ->whereIn('id_rate', $RateIDs)
            ->get();

    $payments = $noPay = 0;
    $uLstRates = [];
    if ($uRates) {
      /*       * ******************************* */
      $charges = [];
      foreach ($uRates as $k => $v) {
        if ($v->id_charges)
          $charges[] = $v->id_charges;
      }
      $aCharges = Charges::whereIn('id', $charges)->pluck('import', 'id')->toArray();
      /*       * ******************************* */
      foreach ($uRates as $k => $v) {
        $idRate = $v->id_rate;
        $idUser = $v->id_user;
        if (!isset($uLstRates[$idUser])) {
          $uLstRates[$idUser] = [];
        }
        if (!isset($uLstRates[$idUser][$idRate])) {
          $uLstRates[$idUser][$idRate] = [];
        }

        // si esta pagado, lo busco luego
        $auxCharges = isset($aCharges[$v->id_charges]) ? $aCharges[$v->id_charges] : null;
        if ($auxCharges) {
          $uLstRates[$idUser][$idRate][] = [
              'price' => $auxCharges,
              'id' => $v->id,
              'paid' => true,
              'cid' => $v->id_charges,
              'appointment' => $v->id_appointment
          ];
          $payments += $auxCharges;
        } else {
          $importe = ($v->price == null) ? $rPrices[$idRate]:$v->price;
          $noPay += $importe;
          
          $uLstRates[$idUser][$idRate][] = [
              'price' => $importe,
              'id' => $v->id,
              'paid' => false,
              'cid' => -1,
              'appointment' => $v->id_appointment
          ];
        }
      }
    }
    return [$uLstRates,$payments,$noPay];
  }

  public function clienteRateCharge($uRateID) {
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
    $paymentMethod = $oUser->paymentMethods()->first();
    if ($paymentMethod) {
      $aux = $paymentMethod->toArray();
      $card['brand'] = $aux['card']['brand'];
      $card['exp_month'] = $aux['card']['exp_month'];
      $card['exp_year'] = $aux['card']['exp_year'];
      $card['last4'] = $aux['card']['last4'];
    }

    /** END: STRIPE              ***** */
    /*     * *************************** */
    return view('/admin/usuarios/clientes/cobro', [
        'rate' => $oRates,
        'user' => $oUser,
        'importe' => ($uRates->price == null) ? $oRates->price : $uRates->price,
        'year' => $uRates->rate_year,
        'month' => $uRates->rate_month,
        'pStripe' => $pStripe,
        'card' => $card,
        'uRate' => $uRates->id,
    ]);
  }

  public function informe($id, $tab = 'datos') {
    $year = getYearActive();
    $months = lstMonthsSpanish(false);
    unset($months[0]);
    $user = User::find($id);
    $userID = $user->id;

    $aRates = $rPrices = [];
    $oRates = Rates::where('status', 1)->get();

    if ($oRates) {
      foreach ($oRates as $k => $v) {
        $aRates[$v->id] = $v;
        $rPrices[$v->id] = $v->price;
      }
    }
    /*     * ****************************************************** */
    foreach ($months as $k => $v) {
      $totalUser[$k] = 0;
      $totalUserNPay[$k] = 0;
    }

    /*     * ****************************************************** */
    $oDates = Dates::where('id_user', $userID)->OrderBy('date')->get();
    /*     * ****************************************************** */
    $oNotes = UsersNotes::where('id_user', $userID)->OrderBy('created_at')->get();
    /*     * ****************************************************** */
    $oCharges = Charges::where('id_user', $userID)
                    ->pluck('import', 'id')->toArray();

    $uLstRates = [];
    $usedRates = [];
    $uRateIds = UserRates::where('id_user', $userID)
                    ->where('rate_year', $year)
                    ->pluck('id_rate')->toArray();
    if ($uRateIds){
      $uRateIds = array_unique($uRateIds);
      foreach ($uRateIds as $rid)
        if (isset ($aRates[$rid]))
          $usedRates[$rid] = $aRates[$rid]->name;
    }
    
    $uLstRates =  [];
    if ($uRateIds) {
      for ($i = 1; $i < 13; $i++) {
        $resp = $this->getRatesByMonth($i, $year, [$id], $rPrices);
        $uLstRates[$i] = (count($resp[0])) ? $resp[0][$id] : [];
        $totalUser[$i] = $resp[1];
        $totalUserNPay[$i] = $resp[2];
      }
    }
    
    /*     * ****************************************************** */
    $oRatesSubsc = Rates::select('rates.*', 'types_rate.type')
                    ->join('types_rate', 'rates.type', '=', 'types_rate.id')
                    ->whereIn('types_rate.type', ['gral', 'pt'])->get();

    $subscrLst = $user->suscriptions;
    /*     * ****************************************************** */
    $aCoachs = User::where('role', 'teach')->orderBy('name')->pluck('name', 'id')->toArray();
    $allCoachs = User::all()->pluck('name', 'id')->toArray();
    /*     * ****************************************************** */
    $path = storage_path('/app/signs/' . $userID . '.png');
    $alreadySign = File::exists($path);
    /*     * ****************************************************** */

    return view('/admin/usuarios/clientes/informe', [
        'aRates' => $aRates,
        'usedRates' => $usedRates,
        'uLstRates' => $uLstRates,
        'totalUser' => $totalUser,
        'totalUserNPay' => $totalUserNPay,
        'subscrLst' => $subscrLst,
        'subscrRates' => $oRatesSubsc,
        'months' => $months,
        'year' => $year,
        'user' => $user,
        'aCoachs' => $aCoachs,
        'allCoachs' => $allCoachs,
        'oDates' => $oDates,
        'oNotes' => $oNotes,
        'tab' => $tab,
        'alreadySign' => $alreadySign,
    ]);
  }

  public function addSubscr(Request $request) {
    $uID = $request->input('id');
    $rID = $request->input('id_rate');
    $oUser = User::find($uID);
    $oRate = Rates::find($rID);
    if (!$oUser) {
      return redirect()->action('UsersController@clientes')->withErrors(['Usuario no encontrado']);
    }
    if (!$oRate) {
      return redirect('/admin/usuarios/informe/' . $uID . '/servic')->withErrors(['Servicio no encontrada']);
    }

    $oObj = new UserRates();
    $oObj->id_user = $uID;
    $oObj->id_rate = $rID;
    $oObj->rate_year = date('Y');
    $oObj->rate_month = date('m');
    $oObj->price = $oRate->price;
    $oObj->save();

    $oObj = new \App\Models\UsersSuscriptions();
    $oObj->id_user = $uID;
    $oObj->id_rate = $rID;
    $oObj->id_coach = $request->input('id_rateCoach');
    $oObj->save();

    return redirect('/admin/usuarios/informe/' . $uID . '/servic')->with('success', $oRate->name . ' asignado a ' . $oUser->name . '.');
  }

  /**
   * Remove subscription
   * @param Request $request
   */
  public function rmSubscr($uID, $id) {
    $oObj = \App\Models\UsersSuscriptions::find($id);
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

  public function unassignedMontly($idUser, $idRate, $date) {
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

  public function exportClients() {
    $array_excel = [];
    $array_excel[] = [
        'Nombre',
        'Email',
        'Telefono',
        'Estado',
        'Servicios'
    ];

    $aRates = Rates::all()->pluck('name', 'id')->toArray();
    $oUserRates = UserRates::where('active', 1)->get();
    $aUserRates = [];
    if ($oUserRates) {
      foreach ($oUserRates as $i) {
        if (!isset($aUserRates[$i->id_user]))
          $aUserRates[$i->id_user] = [];
        if (isset($aRates[$i->id_rate])) {
          $aUserRates[$i->id_user][] = $aRates[$i->id_rate];
        }
      }
      foreach ($aUserRates as $k => $v)
        $aUserRates[$k] = array_unique($aUserRates[$k]);
    }
//                dd($aUserRates);

    \Maatwebsite\Excel\Facades\Excel::create('clientes', function ($excel) use ($array_excel, $aUserRates) {

      $excel->sheet('clientes_activos_inactivos', function ($sheet) use ($array_excel, $aUserRates) {

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
          $serv = isset($aUserRates[$user->id]) ? implode(', ', $aUserRates[$user->id]) : '';
          $array_excel[] = [
              $user->name,
              $user->email,
              $user->telefono,
              $user->status ? 'ACTIVO' : 'NO ACTIVO',
              $serv
          ];
        }

        $sheet->fromArray($array_excel, null, 'A1', false, false);
      });
    })->export('xls');
  }

  public function rateCharge(Request $request) {
    $stripe = null;
    $oUser = User::find($request->id_user);

    $card = null;
    $paymentMethod = $oUser->paymentMethods()->first();
    if ($paymentMethod) {
      $aux = $paymentMethod->toArray();
      $card['brand'] = $aux['card']['brand'];
      $card['exp_month'] = $aux['card']['exp_month'];
      $card['exp_year'] = $aux['card']['exp_year'];
      $card['last4'] = $aux['card']['last4'];
    }
    return view('admin.usuarios.clientes._rate_charge', [
        'user' => $oUser,
        'rates' => Rates::orderBy('status', 'desc')->orderBy('name', 'asc')->get(),
        'stripe' => $stripe,
        'card' => $card
    ]);
  }

  public function addNotes(Request $request) {
    $uID = $request->input('uid');
    $id = $request->input('id');
    $note = $request->input('note');
    $oNote = null;
    if ($id > 0)
      $oNote = UsersNotes::find($id);
    if (!$oNote) {
      $oNote = new UsersNotes();
      $oNote->id_coach = Auth::user()->id;
      $oNote->type = Auth::user()->role;
      $oNote->id_user = $uID;
    }


    $oNote->note = $note;
    $oNote->save();

    return redirect('/admin/usuarios/informe/' . $uID . '/notes')->with(['success' => 'Nota Guardada']);
  }

  public function delNotes(Request $request) {
    $uID = $request->input('uid');
    $id = $request->input('id');
    $oNote = UsersNotes::find($id);
    if ($oNote) {
      if ($oNote->delete()) {
        return redirect('/admin/usuarios/informe/' . $uID . '/notes')->with(['success' => 'Nota eliminada']);
      }
    }

    return redirect('/admin/usuarios/informe/' . $uID . '/notes')->withErrors(['Nota no eliminada']);
  }

  public function addSign(Request $request) {
    $uID = $request->input('uid');
    $sign = $request->input('sign');
    $encoded_image = explode(",", $sign)[1];
    $decoded_image = base64_decode($encoded_image);
    $fileName = 'signs/' . $uID . '.png';
    $path = storage_path('/app/' . $fileName);

    $storage = Storage::disk('local');
    $storage->put($fileName, $decoded_image);
//        file_put_contents("signature.png", $decoded_image);
    return redirect('/admin/usuarios/informe/' . $uID . '/consent')->with(['success' => 'Firma Guardada']);
  }

  function getSign($uid) {

    $path = storage_path('/app/signs/' . $uid . '.png');
    if (!File::exists($path)) {
      abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = \Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
  }

}
