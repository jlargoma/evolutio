<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use Mail;
use App\Models\User;
use App\Models\UserRates;

class CustomerController extends Controller {

  public function index() {
    die();
  }

  public function pagoSimple($type, $token = null, $control = null) {
    $amount = 1500;
    $name = '';
    $items = null;
    $sStripe = new \App\Services\StripeService();
    $data = $sStripe->getPaymentLinkData($type, $token, $control);
    if (!$data) die('error');
      
    $disc = null;
    if (count($data) == 2) {

      $typeKey = $data[0];
      $data = $data[1];
      switch ($typeKey) {
        case 'rate': //$year,$month,$id_user,$importe*100,$rate
          $oRate = \App\Models\Rates::find($data[4]);
          /** @Todo Controlar si ya está pagado */
          $name = 'Pago de la cuota de '
                  . $oRate->name
                  . ' del mes de ' . getMonthSpanish($data[1], false);
          $name .= ' del ' . $data[0];
          $amount = round($data[3]);
          $oUser = User::find($data[2]);
          $disc = $data[5];
          break;
        case 'nutri': //$dID,$oUser->id,$importe*100,$oRate->id;
        case 'fisio': //$dID,$oUser->id,$importe*100,$oRate->id;
          $oDate = \App\Models\Dates::find($data[0]);
          $oUser = $oDate->user;
          $dateTime = strtotime($oDate->date);
          $day = date('d', $dateTime) . ' de ' . getMonthSpanish(date('j', $dateTime));
          $hour = date('H:i', $dateTime);
          $oRate = $oDate->service;
          $oCoach = $oDate->coach;
          /** @Todo Controlar si ya está pagado */
          $name = 'Pago de su cida de ';
          $items = [];
          if ($oDate->date_type == 'nutri') {
            $name .= ' Nutrición ';
            $items[] = 'Nutricionista: ' . $oCoach->name;
          }
          if ($oDate->date_type == 'fisio') {
            $name .= ' Fisioterapia ';
            $items[] = 'Fisioterapeuta: ' . $oCoach->name;
          }
          $items[] = 'Servicio: ' . $oRate->name;
          $items[] = 'Fecha: ' . $day;
          $items[] = 'Hora: ' . $hour;

          $amount = round($data[2]);
          break;
      }
    }
    return view('customers.stripe_payment', [
        'keyStripe' => config('cashier.key'),
        'amount' => $amount,
        'name' => $name,
        'type' => $type,
        'token' => $token,
        'control' => $control,
        'items' => $items,
        'disc'=>$disc,
        'email' => $oUser->email,
    ]);
  }

  public function pagar(Request $request) {

    $type = $request->input('data_1');
    $token = $request->input('data_2');
    $control = $request->input('data_3');
    $sStripe = new \App\Services\StripeService();
    $data = $sStripe->getPaymentLinkData($type, $token, $control);
    $disc = 0;
    if (!$data)
      return redirect()->back()->withErrors(['Error al efectuar el pago (1)']);

    if (count($data) != 2)
      return redirect()->back()->withErrors(['Error al efectuar el pago (2)']);

    $type = $data[0];
    $data = $data[1];
    switch ($type) {
      case 'rate': //$year,$month,$id_user,$importe*100,$rate
        $oRate = \App\Models\Rates::find($data[4]);
        if (!$oRate)  return redirect()->back()->withErrors(['Item no encontrado']);
        $name = 'Pago de la cuota de '
                . $oRate->name
                . ' del mes de ' . getMonthSpanish($data[1], false);
        $name .= ' del ' . $data[0];
        $amount = round($data[3]);
        $disc = $data[5];
        break;
      case 'nutri': //$dID,$oUser->id,$importe*100,$oRate->id;
      case 'fisio':
        $oDate = \App\Models\Dates::find($data[0]);
        /** @Todo Controlar si ya está pagado */
        if (!$oDate)
        return redirect()->back()->withErrors(['Item no encontrado']);
        
        $dateTime = strtotime($oDate->date);
        $day = date('d', $dateTime) . ' de ' . getMonthSpanish(date('j', $dateTime));
        $hour = date('H:i', $dateTime);
        $oRate = $oDate->service;
        $name = 'Pago de su cida de ';
        $items = [];
        if ($oDate->date_type == 'nutri') {
          $name .= ' Nutrición ';
        }
        if ($oDate->date_type == 'fisio') {
          $name .= ' Fisioterapia ';
        }
        $name .= ' (' . $oRate->name . ') ';
        $name .= 'para el día ' . $day . ' a las ' . $hour . 'hrs';
        $amount = round($data[2]);
        break;
    }

    $stripeResp = $sStripe->pagoSimple($amount, $request->all());
//        dd($stripeResp);
//        echo json_encode($response);
//        $stripeResp = '{"id":"ch_1IgYHhDPIlaJjDoZet9zqsWE","object":"charge","amount":1990,"amount_captured":1990,"amount_refunded":0,"application":null,"application_fee":null,"application_fee_amount":null,"balance_transaction":"txn_1IgYHiDPIlaJjDoZyXJyjmMW","billing_details":{"address":{"city":null,"country":null,"line1":null,"line2":null,"postal_code":null,"state":null},"email":null,"name":"asdfdasf@asdfasd.com","phone":null},"calculated_statement_descriptor":"Stripe","captured":true,"created":1618504997,"currency":"eur","customer":"cus_JJAcl21bmPP0gh","description":null,"destination":null,"dispute":null,"disputed":false,"failure_code":null,"failure_message":null,"fraud_details":[],"invoice":null,"livemode":false,"metadata":[],"on_behalf_of":null,"order":null,"outcome":{"network_status":"approved_by_network","reason":null,"risk_level":"normal","risk_score":20,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"payment_intent":null,"payment_method":"card_1IgYEbDPIlaJjDoZlJmk4jNc","payment_method_details":{"card":{"brand":"visa","checks":{"address_line1_check":null,"address_postal_code_check":null,"cvc_check":"pass"},"country":"US","exp_month":12,"exp_year":2021,"fingerprint":"3xQykneQx3Hx5jrE","funding":"credit","installments":null,"last4":"4242","network":"visa","three_d_secure":null,"wallet":null},"type":"card"},"receipt_email":null,"receipt_number":null,"receipt_url":"https:\/\/pay.stripe.com\/receipts\/acct_1GgAEQDPIlaJjDoZ\/ch_1IgYHhDPIlaJjDoZet9zqsWE\/rcpt_JJAcLWsCOe7yRu9ZThgvZ5fHvPsxWUV","refunded":false,"refunds":{"object":"list","data":[],"has_more":false,"total_count":0,"url":"\/v1\/charges\/ch_1IgYHhDPIlaJjDoZet9zqsWE\/refunds"},"review":null,"shipping":null,"source":{"id":"card_1IgYEbDPIlaJjDoZlJmk4jNc","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_JJAcl21bmPP0gh","cvc_check":"pass","dynamic_last4":null,"exp_month":12,"exp_year":2021,"fingerprint":"3xQykneQx3Hx5jrE","funding":"credit","last4":"4242","metadata":[],"name":"asdfdasf@asdfasd.com","tokenization_method":null},"source_transfer":null,"statement_descriptor":null,"statement_descriptor_suffix":null,"status":"succeeded","transfer_data":null,"transfer_group":null}';
//        $stripeResp = json_decode($stripeResp);
    if (is_string($stripeResp))
      return redirect()->back()->withErrors([$stripeResp]);
    if (!$stripeResp->paid)
      return redirect()->back()->withErrors(['Error al efectuar el pago']);

    $amount = round($amount / 100, 2);
    $idPaid = $stripeResp->id;
    $idCust = $stripeResp->customer;
    $receipt_url = $stripeResp->receipt_url;
    $response = ['', ''];
    switch ($type) {
      case 'rate': //$year,$month,$id_user,$importe*100,$rate
        $time = strtotime($data[0] . '-' . $data[1] . '-01');
        $response = ChargesController::savePaymentRate(
                        $time, $data[2], $data[4], 'card', $amount, $disc, $idPaid, $idCust
        );
        break;
      case 'nutri': //$dID,$oUser->id,$importe*100,$oRate->id;
      case 'fisio':
        $oDate = \App\Models\Dates::find($data[0]);
        $response = ChargesController::savePaymentRate(
                        time(), $oDate->id_user, $oDate->id_rate,
                        'card', $amount, $disc, $idPaid, $idCust);
        if ($response[0] != 'OK') {
          return redirect()->back()
                          ->withErrors([$response[1]])
                          ->withInput();
        }
        // Actualizamos la cita
        $oDate->status = 1;
        $oDate->charged = 1;
        $oDate->save();
        break;
    }


//        dd($response);
    return view('customers.stripe_paid', [
        'name' => $name,
        'amount' => $amount,
        'receipt_url' => $receipt_url,
        'response' => $response
    ]);
  }

  function paymentMethod($token = null, $control = null) {
    return view('customers.stripe-pay-method', [
        'name' => $name,
        'amount' => $amount,
        'receipt_url' => $receipt_url,
        'response' => $response
    ]);
  }

  function save_paymentMethod(Request $req) {


    $validator = \Illuminate\Support\Facades\Validator::make($req->all(), [
                'cc_number' => 'required|min:14|max:20',
                'cc_expide_mm' => 'required|numeric|min:1|max:13',
                'cc_expide_yy' => 'required|numeric|min:20|max:' . (date('Y') + 10),
                'cc_cvc' => 'required|numeric|min:99|max:9999',
                    ], [
                'cc_number.required' => 'Debe ingresar el número de tarjeta',
                'cc_number.min' => 'Debe ingresar el número de tarjeta',
                'cc_number.max' => 'Debe ingresar el número de tarjeta',
                'cc_expide_mm.required' => 'Debe ingresar la Fecha de vencimiento',
                'cc_expide_mm.min' => 'Debe ingresar la Fecha de vencimiento',
                'cc_expide_mm.max' => 'Debe ingresar la Fecha de vencimiento',
                'cc_expide_yy.required' => 'Debe ingresar la Fecha de vencimiento',
                'cc_expide_yy.min' => 'Debe ingresar la Fecha de vencimiento 1',
                'cc_expide_yy.max' => 'Debe ingresar la Fecha de vencimiento',
                'cc_cvc.required' => 'Debe ingresar el CVC / CVV',
                'cc_cvc.min' => 'Debe ingresar el CVC / CVV',
                'cc_cvc.max' => 'Debe ingresar el CVC / CVV',
                    ]
    );
    if ($validator->fails()) {
      return redirect()->back()
                      ->withErrors($validator)
                      ->withInput();
    }
    $cc_number = $req->input('cc_number', null);
    $cc_expide_mm = $req->input('cc_expide_mm', null);
    $cc_expide_yy = $req->input('cc_expide_yy', null);
    $cc_cvc = $req->input('cc_cvc', null);

    $oUser = \App\Models\User::find($uID);
    $sStripe = new \App\Services\StripeService();
    $resp = $sStripe->subscription_changeCard($oUser, $cc_number, $cc_expide_mm, $cc_expide_yy, $cc_cvc);
    die('asfdasdf');
  }

}
