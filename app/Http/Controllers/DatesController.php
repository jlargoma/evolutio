<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\User;
use App\Models\Dates;
use App\Models\Rates;
use App\Models\UserRates;
use App\Models\CoachTimes;
use Illuminate\Support\Facades\Mail;

class DatesController extends Controller {

    public function index($month = "") {

        if ($month == "") {
            $month = Carbon::now()->startOfMonth();
            $startWeek = Carbon::now()->startOfWeek();
            $endWeek = Carbon::now()->endOfWeek();
        } else {
            $startWeek = Carbon::createFromFormat('Y-m-d', $month)->startOfWeek();
            $endWeek = Carbon::createFromFormat('Y-m-d', $month)->endOfWeek();

            $month = Carbon::createFromFormat('Y-m-d', $month)->startOfMonth();
        }

        return view('admin/dates/index', [
            'month' => $month,
            'week' => $startWeek,
            'selectedWeek' => $startWeek->format("W")
        ]);
    }

    public function delete($id) {
        $date = Dates::find($id);
        if ($date->delete()) {
            return redirect()->back();
        }
    }

    public function create(Request $request) {

        $validated = $this->validate($request, [
            'date' => 'required',
            'id_rate' => 'required',
            'id_coach' => 'required',
                ], [
            'date.required' => 'Fecha requerida',
            'id_rate.required' => 'Tarifa requerida',
            'id_coach.required' => 'Coach requerido',
        ]);
        
        $ID = $request->input('idDate', null);
        
        /*********************************************************************/
        $id_user = $request->input('id_user',null);
        $uEmail = $request->input('email');
        $uPhone = $request->input('phone');
        
        if (!$id_user){
          $issetUser = User::where('email',$uEmail)->first();
          if ($issetUser) {
            return redirect()->back()->withErrors(["email duplicado"])->withInput();
          } else {
            $oUser = new User();
            $oUser->name = $request->input('u_name');
            $oUser->email = $uEmail;
            $oUser->password = str_random(60); //bcrypt($request->input('password'));
            $oUser->remember_token = str_random(60);
            $oUser->role = 'user';
            $oUser->telefono = $uPhone;
            $oUser->save();
            $id_user = $oUser->id;
          }
        } else {
          $oUser = User::find($id_user);
          
          if ($oUser && $oUser->email != $uEmail) {
              $oUser->email = $uEmail;
              $oUser->save();
          }
          if ($uPhone && $oUser->telefono != $uPhone) {
              $oUser->telefono = $uPhone;
              $oUser->save();
          }
        }
        /*********************************************************************/
        
        $id_coach = $request->input('id_coach');
        $oCarbon = Carbon::createFromFormat('d-m-Y H:00:00', $request->input('date') . " " . $request->input('hour') . ":00:00");
        $date = $oCarbon->format('Y-m-d H:i:00');

        /*         * *********************************************************** */
        $alreadyExit = Dates::where('date', $date)
                        ->where('id', '!=', $ID)
                        ->where(function ($query) use ($id_user, $id_coach) {
                            $query->where('id_user', $id_user)
                            ->orWhere('id_coach', $id_coach);
                        })->first();
        if ($alreadyExit) {
            if ($alreadyExit->id_user == $id_user)
                $msg = 'Usuario ocupado';
            if ($alreadyExit->id_coach == $id_coach)
                $msg = 'Personal ocupado';
            return redirect()->back()->withErrors([$msg]);
        }
        /*         * *********************************************************** */
        $coachTimes = CoachTimes::where('id_coach', $id_coach)->first();
        if ($coachTimes) {
            $t_control = json_decode($coachTimes->times, true);
            $aux_d = $oCarbon->format('w');
            $aux_h = $oCarbon->format('H');
            if (isset($t_control[$aux_d])) {
                if (isset($t_control[$aux_d][$aux_h]) && $t_control[$aux_d][$aux_h] == 0)
                    return redirect()->back()->withErrors(['Horario no disponible']);
            }
        }
        /*         * *********************************************************** */
        if ($ID) {
            $oObj = Dates::find($ID);
        } else {
            $oObj = new Dates();
        }
        $type=$request->input('date_type');
        $oObj->id_rate = $request->input('id_rate');
        $oObj->price = $request->input('importe',0);
        $oObj->id_user = $id_user;
        $oObj->id_coach = $id_coach;
        $oObj->date_type = $type;
        $oObj->date = $date;
        $oObj->created_at = $date;
        if (!$ID) {
            $oObj->charged = 0;
            $oObj->status = 0;
            $oObj->updated_at = $date;
        }
        if ($oObj->save()) {
           
          if ($type == 'pt'){
            $oObj->charged = 1;
            $oObj->status = 1;
            $oObj->save();
            return redirect('/admin/citas-pt/edit/'.$oObj->id);
          }
            /*             * ************************************************************** */
            $timeCita = strtotime($oObj->date);
            $service = Rates::find($oObj->id_rate);
            $coach = User::find($oObj->id_coach);

            /******************************************/
            //crear el pago
            $pStripe = null;
            $oRate = Rates::find($oObj->id_rate);
            $importe = $oObj->price;
            if (!$ID) {
              $data = [$oObj->id,$oUser->id,$oObj->price*100,$oRate->id];
              $sStripe = new \App\Services\StripeService();
              $rType = \App\Models\TypesRate::find($oRate->type);
              $pStripe = url($sStripe->getPaymentLink($rType->type,$data));
            }
        
            /******************************************/
            MailController::sendEmailPayDateByStripe($oObj, $oUser, $oRate,$coach,$pStripe,$importe);
            /*             * ************************************************************** */
          if ($type == 'nutri') return redirect('/admin/citas-nutricion/edit/'.$oObj->id);
          if ($type == 'fisio') return redirect('/admin/citas-fisioterapia/edit/'.$oObj->id);
        }
    }

  
    public function chargeAdvanced(Request $req) {

        $ajax = $req->ajax();
        $oDates = Dates::find($req->idDate);
        if (!$oDates) {
            if ($ajax)
                return "Cita no encontada";
            else
                return redirect()->back()->with(['error' => 'Cita no encontada']);
        }

        $oUser = $oDates->user;
        $service = $oDates->service;
        $oRate = Rates::find($req->input('id_rate'));
        $payType = $req->input('type_payment');
        if (!$oRate) {
            if ($ajax)
                return "Tarifa no encontada";
            else
                return redirect()->back()->with(['error' => 'Tarifa no encontada']);
        }

            $value = $oDates->price;
            $idStripe=null;$cStripe=null;
            if ($payType == 'card'){
                 
                $cc_number = $req->input('cc_number', null);
                $cc_expide_mm = $req->input('cc_expide_mm', null);
                $cc_expide_yy = $req->input('cc_expide_yy', null);
                $cc_cvc = $req->input('cc_cvc', null);
                $cardLoaded = $req->input('cardLoaded', null);
                $sStripe = new \App\Services\StripeService();
                
                /***********************************/
                /** GUARDAR TARJETA **/
                /***********************************/
                if ($cardLoaded == 0){
                    $validate = \App\Services\StripeCardValidation::validate($req);
                    if ($validate !== 'OK'){
                        return redirect()->back()
                                ->withErrors($validate)
                                ->withInput();
                    }
                    $resp = $sStripe->subscription_changeCard($oUser, $cc_number, $cc_expide_mm, $cc_expide_yy, $cc_cvc);
                    if ( $resp != 'updated'){
                        return redirect()->back()
                                ->withErrors([$resp])
                                ->withInput();
                    }
                }
                /***********************************/
                /** COBRAR POR STRIPE **/
                /***********************************/
                $resp = $sStripe->automaticCharge($oUser,round($value*100));
                if ( $resp[0] != 'OK'){
                    return redirect()->back()
                            ->withErrors([$resp])
                            ->withInput();
                }
                $idStripe = $resp[1];
                $cStripe = $resp[2];
            }
            
            /*******************************************/
            //Save payment
            $time = strtotime($oDates->date);
            $oCobro = new \App\Models\Charges();
            $oCobro->id_user = $oUser->id;
            $oCobro->date_payment = date('Y-m-d',$time);
            $oCobro->id_rate = $oRate->id;
            $oCobro->type_payment = $payType;
            $oCobro->type = 1;
            $oCobro->import = $value;
            $oCobro->discount = 0;
            $oCobro->type_rate = $oRate->type;
            $oCobro->id_stripe = $idStripe;
            $oCobro->customer_stripe = $cStripe;
            $oCobro->save();
            
            /*--------------------------------*/
            $oUserRate = UserRates::where('id_appointment', $oDates->id)->first();
            if (!$oUserRate) {
            //si no tenia asignada la tarifa del mes
              $oUserRate = new UserRates();
              $oUserRate->id_user = $oUser->id;
              $oUserRate->id_rate = $oRate->id;
              $oUserRate->rate_year = date('Y',$time);
              $oUserRate->rate_month = date('n',$time);
              $oUserRate->id_charges = $oCobro->id;
              $oUserRate->id_appointment = $oDates->id;
            }
            $oUserRate->id_charges = $oCobro->id;
            $oUserRate->save();
            /*******************************************/
            $dataMail = [
                  'fecha_pago' => date('Y-m-d'),
                  'type_payment' => $payType,
                  'importe' => $value,
              ];
            MailController::sendEmailPayRate($dataMail, $oUser, $oRate);
            // Actualizamos la cita
            $oDates->status = 1;
            $oDates->charged = 1;
            $oDates->id_charges = $oCobro->id;

        if ($oDates->save()) {
            if ($ajax)
                return "OK";
            else
                return redirect()->back()->with(['success' => 'Cobro guadado']);
        } else {
            if ($ajax)
                return "No se pudo guardar el cobro";
            else
                return redirect()->back()->with(['error' => 'No se pudo guardar el cobro']);
        }
    }

}
