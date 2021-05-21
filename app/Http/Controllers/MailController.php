<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use \Carbon\Carbon;

class MailController extends Controller
{
	public static function sendEmailPaymentRate($data)
	{

		$user        = \App\User::find($data['id_user']);
		$date        = Carbon::createFromFormat('Y-m-d', $data['fecha_pago']);
		$rate        = \App\Rates::find($data['id_tax']);
		$typePayment = $data['type_payment'];
		$importe     = $data['importe'];
		$email       = $data['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
        try{
          $sended = Mail::send('emails._payment_rate', [
              'user'        => $user,
              'date'        => $date,
              'rate'        => $rate,
              'importe'     => $importe,
              'typePayment' => $typePayment
          ], function ($message) use ($email) {
              $message->subject('Comprobante de pago evolutio');
              $message->from(config('mail.from.address'), config('mail.from.name'));
              $message->to($email);
          });
        } catch (\Exception $ex) {
          return ($ex->getMessage());
        }
		 
        return 'OK';
	}
        
        public static function sendEmailPayRate($data,$oUser,$oRate)
	{

		$date        = Carbon::createFromFormat('Y-m-d', $data['fecha_pago']);
		$typePayment = $data['type_payment'];
		$importe     = $data['importe'];
		$email       = $oUser->email;
                
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
        try{
          $sended = Mail::send('emails._payment_rate', [
              'user'        => $oUser,
              'date'        => $date,
              'rate'        => $oRate,
              'importe'     => $importe,
              'typePayment' => $typePayment
          ], function ($message) use ($email) {
              $message->subject('Comprobante de pago evolutio');
              $message->from(config('mail.from.address'), config('mail.from.name'));
              $message->to($email);
          });
        } catch (\Exception $ex) {
          return ($ex->getMessage());
        }
		
        return 'OK';
	}
        
    public static function sendEmailPayRateByStripe($data,$oUser,$oRate,$pStripe)
	{

		$date        = Carbon::createFromFormat('Y-m-d', $data['fecha_pago']);
		$typePayment = $data['type_payment'];
		$importe     = $data['importe'];
		$email       = $oUser->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
        
        $rType = \App\Models\TypesRate::find($oRate->type);
        $type = 'Nuevo Servicio';
        if ($rType->type == 'fisio' || $rType->type == 'nutri'){
          $type = 'Nueva Cita';
        }
        try{
          $sended = Mail::send('emails._payment_rateStripe', [
              'user'        => $oUser,
              'date'        => $date,
              'rate'        => $oRate,
              'type'        => $type,
              'importe'     => $importe,
              'pStripe'     => $pStripe
          ], function ($message) use ($email) {
              $message->subject('Solicitud de pago evolutio');
              $message->from(config('mail.from.address'), config('mail.from.name'));
              $message->to($email);
          });
        } catch (\Exception $ex) {
          return ($ex->getMessage());
        }
         
        return 'OK';
		return (!$sended) ? true : false;
	}
        
        
    public static function sendEmailPayDateByStripe($oDate, $oUser, $oRate,$oCoach,$pStripe,$importe)
	{
            $email    = $oUser->email;
            $dateTime = strtotime($oDate->date);
            $day = date('d',$dateTime).' de '.getMonthSpanish(date('n',$dateTime),false);
            $hour = ($oDate->customTime) ? ($oDate->customTime) : date('H:i',$dateTime);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
            try{
              $sended = Mail::send('emails._payment_citaStripe', [
                      'user'    => $oUser,
                      'obj'     => $oDate,
                      'rate'    => $oRate,
                      'importe' => $importe,
                      'oCoach'  => $oCoach,
                      'pStripe' => $pStripe,
                      'hour'    => $hour,
                      'day'     => $day,
              ], function ($message) use ($email) {
                      $message->subject('Solicitud de pago evolutio');
                      $message->from(config('mail.from.address'), config('mail.from.name'));
                      $message->to($email);
              });
            } catch (\Exception $ex) {
              return ($ex->getMessage());
            }
            return 'OK';
	}

}
