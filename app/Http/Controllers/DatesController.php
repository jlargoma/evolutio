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
    if ($object) {
      if ($object->date_type != 'pt') {
        /* Busca y elimina el user_rate */
        $uRate = $object->uRates;
        if ($uRate) {
          /* Buscar y elimnar cobro */
          $charge = $uRate->charges;
          if ($charge){

            $sBonos = new \App\Services\BonoService();
            //devuelvo el bono
            $sBonos->restoreBonoCharge($charge->id);

            $charge->delete();
          }
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
    $id_user = $request->input('id_user', null);
    $uEmail = $request->input('email');
    $uPhone = $request->input('phone');
    $type = $request->input('date_type');
    $importe = $request->input('importe', 0);
    $id_rate = $request->input('id_rate');
    $id_coach = $request->input('id_coach');
    $date = $request->input('date');
    $hour = $request->input('hour');
    $cHour = $request->input('customTime');
    $observ = $request->input('observ');
    $isGroup = ($request->input('is_group') == 'on');
    $senial = $request->input('senial');
    $time_type = $request->input('time_type');
    $convenio = $request->input('convenio');
    $timeCita = strtotime($date);
    /* -------------------------------------------------------------------- */
    $oCarbon = Carbon::createFromFormat('d-m-Y H:00:00', "$date $hour:00:00");
    $date_compl = $oCarbon->format('Y-m-d H:i:00');
    
    $aux_week = $oCarbon->format('w');
    if ($aux_week == 0) return redirect()->back()->withErrors(['Domingos no habilitados']);
    /* -------------------------------------------------------------------- */
    if ($blocked && !$isGroup) {
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

      $motive = $request->input('motive','');
      if (trim($motive) != ''){
        $oObj->setMetaContent('motive', $motive);
      }  else {
        $oObj->delMetaContent('motive');
      }     
      
      return redirect()->back();
    }
    /* -------------------------------------------------------------------- */
    $validated = $this->validate($request, [
        'date' => 'required',
        'id_rate' => 'required',
        'id_coach' => 'required',
            ], [
        'date.required' => 'Fecha requerida',
        'id_rate.required' => 'Tarifa requerida',
        'id_coach.required' => 'Coach requerido',
    ]);
    /* -------------------------------------------------------------------- */
    $alreadyExit = Dates::where('date', $date_compl)
                    ->where('id', '!=', $ID)
                    ->where('date_type', $type)
                    ->where('blocked', 1)
                    ->where('id_coach', $id_coach)->first();
    if ($alreadyExit) {
      return redirect()->back()->withErrors(['Horario bloqueado']);
    }
    $alreadyExit = Dates::where('date', $date_compl)
                    ->where('id', '!=', $ID)
                    ->where('id_coach', $id_coach)->get();
    if ($alreadyExit){
      $aux = 0;
      foreach($alreadyExit as $v){
        $aux++;
        if($v->time_type == 'double') return redirect()->back()->withErrors(['Personal ocupado']);
      }
      if ($aux > 1) {
        return redirect()->back()->withErrors(['Personal ocupado']);
      }
    }
    /* -------------------------------------------------------------------- */
    if (!$isGroup) {
      if (!$id_user) {
        $issetUser = User::where('email', $uEmail)->first();
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
          if(isset($convenio)) {
            $oUser->convenio = $convenio;
          }
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

      $alreadyExit = Dates::where('date', $date_compl)
                      ->where('id', '!=', $ID)
                      ->where('id_user', $id_user)->count();
      if ($alreadyExit > 1) {
        return redirect()->back()->withErrors(['Usuario ocupado']);
      }
    }
    /* -------------------------------------------------------------------- */
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
    /* -------------------------------------------------------------------- */
    if ($ID) {
      $oObj = Dates::find($ID);
    } else {
      $oObj = new Dates();
    }
    /* -------------------------------------------------------------------- */
    //nueva cita => crear userRate
    $id_user_rates = null;
    if (!$isGroup) {
      if ($type == 'pt') {
        $uRate = UserRates::where('id_user', $oUser->id)
                ->where('id_rate', $id_rate)
                ->where('rate_month', date('n', $timeCita))
                ->where('rate_year', date('Y', $timeCita))
                ->first();
        if (!$uRate)
          $uRate = \App\Services\ValoracionService::getURate($oUser->id, $id_rate, $timeCita, $id_coach);
      } else {
        $uRate = UserRates::find($oObj->id_user_rates);
        if ($uRate) {
          $uRate->rate_year = date('Y', $timeCita);
          $uRate->rate_month = date('n', $timeCita);
          $uRate->price = $importe;
          $uRate->id_rate = $id_rate;
          $uRate->coach_id = $id_coach;
          $uRate->save();
        } else {

          $uRate = new UserRates();
          $uRate->id_user = $oUser->id;
          $uRate->id_rate = $id_rate;
          $uRate->rate_year = date('Y', $timeCita);
          $uRate->rate_month = date('n', $timeCita);
          $uRate->price = $importe;
          $uRate->coach_id = $id_coach;
          $uRate->save();
        }
      }
      if (!$uRate)
        return redirect()->back()->withErrors(['Servicio no encontrado']);

      $id_user_rates = $uRate->id;
    }
    /* -------------------------------------------------------------------- */
    $hasSenial = $oObj->getMetaContent('senial_price');
    if($hasSenial) $senial = 2;
    //dd($hasSenial,$senial);
    /* -------------------------------------------------------------------- */
    $oObj->id_rate = $id_rate;
    $oObj->id_user = $id_user;
    $oObj->id_coach = $id_coach;
    $oObj->id_user_rates = $id_user_rates;
    $oObj->date_type = $type;
    $oObj->date = $date_compl;
    $oObj->customTime = $cHour;
    $oObj->updated_at = $date;
    $oObj->senial = $senial;
    if ($type == 'pt') {
      $ratesInfo = Rates::where('id', $id_rate)->first();

      $dateAppointment = new \DateTime($date);

      // Set the DateTime object to the beginning of the week 
      $initialDay = clone $dateAppointment;
      $initialDay->modify('this week');

      // Set the DateTime object to the end of the week 
      $lastDay = clone $initialDay;
      $lastDay->modify('this week +6 days');

      // Format the dates if needed
      $initialDayFormatted = $initialDay->format('Y-m-d');
      $lastDayFormatted = $lastDay->format('Y-m-d');

      $thisWeekRatesBuilder = Dates::select('id_rate', 'id_coach', 'date')
                                  ->where('id_rate', $id_rate)
                                  ->where('id_user', $id_user)
                                  ->where('recuperacion', 0)
                                  ->where('date', '>=' , $initialDayFormatted . ' 00:00:00')
                                  ->where('date', '<=' , $lastDayFormatted . ' 23:59:59');

      $thisWeekRates = [];
      
      if(isset($ID) && $ID) {
        $thisWeekRates = $thisWeekRatesBuilder->where('id', '!=', $ID)->get()->toArray();
      } else {
        $thisWeekRates = $thisWeekRatesBuilder->get()->toArray();
      }
      
      if(count($thisWeekRates) >= $ratesInfo->max_pax) {
        $oObj->recuperacion = 1;
      } else {
        $oObj->recuperacion = 0;
      }
      
    }
    $oObj->time_type = $time_type;

    if ($isGroup) {
      $oObj->price = $importe;
      $oObj->id_user = 0;
      $oObj->blocked = 1;
      $oObj->is_group = 1;
      $oObj->save();

      if ($type == 'pt')
        return redirect('/admin/citas-pt/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'nutri')
        return redirect('/admin/citas-nutricion/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'nutriG')
        return redirect('/admin/citas-nutricion-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'fisio')
        return redirect('/admin/citas-fisioterapia/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'fisioG')
        return redirect('/admin/citas-fisioterapia-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'esthetic')
        return redirect('/admin/citas-estetica/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'estheticG')
        return redirect('/admin/citas-estetica-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
    }



    if ($oObj->save()) {
      $timeCita = strtotime($oObj->date);
      $service = Rates::find($oObj->id_rate);
      $coach = User::find($oObj->id_coach);
      $oRate = Rates::find($oObj->id_rate);

      /* ------- BEGIN: extrIDs ------------- */
      
      $oObj->setMetaContent('extrs',$request->input('extrIDs'));
      /* ------- BEGIN: prepare iCAL ------------- */
      $uID = str_pad($oObj->id, 7, "0", STR_PAD_LEFT);
      $invite = new \App\Services\InviteICal($uID);
      $dateTime = $oObj->date;
      if ($oObj->customTime) {
        $dateTime = explode(' ', $oObj->date);
        $dateTime = $dateTime[0] . ' ' . $oObj->customTime;
      }
      $dateZone = 'Europe/Madrid';
      //$dateZone = 'America/Argentina/Buenos_Aires';
      $dateStart = new \DateTime($dateTime, new \DateTimeZone($dateZone));
      $dateEnd = new \DateTime($dateTime, new \DateTimeZone($dateZone));
      $dateEnd->modify('+1 hours');
      $dateStart->setTimezone(new \DateTimeZone('UTC'));
      $dateEnd->setTimezone(new \DateTimeZone('UTC'));
      $icsDetail = 'Tienes una cita con tu ';
      switch ($type) {
        case 'nutri':
          $icsDetail .= 'Nutricionista ';
          break;
        case 'nutriG':
          $icsDetail .= 'Nutricionista ';
        break;
        case 'fisio':
          $icsDetail .= 'Fisioterapeuta ';
          break;
        case 'fisioG':
          $icsDetail .= 'Fisioterapeuta ';
          break;
        case 'pt':
          $icsDetail .= 'Entrenador ';
          break;
        case 'estheticG':
        case 'esthetic':
          $icsDetail .= 'Estética ';
          break;
      }
      $icsDetail .= $coach->name;
      $invite->setSubject($oRate->name)
              ->setDescription($icsDetail)
              ->setStart($dateStart)
              ->setEnd($dateEnd)
              ->setCreated(new \DateTime());

      //Set location GETAFE if needed
      if(in_array($type, ['fisioG', 'nutriG', 'estheticG'])){
        $invite->setLocation('C. Morse, 57, 28906 Getafe, Madrid');
      }

      $calFile = $invite->save();
      /** END:  prepare iCAL * */
      /* -------------------------------------------------------------------- */
      if ($type == 'pt') {


        $subjet = 'Nueva cita en Evolutio';
        if ($ID)
          $subjet = 'Actualización de su cita';

        MailController::sendEmailPayDateByStripe($oObj, $oUser, $oRate, $coach,null, $importe, $subjet, $calFile);
        return redirect('/admin/citas-pt/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      }
      /* -------------------------------------------------------------------- */
      //crear el pago
      $pStripe = null;
      $uRate = $oObj->uRates;
      $charge = ($uRate) ? $uRate->charges : null;
      if (!$charge) {
        $isSenial = ($senial == 1);
        if($isSenial) $importe = 15;
        $data = [$oObj->id, $oUser->id, $importe * 100, $oRate->id];
        $sStripe = new \App\Services\StripeService();
        $rType = \App\Models\TypesRate::find($oRate->type);
        $pStripe = url($sStripe->getPaymentLink($rType->type, $data,$isSenial));
      }
      /* -------------------------------------------------------------------- */
      /* -------------------------------------------------------------------- */
      $subjet = 'Nueva cita en Evolutio';
      if ($ID)
        $subjet = 'Actualización de su cita';

      MailController::sendEmailPayDateByStripe($oObj, $oUser, $oRate, $coach, $pStripe, $importe, $subjet, $calFile, $type);
      /* -------------------------------------------------------------------- */

      $roomID = $request->input('id_room');
      $oObj->setMetaContent('room', $roomID);
      $oObj->setMetaContent('observ', $observ);
      
      $equipments = ['ecogr', 'indiba', 'equip_a', 'equip_b', 'equip_c' ];
      foreach($equipments as $eq){ $oObj->setMetaContent($eq, 0); }
      
      $equipments = $request->input('equipments',[]);
      foreach($equipments as $eq){$oObj->setMetaContent($eq, 1); }
      /* -------------------------------------------------------------------- */

      if ($type == 'nutri')
        return redirect('/admin/citas-nutricion/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'nutriG')
        return redirect('/admin/citas-nutricion-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'fisio')
        return redirect('/admin/citas-fisioterapia/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'fisioG')
        return redirect('/admin/citas-fisioterapia-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'esthetic')
        return redirect('/admin/citas-estetica/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
      if ($type == 'estheticG')
        return redirect('/admin/citas-estetica-getafe/edit/' . $oObj->id)->with(['success'=>'Registro guardado']);
    }
  }

  public function chargeAdvanced(Request $req) {
    $oDates = Dates::find($req->idDate);
    if (!$oDates)
      return redirect()->back()->with(['error' => 'Cita no encontada']);

    $uRate = $oDates->uRates;
    if (!$uRate)
      return redirect()->back()->with(['error' => 'Cita no encontada']);

    $oUser = $uRate->user;
    $service = $oDates->service;
    $oRate = $uRate->rate;
    $payType = $req->input('type_payment');
    if (!$oRate)
      return redirect()->back()->with(['error' => 'Tarifa no encontada']);


    $value = $uRate->price;
    $idStripe = null;
    $cStripe = null;
    $UserBonos = null;
    if ($payType == 'card') {
      //--- NUEVA TARJETA ---------------------------------------//
      if ($req->input('cardLoaded') == 0) {
        $CardService = new \App\Services\CardService();
        $resp = $CardService->processCard($oUser, $req);
        if ($resp !== 'OK')
          return back()->withErrors($resp)->withInput();
      }
      //--- COBRAR POR STRIPE ---------------------------------------//
      $sStripe = new \App\Services\StripeService();
      $resp = $sStripe->automaticCharge($oUser, round($value * 100));
      if ($resp[0] !== 'OK') {
        if ($resp[0] == '3DS') {
          \App\Models\Stripe3DS::addNew($oUser->id, $resp[1], $resp[2], 'cita', ['dID' => $oDates->id]);
          return redirect()->route(
                          'cashier.payment',
                          [$resp[1], 'redirect' => 'resultado']
          );
        } else {
          return redirect()->back()
                          ->withErrors([$resp[1]])
                          ->withInput();
        }
      }
      $idStripe = $resp[1];
      $cStripe = $resp[2];
    }//END CARD
    //--- COBRAR POR BONO ---------------------------------------//
    $bono_qty = 1;
    if ($payType == 'bono') {
      $bonoID = $req->input('id_bono', 0);
      $UserBonos = \App\Models\UserBonos::find($bonoID);
      if (!$UserBonos)
        return redirect()->back()->withErrors(['Bono no encontrado'])->withInput();

      $bono_qty = 1;
      $extrs = $oDates->getMetaContent('extrs');
      if ($extrs && trim($extrs) != ''){
        $extrs = explode(',',$extrs);
        $bono_qty = 1 + count($extrs); // con servicios extras
      }
      $resp = $UserBonos->check($oUser->id,$bono_qty);
      if ($resp != 'OK')
        return redirect()->back()
                        ->withErrors([$resp])
                        ->withInput();

      $value = 0;
    } //END BONO
    //---------------------------------//
    //Save payment
    $ChargesDate = new \App\Services\ChargesDateService();
    $ChargesDate->generatePayment($oDates, $payType, $value, $idStripe, $cStripe, $UserBonos,$bono_qty);
    return redirect()->back()->with(['success' => 'Cobro guadado']);
  }

  function openChargeDate($id) {
    $obj = Dates::find($id);
    if (!$obj) {
      UserRates::where('id_appointment', $id)->delete();
      echo 'Registro no encontrado.';
    } else {
      $date_type = $obj->date_type;
      switch ($date_type) {
        case 'nutri':
          header('Location: /admin/citas-nutricion/edit/' . $id);
          exit();
          break;
        case 'nutriG':
          header('Location: /admin/citas-nutricion-getafe/edit/' . $id);
          exit();
          break;
        case 'fisio':
          header('Location: /admin/citas-fisioterapia/edit/' . $id);
          exit();
          break;
        case 'fisioG':
          header('Location: /admin/citas-fisioterapia-getafe/edit/' . $id);
          exit();
          break;
        case 'esthetic':
          header('Location: /admin/citas-estetica/edit/' . $id);
          exit();
          break;
        case 'estheticG':
          header('Location: /admin/citas-estetica-getafe/edit/' . $id);
          exit();
          break;
      }
      dd($obj);
    }
  }

  function blockDates($type) {
    $coachs = \App\Services\CitasService::getCoachs($type);
    $cNames = [];
    if ($coachs) {
      foreach ($coachs as $item) {
        $cNames[$item->id] = $item->name;
      }
    }

    return view('calendars.blockDates', [
        'type' => $type,
        'coachs' => $cNames,
        'aWeeks' => listDaysSpanish()
    ]);
  }

  function blockDatesSave(Request $req) {

    $type = $req->input('date_type');
    $type_action = $req->input('type_action');
    $start = $req->input('start');
    $end = $req->input('end');
    $hours = $req->input('hours');
    $uIDs = $req->input('user_ids');
    $id_coach = $req->input('id_coach');
    $wDays = $req->input('wDay');
    $motive = $req->input('motive');
    if (trim($motive) == '') $motive = null;
    if (!$uIDs && $id_coach) $uIDs = [$id_coach]; 

    if (!$uIDs)
      return back()->withErrors(['Debe seleccionar al menos un Coach']);
    if (!$start)
      return back()->withErrors(['Debe seleccionar al menos la fecha de inicio']);

    $startTime = null;
    $aux = explode('-', $start);
    if (is_array($aux) && count($aux) == 3)
      $startTime = ($aux[2] . '-' . $aux[1] . '-' . $aux[0]);

    $endTime = null;
    $aux = explode('-', $end);
    if (is_array($aux) && count($aux) == 3)
      $endTime = ($aux[2] . '-' . $aux[1] . '-' . $aux[0]);

    $aDays = arrayDays($startTime, $endTime, 'Y-m-d', 'w');
    if (!$hours) {
      $hours = [];
      for ($i = 8; $i <= 22; $i++)
        $hours[] = $i;
    }
    foreach ($aDays as $d => $wd) {
      if ($wd > 0) {
        if ($wDays && !in_array($wd, $wDays))
          continue;
        foreach ($hours as $h) {
          $dateHour = $d . " $h:00:00";
          foreach ($uIDs as $uID) {
            $exist = Dates::where('id_coach', $uID)
                            ->where('date_type', $type)
                            ->where('date', $dateHour)->first();

            //bloqueo de fechas
            if ($type_action == 'block' && !$exist) {
              $oObj = new Dates();
              $oObj->id_coach = $uID;
              $oObj->id_rate = 0;
              $oObj->id_user = 0;
              $oObj->blocked = 1;
              $oObj->id_user_rates = -1;
              $oObj->date_type = $type;
              $oObj->date = $dateHour;
              $oObj->save();
              if ($motive){
                $oObj->setMetaContent('motive', $motive);
              }
            }

            //desbloqueo de fechas
            if ($type_action == 'unblock' && $exist) {
              if ($exist->blocked == 1)
                $exist->delete();
            }
          }
        }
      }
    }
    return redirect()->back()->with(['success' => 'Horarios bloqueados']);
  }

  function cloneDates($id) {
    $obj = Dates::find($id);
    $cNames = [];
    $uRate = $obj->uRates;

    $id_coach = $obj->id_coach;
    $alreadyUsed = [];

    $start = substr($obj->date, 0, 10);
    $oCalendar = new \App\Services\CalendarService($start);
    $oCalendar->setLastDayWeeks(6);
    $calendar = $oCalendar->getCalendarWeeks();

    $rslt = \App\Services\CitasService::get_calendars($calendar['firstDay'], $calendar['lastDay'], null, $id_coach, $obj->date_type);
//      dd($rslt);

    $rslt['calendar'] = $calendar['days'];
    $rslt['obj'] = $obj;
    $rslt['uRate'] = $uRate;

    $times = [];
    return view('calendars.cloneDates', $rslt);
  }

  function cloneDatesSave(Request $req) {
    $datelst = $req->input('datelst');
    $idDate = $req->input('idDate');

    $aux = explode(';', $datelst);
    $aDates = [];
    if (is_array($aux)) {
      foreach ($aux as $d) {
        if (!empty($d)) {
          $aux2 = explode('-', $d);
          $aDates[] = date('Y-m-d H', ($aux2[0] + ($aux2[1] * 3600))) . ':00:00';
        }
      }
    }

    $oDate = Dates::find($idDate);
    $uRate = $oDate->uRates;

    foreach ($aDates as $d) {
      if ($oDate->date_type == 'pt') {
        $has = UserRates::where('id_user', $oDate->id_user)
                        ->where('id_rate', $oDate->id_rate)
                        ->where('active', 1)->first();
        if (!$has) {
          return redirect()->back()->with(['error' => 'Servicio no habilitado']);
        }
        $id_user_rates = $oDate->id_user_rates;
      } else {
        $timeCita = strtotime($d);
        $urClone = new UserRates();
        $urClone->id_user = $uRate->id_user;
        $urClone->id_rate = $uRate->id_rate;
        $urClone->rate_year = date('Y', $timeCita);
        $urClone->rate_month = date('n', $timeCita);
        $urClone->price = $uRate->price;
        $urClone->coach_id = $uRate->coach_id;
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
    return redirect()->back()->with(['success' => 'Citas Creadas']);
  }

  public function checkDateDisp(Request $req) {

    $date = $req->input('date');
    $time = $req->input('time');
    $ID = $req->input('id');
    $uID = $req->input('uID');
    $type = $req->input('type');
    $cID = $req->input('cID'); //id_coach


    $aux = explode('-', $date);
    if (is_array($aux) && count($aux) == 3) {
      $date = $aux[2] . '-' . $aux[1] . '-' . $aux[0];
    }

    $dateCompl = $date . " $time:00:00";

    $sqlCoach = Dates::where('date', $dateCompl)->where('id_coach', $cID);
    $sqlUser = Dates::where('date', $dateCompl)->where('id_user', $uID);
    $sqlBloq = Dates::where('date', $dateCompl)->where('id_coach', $cID);

    if ($ID && $ID != 'undefined') {
      $sqlCoach->where('id', '!=', $ID);
      $sqlBloq->where('id', '!=', $ID);
      $sqlUser->where('id', '!=', $ID);
    }

    if ($sqlBloq->where('date_type', $type)->where('blocked', 1)->first()) {
      return 'bloqueo';
    }

    $useCoach = $sqlCoach->count();
    $useUser = $sqlUser->count();

//    dd($useCoach,$useUser,$req->all());
    return ($useCoach > $useUser) ? $useCoach : $useUser;
  }


  public function checkDispCoaches(Request $req) {

    $date = $req->input('date');
    $time = $req->input('time');
    $type = $req->input('type');
    $dateID = $req->input('id');

    // $date = '20-07-2022';
    // $time = '10';
    // $type = 'nutri';


    $aux = explode('-', $date);
    if (is_array($aux) && count($aux) == 3) {
      $date = $aux[2] . '-' . $aux[1] . '-' . $aux[0];
    }

    $dateCompl = $date . " $time:00:00";


    $oCoachs =  \App\Services\CitasService::getCoachs($type)->pluck('id');
    $aCoachs = [];
    if($oCoachs){
      foreach($oCoachs as $d){
        $aCoachs[$d] = 0;
      }
    }

    $week = date('N',strtotime($dateCompl));
    $coachTimes = \App\Models\CoachTimes::whereIn('id_coach', $oCoachs)->pluck('times', 'id_coach');
    
    if ($coachTimes) {
      $time = intval($time);
      foreach ($coachTimes as $idCoach => $t) {
        $times = json_decode($t, true);
        if (is_array($times)) {
          if (isset($times[$week]) && isset($times[$week][$time])){
            if ($times[$week][$time] == 0) $aCoachs[$idCoach] = 'no';
          } else {
            $aCoachs[$idCoach] = 'no';
          }
        }
      }
    }

    $sqlCoach = Dates::where('date', $dateCompl)->whereIn('id_coach', $oCoachs);
    $sqlBloq = Dates::where('date', $dateCompl)->whereIn('id_coach', $oCoachs);
    

    $useCoach = $sqlCoach->get();
    if($useCoach){
      foreach($useCoach as $d){
        if (isset($aCoachs[$d->id_coach]) && is_numeric($aCoachs[$d->id_coach])){
          $aCoachs[$d->id_coach]++;
          if($d->time_type == 'double') $aCoachs[$d->id_coach] = 'blocked';
        }
      }
    }

    $useCoach = $sqlBloq->where('date_type', $type)->where('blocked', 1)->get();

    if($useCoach){
      foreach($useCoach as $d){
        $aCoachs[$d->id_coach] = 'blocked';
      }
    }



    
    $equipments = ['ecogr', 'indiba', 'equip_a', 'equip_b', 'equip_c' ];
    $data = ['room'=>[],'ecogr'=>0, 'indiba'=>0, 'equip_a'=>0, 'equip_b'=>0, 'equip_c'=>0 ];
    
    $inUse = Dates::select('meta_key','appointment.id','meta_value')->LeftJoin('appointment_meta', function ($join) {
          $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
          }) ->where('date', $dateCompl)
          ->where('appointment.id', '!=', $dateID)
          ->where('meta_value', 1)->whereIn('meta_key', $equipments)->get();
    if ($inUse){
      foreach($inUse as $i){
        if (isset($data[$i->meta_key])){
          $data[$i->meta_key]++;
        }
      }
    }

    $data['room'] = Dates::LeftJoin('appointment_meta', function ($join) {
      $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
      })->where('date', $dateCompl)->where('appointment.id', '!=', $dateID)->where('meta_key', 'room')->pluck('meta_value');

    $data['aCoachs'] = $aCoachs;

    return $data;
  }



  public function createMultip(Request $request) {

    $dates = $request->input('dates', null);
    $times = $request->input('times', null);
    $id_rate = $request->input('id_rate');
    $id_coach = $request->input('id_coach');

    /* -------------------------------------------------------------------- */
    $validated = $this->validate($request, [
        'dates' => 'required',
        'id_rate' => 'required',
        'id_coach' => 'required',
        'times' => 'required',
            ], [
        'dates.required' => 'Fecha requerida',
        'id_rate.required' => 'Tarifa requerida',
        'id_coach.required' => 'Coach requerido',
        'times.required' => 'Horarios requeridos',
    ]);

    foreach($dates as $k=>$date){
      if (!$times[$k]) continue;
      $sTime = explode(',',$times[$k]);
      foreach($sTime as $t){

        $time = str_pad($t, 2, "0", STR_PAD_LEFT).':00:00';
        $date_compl = $date.' '. $time;

        $oObj = new Dates();
        $oObj->id_rate = $id_rate;
        $oObj->id_coach = $id_coach;
        $oObj->date_type = 'pt';
        $oObj->date = $date_compl;
        $oObj->customTime = $time;
        $oObj->price = 0;
        $oObj->id_user = 0;
        $oObj->blocked = 1;
        $oObj->is_group = 1;
        $oObj->save();


      }
    }

    return redirect('/admin/citas-pt')->with(['success'=>'Registro guardado']);

  }
}
