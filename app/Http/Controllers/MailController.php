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

		return ($sended) ? true : false;
	}
        
        public static function sendEmailPayRate($data,$oUser,$oRate)
	{

		$date        = Carbon::createFromFormat('Y-m-d', $data['fecha_pago']);
		$typePayment = $data['type_payment'];
		$importe     = $data['importe'];
		$email       = $oUser->email;

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

		return ($sended) ? true : false;
	}

}
