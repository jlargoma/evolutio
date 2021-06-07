<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MailsService {

  public static function sendEmailPayBono($oUser, $oBono, $tPay) {
    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return $email . ' no es un mail válido';
    try {
      $sended = Mail::send('emails._payment_bono', [
                  'user' => $oUser,
                  'bono' => $oBono,
                  'typePayment' => $tPay
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

  public static function sendEmailPayRate($data, $oUser, $oRate) {

    $date = Carbon::createFromFormat('Y-m-d', $data['fecha_pago']);
    $typePayment = $data['type_payment'];
    $importe = $data['importe'];
    $email = $oUser->email;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return $email . ' no es un mail válido';
    try {
      $sended = Mail::send('emails._payment_rate', [
                  'user' => $oUser,
                  'date' => $date,
                  'rate' => $oRate,
                  'importe' => $importe,
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

}
