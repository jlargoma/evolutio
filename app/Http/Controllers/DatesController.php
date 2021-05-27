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
        $object = Dates::find($id);
        if ($object){
          if ($object->date_type != 'pt'){
            /*Busca y elimina el user_rate*/
            $uRate = $object->uRates;
            if ($uRate){
              /*Buscar y elimnar cobro*/
              $charge = $uRate->charges;
              if ($charge) $charge->delete();
              $uRate->delete();
            }
          }
          $object->delete();
          return redirect()->back();
        }
    }

    public function create(Request $request) {
      
      $ID = $request->input('idDate', null);
      $blocked = $request->input('blocked', null);
      $id_user = $request->input('id_user',null);
      $uEmail  = $request->input('email');
      $uPhone  = $request->input('phone');
      $type    = $request->input('date_type');
      $importe = $request->input('importe',0);
      $id_rate = $request->input('id_rate');
      $id_coach= $request->input('id_coach');
      $date    = $request->input('date');
      $hour    = $request->input('hour');
      $cHour   = $request->input('customTime');
      $timeCita= strtotime($date);
      /*********************************************************************/
      $oCarbon = Carbon::createFromFormat('d-m-Y H:00:00', "$date $hour:00:00");
      $date_compl = $oCarbon->format('Y-m-d H:i:00');  
      /*********************************************************************/
      if ($blocked){
        $alreadyExit = Dates::where('date', $date_compl)
                        ->where('id', '!=', $ID)
                        ->where('id_coach', $id_coach)
                        ->first();
        if ($alreadyExit) {
          $msg = 'Personal ocupado';
          return redirect()->back()->withErrors([$msg]);
        }
        $oObj = Dates::find($ID);
        $oObj->id_coach = $id_coach;
        $oObj->date = $date_compl;
        $oObj->updated_at = $date;
        $oObj->save();
        return redirect()->back();
      }
      /*********************************************************************/
      $validated = $this->validate($request, [
          'date' => 'required',
          'id_rate' => 'required',
          'id_coach' => 'required',
              ], [
          'date.required' => 'Fecha requerida',
          'id_rate.required' => 'Tarifa requerida',
          'id_coach.required' => 'Coach requerido',
      ]);
      /************************************************************/
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

        /************************************************************ */
        $alreadyExit = Dates::where('date', $date_compl)
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
        /************************************************************ */
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
        /************************************************************ */
        if ($ID) {
          $oObj = Dates::find($ID);
        } else {
          $oObj = new Dates();
        }
        /************************************************************ */
        //nueva cita => crear userRate
        $id_user_rates = null;
        if ($type == 'pt'){
          $uRate = UserRates::where('id_user', $oUser->id)
                    ->where('id_rate', $id_rate)
                    ->where('rate_month', date('n', $timeCita))
                    ->where('rate_year', date('Y', $timeCita))
                    ->first();
          
        } else {
          $uRate = UserRates::find($oObj->id_user_rates);
          if ($uRate){
            $uRate->rate_year = date('Y', $timeCita);
            $uRate->rate_month = date('n', $timeCita);
            $uRate->price = $importe;
            $uRate->save();
          }else{
            
            $uRate = new UserRates();
            $uRate->id_user = $oUser->id;
            $uRate->id_rate = $id_rate;
            $uRate->rate_year = date('Y', $timeCita);
            $uRate->rate_month = date('n', $timeCita);
            $uRate->price = $importe;
            $uRate->save();
          }
        }
        if (!$uRate)
          return redirect()->back()->withErrors(['Servicio no encontrado']);
        
        $id_user_rates = $uRate->id;
        /************************************************************ */
        if ($ID) {
            $oObj = Dates::find($ID);
        } else {
            $oObj = new Dates();
        }
        
        $oObj->id_rate = $id_rate;
        $oObj->id_user = $id_user;
        $oObj->id_coach = $id_coach;
        $oObj->id_user_rates = $id_user_rates;
        $oObj->date_type = $type;
        $oObj->date = $date_compl;
        $oObj->customTime = $cHour;
        $oObj->updated_at = $date;
        if ($oObj->save()) {
          if ($type == 'pt'){
            return redirect('/admin/citas-pt/edit/'.$oObj->id);
          }
          /*************************************************************** */
            $timeCita = strtotime($oObj->date);
            $service = Rates::find($oObj->id_rate);
            $coach = User::find($oObj->id_coach);

            /******************************************/
            //crear el pago
            $pStripe = null;
            $oRate = Rates::find($oObj->id_rate);
            if (!$ID) {
              $data = [$oObj->id,$oUser->id,$importe*100,$oRate->id];
              $sStripe = new \App\Services\StripeService();
              $rType = \App\Models\TypesRate::find($oRate->type);
              $pStripe = url($sStripe->getPaymentLink($rType->type,$data));
            }
        
            /**************************************************** */
           
            /**************************************************/
            $subjet = 'Nueva cita en Evolutio';
            if ($ID) $subjet = 'Actualización de su cita';
            MailController::sendEmailPayDateByStripe($oObj, $oUser, $oRate,$coach,$pStripe,$importe,$subjet);
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

        $uRate = $oDates->uRates;
        if (!$uRate){
          if ($ajax) return "Cita no encontada";
          else return redirect()->back()->with(['error' => 'Cita no encontada']);
        }
        $oUser = $uRate->user;
        $service = $oDates->service;
        $oRate = $uRate->rate;
        $payType = $req->input('type_payment');
        if (!$oRate) {
            if ($ajax)
                return "Tarifa no encontada";
            else
                return redirect()->back()->with(['error' => 'Tarifa no encontada']);
        }

            $value = $uRate->price;
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
            $oCobro->date_payment = date('Y-m-d');
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
            $uRate->id_charges = $oCobro->id;
            $uRate->save();
            /*******************************************/
            $dataMail = [
                  'fecha_pago' => date('Y-m-d'),
                  'type_payment' => $payType,
                  'importe' => $value,
              ];
            MailController::sendEmailPayRate($dataMail, $oUser, $oRate);
            if ($ajax) return "OK";
            else return redirect()->back()->with(['success' => 'Cobro guadado']);
            
    }

    function openChargeDate($id){
      $obj = Dates::find($id);
      if (!$obj){
        UserRates::where('id_appointment', $id)->delete();
        echo 'Registro no encontrado.';
      } else {
        $date_type = $obj->date_type;
        switch ($date_type){
          case 'nutri':
            header('Location: /admin/citas-nutricion/edit/'.$id);
            exit();
            break;
          case 'fisio':
            header('Location: /admin/citas-fisioterapia/edit/'.$id);
            exit();
            break;
        }
        dd($obj);
      }
    }
    
    function blockDates($type){
      $coachs = \App\Services\CitasService::getCoachs($type);
      $cNames = [];
      if ($coachs) {
        foreach ($coachs as $item) {
          $cNames[$item->id] = $item->name;
        }
      }
      
      return view('calendars.blockDates', [
          'coachs' => $cNames,
          'type' => $type
              ]);
    
    }
    function blockDatesSave(Request $req){
      
      $type = $req->input('date_type');
      $id_coach = $req->input('id_coach');
      $start = $req->input('start');
      $end = $req->input('end');
      $hours = $req->input('hours');
     
      $startTime = null;
      $aux = explode('-', $start);
      if (is_array($aux) && count($aux)==3)
        $startTime = strtotime($aux[2].'-'.$aux[1].'-'.$aux[0]);
      
      $endTime = null;
      $aux = explode('-', $end);
      if (is_array($aux) && count($aux)==3)
        $endTime = strtotime($aux[2].'-'.$aux[1].'-'.$aux[0]);
      
      $oneDay = 24*60*60;
      if ($startTime && $endTime){
        while ($startTime<=$endTime){
          if (date('w',$startTime)>0){
            foreach ($hours as $h){
              $oObj = new Dates();
              $oObj->id_coach = $id_coach;
              $oObj->id_rate = 0;
              $oObj->id_user = 0;
              $oObj->blocked = 1;
              $oObj->id_user_rates = -1;
              $oObj->date_type = $type;
              $oObj->date = date('Y-m-d',$startTime)." $h:00:00";
              $oObj->save();
            }
          }
          
          $startTime += $oneDay;
        }
      }
      return redirect()->back()->with(['success'=>'Horarios bloqueados']);
    
    }
    
    
    
    function cloneDates($id){
      $obj= Dates::find($id);
      $cNames = [];
      $uRate = $obj->uRates;
      
      $id_coach = $obj->id_coach;
      $alreadyUsed = [];
      
      $start = substr($obj->date,0,10);
      $oCalendar = new \App\Services\CalendarService($start);
      $oCalendar->setLastDayWeeks(6);
      $calendar = $oCalendar->getCalendarWeeks();
      
      $rslt = \App\Services\CitasService::get_calendars($calendar['firstDay'],$calendar['lastDay'],null,$id_coach,$obj->date_type);
//      dd($rslt);
      
      $rslt['calendar'] = $calendar['days'];
      $rslt['obj'] = $obj;
      $rslt['uRate'] = $uRate;
      
      $times = [];
      return view('calendars.cloneDates', $rslt);
    
    }
    function cloneDatesSave(Request $req){
      $datelst = $req->input('datelst');
      $idDate = $req->input('idDate');
      
      $aux = explode(';', $datelst);
      $aDates = [];
      if (is_array($aux)){
        foreach ($aux as $d){
          if (!empty($d)){
            $aux2 = explode('-', $d);
            $aDates[] = date('Y-m-d H',($aux2[0]+($aux2[1]*3600))).':00:00';
          }
        }
      }
      
      $oDate = Dates::find($idDate);
      $uRate = $oDate->uRates;
      
      foreach ($aDates as $d){
        if ($oDate->date_type == 'pt'){
          $id_user_rates = $oDate->id_user_rates;
        } else {
          $timeCita = strtotime($d);
          $urClone = new UserRates();
          $urClone->id_user = $uRate->id_user;
          $urClone->id_rate = $uRate->id_rate;
          $urClone->rate_year = date('Y', $timeCita);
          $urClone->rate_month = date('n', $timeCita);
          $urClone->price = $uRate->price;
          $urClone->save();
          $id_user_rates = $urClone->id;
        }
        
        $clone = new Dates();
        $clone->date = $d;
        $clone->id_rate = $oDate->id_rate;
        $clone->id_user = $oDate->id_user;
        $clone->id_coach = $oDate->id_coach;
        $clone->date_type = $oDate->date_type;
        $clone->id_user_rates = $id_user_rates;
        $clone->save();
      }
      
//      switch ($oDate->date_type){
//          case 'nutri':
//            header('Location: /admin/citas-nutricion/edit/'.$idDate);
//            exit();
//            break;
//          case 'fisio':
//            header('Location: /admin/citas-fisioterapia/edit/'.$idDate);
//            exit();
//            break;
//        }
      return redirect()->back()->with(['success'=>'Citas Creadas']);
    }
}
