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
        if (!$month) $month = date('n');

        $year = getYearActive();
        $months = lstMonthsSpanish(false);
        unset($months[0]);
        
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
        /*******************************************************/
        $aRates = [];
        $typeRates = TypesRate::whereIn('type',['gral','pt'])->pluck('id');
        $oRates = Rates::whereIn('type',$typeRates)->get();
        if ($oRates){
            foreach ($oRates as $r){
                $aRates[$r->id] = $r;
            }
        }
        /*******************************************************/
        $arrayPaymentMonthByUser = array();
        $date = date('Y-m-d', strtotime($year . '-' . $month.'-01' . ' -1 month'));
        $payments = $uRates = $uCobros = [];
        for ($i = 0; $i < 3; $i++) {
            $aux = $this->getRatesByMonth($date,$userIDs,$aRates);
            $payments[$i] = $aux[1];
            $uRates[$i] = $aux[0];
            $uCobros[$i] = $aux[2];
            $date = date('Y-m-d', strtotime($date . ' +1 month'));
        }
        
        $aCoachs = User::where('role','teach')->orderBy('name')->pluck('name','id')->toArray();
        return view('/admin/usuarios/clientes/index', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'status' => $status,
            'payments' => $payments,
            'uRates' => $uRates,
            'uCobros' => $uCobros,
            'months' => $months,
            'aCoachs' => $aCoachs,
            'total_pending' => array_sum($arrayPaymentMonthByUser),
        ]);
    }

    public function getRatesByMonth($date,$userIDs,$aRates) {
        $pendiente = 0;
        $ratesAsign= [];
        $aCobros = [];   
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $uRates = UserRates::whereIN('id_user',$userIDs)
                ->where('rate_year',$year)
                ->where('rate_month',$month)
                ->get();
        if ($uRates && count($uRates)>0){
            
            $cobrosID = [];
            foreach ($uRates as $item){
                if ($item->id_charges)
                    $cobrosID[] = $item->id_charges;
            }
            $cobros = Charges::whereIN('id', $cobrosID)->get();
            
            if ($cobros && count($cobros)>0){  
            
                foreach ($cobros as $cobro) {
                    $aCobros[$cobro->id] = $cobro;
                }
            }
            foreach ($uRates as $item){
                if (isset($aRates[$item->id_rate])) {
                    $aux = $aRates[$item->id_rate];
                    $iRate =   (object)[
                                    'price' => $aux->price,
                                    'id'    => $aux->id,
                                    'charge'    => $item->id_charges,
                                ];
                    if (isset($ratesAsign[$item->id_user])){
                        $ratesAsign[$item->id_user][] = $iRate;
                    } else {
                        $ratesAsign[$item->id_user] = [$iRate];
                    }
                    if (!isset($aCobros[$item->id_charges]))
                    $pendiente += $aRates[$item->id_rate]->price;
                }

            }
            
        }

        return [$ratesAsign,$pendiente,$aCobros];
    }

    public function clienteRateCharge($date,$id_user,$importe,$rate){
        $oUser = User::find($id_user);
        
        $aux = explode('-', $date);
        if (count($aux) == 2){
            $year  = $aux[0];
            $month = $aux[1];
        } else {
            $year = getYearActive();
            $month = date('m');
        }
        
        
        $uRates = UserRates::where('id_user',$id_user)
            ->where('rate_year',$year)
            ->where('rate_month',$month)
            ->where('id_rate',$rate)
            ->first();
        if (!$uRates){
            return view('admin.popup_msg',['msg'=>'Servicio no asignada']);
        }
        /******************************/
        /** STRIPE              ******/
        $data = [$year,$month,$id_user,$importe*100,$rate];
        $sStripe = new \App\Services\StripeService();
        $pStripe = $sStripe->getPaymentLink('rate',$data);
        $card = null;
        $paymentMethod = $oUser->paymentMethods()->first();
        if ($paymentMethod){
            $aux = $paymentMethod->toArray();
            $card['brand'] = $aux['card']['brand'];
            $card['exp_month'] = $aux['card']['exp_month'];
            $card['exp_year'] = $aux['card']['exp_year'];
            $card['last4'] = $aux['card']['last4'];
        }
        
        /** STRIPE              ******/
        /******************************/
        return view('/admin/usuarios/clientes/cobro', [
                'rate'    => Rates::find($rate),
                'date'    => $date,
                'user'    => $oUser,
                'importe' => $importe,
                'year'    => $year,
                'month'   => $month,
                'pStripe' => $pStripe,
                'card' => $card,
        ]);
    }
        
    public function informe($id,$tab='datos') {
        $year = getYearActive();
        $months = lstMonthsSpanish(false);
        unset($months[0]);
        $user = User::find($id);
        $userID = $user->id;
        
        $aRates = [];
        $oRates = Rates::where('status',1)->get();
        
        if ($oRates){
            foreach ($oRates as $k=>$v){
                $aRates[$v->id] = $v;
            }
        }
        /*********************************************************/
        foreach ($months as $k=>$v){
            $totalUser[$k] = 0;
            $totalUserNPay[$k] = 0;
        }
        
        /*********************************************************/
        $oDates = Dates::where('id_user',$userID)->OrderBy('date')->get();
        /*********************************************************/
        $oNotes = UsersNotes::where('id_user',$userID)->OrderBy('created_at')->get();
        /*********************************************************/
        $oCharges = Charges::where('id_user',$userID)
                ->pluck('import','id')->toArray();
       
        $uLstRates = [];
        $uLstRatesNoPay = [];
        $uRates = UserRates::where('id_user',$userID)
                ->where('rate_year',$year)->get();
        $usedRates = [];
        
        if ($uRates){
            foreach ($uRates as $k=>$v){
                if (isset($aRates[$v->id_rate])){
                    if (!isset($totalUser[$v->rate_month])){
                        $totalUser[$v->rate_month] = 0;
                        $totalUserNPay[$v->rate_month] = 0;
                    }
                    if (!isset($uLstRates[$v->id_rate])){
                        $uLstRates[$v->id_rate] = [];
                        $uLstRatesNoPay[$v->id_rate] = [];
                    }
                    $idRate = $v->id_rate;
                    $rate = $aRates[$idRate];
                    // si esta pagado, lo busco luego
                    
                    $auxCharges = ($v->id_charges) ? Charges::find($v->id_charges) : null;
                    
                    if ($auxCharges){
                        if (!isset($uLstRates[$idRate][$v->rate_month]))
                            $uLstRates[$idRate][$v->rate_month] = [];

                        $uLstRates[$idRate][$v->rate_month][] = $auxCharges;
                        $totalUser[$v->rate_month] += $auxCharges->import;
                    } else {
                       if (!isset($uLstRatesNoPay[$idRate][$v->rate_month]))
                            $uLstRatesNoPay[$idRate][$v->rate_month] = [];
                       $uLstRatesNoPay[$idRate][$v->rate_month][] = [
                            'price' =>$rate->price,
                            'id' =>$v->id_rate,
                            'date' => $v->rate_year.'-'.$v->rate_month,
                               ];
                       $totalUserNPay[$v->rate_month] += $rate->price;
                    }
                    
                    $usedRates[$v->id_rate] = $aRates[$v->id_rate]->name;
                }
            }
        }
        /*********************************************************/
        
        $uCurrentRates = [];
                
        $aRatesID = Rates::join('types_rate','rates.type','=','types_rate.id')
                ->whereIn('types_rate.type',['gral','pt'])->pluck('rates.id');
        $cUserRates = UserRates::where('id_user',$userID)
                ->where('active',1)
                ->whereIn('id_rate',$aRatesID)->get();
        if ($cUserRates){
            foreach ($cUserRates as $k=>$v){
                if (isset($aRates[$v->id_rate]))
                $uCurrentRates[$v->id_rate] = $aRates[$v->id_rate];
            }
        }       
        
        /*********************************************************/
        $aCoachs = User::where('role','teach')->orderBy('name')->pluck('name','id')->toArray();
        $allCoachs = User::all()->pluck('name','id')->toArray();
        $coachID = $user->userCoach()->select('id_coach')->first();
        if($coachID) $coachID = $coachID->id_coach;
        /*********************************************************/
        $path = storage_path('/app/signs/'.$userID.'.png');
        $alreadySign = File::exists($path);
        /*********************************************************/
        
        return view('/admin/usuarios/clientes/informe', [
            'aRates' => $aRates,
            'usedRates' => $usedRates,
            'uLstRates' => $uLstRates,
            'uLstRatesNoPay' => $uLstRatesNoPay,
            'totalUser' => $totalUser,
            'totalUserNPay' => $totalUserNPay,
            'uCurrentRates' => $uCurrentRates,
            'months' => $months,
            'year' => $year,
            'user' => $user,
            'coachID' => $coachID,
            'aCoachs' => $aCoachs,
            'allCoachs' => $allCoachs,
            'oDates' => $oDates,
            'oNotes' => $oNotes,
            'tab' => $tab,
            'alreadySign' => $alreadySign,
        ]);
    }

    public function addRate(Request $request) {
        $uID = $request->input('id');
	$rID = $request->input('id_rate');
        $oUser = User::find($uID);
        $oRate = Rates::find($rID);
        if (!$oUser){
            return redirect()->action('UsersController@clientes')->withErrors(['Usuario no encontrado']);
        }
        if (!$oRate){
            return redirect('/admin/usuarios/informe/'.$uID.'/servic')->withErrors(['Servicio no encontrada']);
        }
       
        $oObj = new UserRates();
        $oObj->id_user = $uID;
        $oObj->id_rate = $rID;
        $oObj->rate_year = date('Y');
        $oObj->rate_month = date('m');
        $oObj->save();
                    
        return redirect('/admin/usuarios/informe/'.$uID.'/servic')->with('success',$oRate->name.' asignado a '.$oUser->name.'.');
    }
    
    public function unassignedMontly($idUser, $idRate, $date) {
        $aDate = explode('-', $date);
        if (count($aDate) != 2){
            return redirect()->action('UsersController@clientes')->withErrors(['Periodo inválido']);
        }
        $userRate = UserRates::where('id_user', $idUser)
                        ->where('id_rate', $idRate)
                        ->where('active',1)->first();
        
        if ($userRate){
            $userRate->active = 0;
            $userRate->save();
            return redirect()->action('UsersController@clientes')->with('success','Cliente desuscripto del Servicio '.$date);
        }
   
        return redirect()->action('UsersController@clientes')->withErrors(['No se ha podido desuscribir']);
    }
    
    public function exportClients(){
		$array_excel   = [];
		$array_excel[] = [
			'Nombre',
			'Email',
			'Telefono',
			'Estado',
                        'Servicios'
		];
                
                $aRates = Rates::all()->pluck('name','id')->toArray();
                $oUserRates = UserRates::where('active',1)->get();
                $aUserRates = [];
                if ($oUserRates){
                    foreach ($oUserRates as $i){
                        if (!isset($aUserRates[$i->id_user]))
                            $aUserRates[$i->id_user] = [];
                        if (isset($aRates[$i->id_rate])){
                            $aUserRates[$i->id_user][] = $aRates[$i->id_rate];
                        }
                    }
                    foreach ($aUserRates as $k=>$v)
                        $aUserRates[$k] = array_unique ($aUserRates[$k]);
                }
//                dd($aUserRates);

		\Maatwebsite\Excel\Facades\Excel::create('clientes', function ($excel) use ($array_excel,$aUserRates) {

			$excel->sheet('clientes_activos_inactivos', function ($sheet) use ($array_excel,$aUserRates) {

				$users = User::where('role', 'user')->get();

				foreach ($users as $user)
				{
                                    $serv = isset($aUserRates[$user->id]) ? implode(', ', $aUserRates[$user->id]) : '';
					$array_excel[] = [
						$user->name,
						$user->email,
						$user->telefono,
						$user->status ? 'ACTIVO' : 'NO ACTIVO',
                                                $serv
					];
				}

				$sheet->fromArray($array_excel,null,'A1',false,false); 

			});
                        
		})->export('xls');

	}
        
            
    public function rateCharge(Request $request) {
        $stripe = null;
        $oUser = User::find($request->id_user);
                
        $card = null;
        $paymentMethod = $oUser->paymentMethods()->first();
        if ($paymentMethod){
            $aux = $paymentMethod->toArray();
            $card['brand'] = $aux['card']['brand'];
            $card['exp_month'] = $aux['card']['exp_month'];
            $card['exp_year'] = $aux['card']['exp_year'];
            $card['last4'] = $aux['card']['last4'];
        }
        return view('admin.usuarios.clientes._rate_charge', [
            'user'  => $oUser,
            'rates' => Rates::orderBy('status', 'desc')->orderBy('name', 'asc')->get(),
            'stripe'=> $stripe,
            'card'  => $card
        ]);
    }
    public function addNotes(Request $request) {
        $uID = $request->input('uid');
        $id = $request->input('id');
        $note = $request->input('note');
        $oNote = null;
        if ($id>0) $oNote = UsersNotes::find($id);
        if (!$oNote){
            $oNote = new UsersNotes();
            $oNote->id_coach   = Auth::user()->id;
            $oNote->type   = Auth::user()->role;
            $oNote->id_user	 = $uID;
        }
        
        
        $oNote->note = $note;
        $oNote->save();
        
        return redirect('/admin/usuarios/informe/'.$uID.'/notes')->with(['success'=>'Nota Guardada']);
    }
    public function delNotes(Request $request) {
        $uID = $request->input('uid');
        $id = $request->input('id');
        $oNote = UsersNotes::find($id);
        if ($oNote){
            if ($oNote->delete()){
                return redirect('/admin/usuarios/informe/'.$uID.'/notes')->with(['success'=>'Nota eliminada']);
            }
        }
                
        return redirect('/admin/usuarios/informe/'.$uID.'/notes')->withErrors(['Nota no eliminada']);
        
    }
    public function addSign(Request $request) {
        $uID = $request->input('uid');
        $sign = $request->input('sign');
        $encoded_image = explode(",", $sign)[1];
        $decoded_image = base64_decode($encoded_image);
        $fileName = 'signs/'.$uID.'.png';
        $path = storage_path('/app/' . $fileName);

        $storage = Storage::disk('local');
        $storage->put($fileName,$decoded_image);
//        file_put_contents("signature.png", $decoded_image);
        return redirect('/admin/usuarios/informe/'.$uID.'/consent')->with(['success'=>'Firma Guardada']);
    }
    
    function getSign($uid){
      
        $path = storage_path('/app/signs/'.$uid.'.png');
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
