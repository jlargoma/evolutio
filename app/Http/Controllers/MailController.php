<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Rates;
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
        
     public static function sendEmailPuncharseBonoByStripe($data,$oUser,$oBono,$pStripe)
	{

		$typePayment = $data['type_payment'];
		$importe     = $data['importe'];
		$email       = $oUser->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
        try{
          $sended = Mail::send('emails._payment_BonoStripe', [
              'type'        => 'Compra de Bono',
              'user'        => $oUser,
              'bono_name'   => $oBono->name,
              'importe'     => $importe,
              'pStripe'     => $pStripe
          ], function ($message) use ($email) {
              $message->subject('Solicitud de compra de Bonos Evolutio');
              $message->from(config('mail.from.address'), config('mail.from.name'));
              $message->to($email);
          });
        } catch (\Exception $ex) {
          return ($ex->getMessage());
        }
         
        return 'OK';
		return (!$sended) ? true : false;
	}
        
    public static function sendEmailPayDateByStripe($oDate, $oUser, $oRate,$oCoach,$pStripe,$importe,$subj=null,$calFile=null,$type=null)
	{
            $email    = $oUser->email;
            $dateTime = strtotime($oDate->date);
            $day = date('d',$dateTime).' de '.getMonthSpanish(date('n',$dateTime),false);
            $hour = $oDate->getHour();
            
            $rateLst = [$oRate->name];
            $extrs = explode(',',$oDate->getMetaContent('extrs'));
            if (count($extrs)>0){
              $rNames = Rates::whereIn('id',$extrs)->pluck('name');
              if ($rNames){
                foreach($rNames as $n){
                  $rateLst[] = $n;
                }
              }

            }
            $mailData = [
                      'user'    => $oUser,
                      'obj'     => $oDate,
                      'rate'    => $oRate,
                      'rateLst' => $rateLst,
                      'importe' => $importe,
                      'oCoach'  => $oCoach,
                      'pStripe' => $pStripe,
                      'hour'    => $hour,
                      'day'     => $day,
                      'urlEntr' => null,
                      'urlIndiba' => null,
                      'urlSuelPelv' => null,
              ];
            /***********************************************************/
            $helpCitaCont = new \App\Helps\CitasMailsContent();
            //BEGIN: entrevista nutrición
            if ($type == 'nutri'){
              $mailData['urlEntr'] = $helpCitaCont->get_urlEntrevista($oUser);
            }
            //BEGIN: entrevista Fisioterapia
            if ($type == 'fisio'){
              $mailData['urlIndiba']   = $helpCitaCont->get_urlIndiba($oUser,$oRate);
              $mailData['urlSuelPelv'] = $helpCitaCont->get_urlSuelPelv($oUser,$oRate);
            }
            /***********************************************************/
            $mailData['remember'] = false;
        
        
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
            try{
              if (!$subj) $subj = 'Solicitud de pago evolutio';
              $sended = Mail::send('emails._payment_citaStripe', $mailData,
                  function ($message) use ($email,$subj,$calFile) {
                      $message->subject($subj);
                      $message->from(config('mail.from.address'), config('mail.from.name'));
                      $message->to($email);
                      $message->attach(public_path('/img/protocolo.jpeg'), array(
                            'as' => 'Protocolo Covid', 
                            'mime' => 'image/jpeg'));
                      if ($calFile){
                        $message->attach($calFile, array(
                            'as' => 'Evento Calendario '.time()));
                      }
                });
            } catch (\Exception $ex) {
              return ($ex->getMessage());
            }
            return 'OK';
	}
    
  public static function sendEmailCitaOLD($oDate, $oUser, $oRate,$oCoach,$importe,$subj=null,$calFile=null)
	{
            $email    = $oUser->email;
            $dateTime = strtotime($oDate->date);
            $day = date('d',$dateTime).' de '.getMonthSpanish(date('n',$dateTime),false);
            $hour = $oDate->getHour();
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
            try{
              
              if (!$subj)  $subj = 'Recordatorio de su Cita de Evolutio';
              
              $sended = Mail::send('emails._payment_citaStripe', [
                      'user'    => $oUser,
                      'obj'     => $oDate,
                      'rate'    => $oRate,
                      'importe' => $importe,
                      'oCoach'  => $oCoach,
                      'hour'    => $hour,
                      'day'     => $day,
              ], function ($message) use ($email,$subj,$calFile) {
                      $message->subject($subj);
                      $message->from(config('mail.from.address'), config('mail.from.name'));
                      $message->to($email);
                      $message->attach(public_path('/img/protocolo.jpeg'), array(
                            'as' => 'Protocolo Covid', 
                            'mime' => 'image/jpeg'));
                      if ($calFile){
                        $message->attach($calFile, array(
                            'as' => 'Evento Calendario '.time()));
                      }
              });
            } catch (\Exception $ex) {
              return ($ex->getMessage());
            }
            return 'OK';
              
	}

}
