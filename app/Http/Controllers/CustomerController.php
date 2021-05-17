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
    $amount = 15000;
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
          $uRates = $oDate->uRates;
          if (!$uRates){
            $oDate->delete();
            die('Usuario eliminado');
          }
          $oUser = $uRates->user;
          if (!$oUser){
            $uRates->delete();
            $oDate->delete();
            die('Usuario eliminado');
          }
          $dateTime = strtotime($oDate->date);
          $day = date('d', $dateTime) . ' de ' . getMonthSpanish(date('n', $dateTime),false);
          $hour = date('H:i', $dateTime);
          $oRate = $uRates->rate;
          $oCoach = $oDate->coach;
          /** @Todo Controlar si ya está pagado */
          $name = 'Pago de su cita de ';
          $items = [];
          if ($oDate->date_type == 'nutri') {
            $name .= ' Nutrición ';
            $items[] = '<b>Nutricionista:</b> ' . $oCoach->name;
          }
          if ($oDate->date_type == 'fisio') {
            $name .= ' Fisioterapia ';
            $items[] = '<b>Fisioterapeuta:</b> ' . $oCoach->name;
          }
          $items[] = '<b>Servicio:</b> ' . $oRate->name;
          $items[] = '<b>Fecha:</b> ' . $day;
          $items[] = '<b>Hora:</b> ' . $hour;

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
        $day = date('d', $dateTime) . ' de ' . getMonthSpanish(date('n', $dateTime));
        $hour = date('H:i', $dateTime);
        $oRate = $oDate->service;
        $name = 'Pago de su cita de ';
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
        $uRates = $oDate->uRates;
        if (!$uRates){
          die('Usuario eliminado');
        }
        $oUser = $uRates->user;
        if (!$oUser){
          die('Usuario eliminado');
        }
        $response = ChargesController::savePayment(
                        date('Y-m-d'), $oUser->id, $oDate->id_rate,
                        'card', $amount, $disc, $idPaid, $idCust);
        if ($response[0] != 'OK') {
          return redirect()->back()
                          ->withErrors([$response[1]])
                          ->withInput();
        } else {
          $uRates->id_charges = $response[2];
          $uRates->save();
        }
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
  
  
  public function showResult() {
    return view('customers.message');
  }
  
  public function signConsentSave(Request $request,$code,$control) {
    $data = \App\Services\LinksService::getLinkData($code,$control);
    if (!$data){
      abort(404);
      exit();
    }
    $oUser = User::find($data[0]);
    if (!$oUser){
      abort(404);
      exit();
    }
    $uID  = $oUser->id;
    $sign = $request->input('sign');
    $encoded_image = explode(",", $sign)[1];
    $decoded_image = base64_decode($encoded_image);
    switch ($data[1]){
      case 1001:
        $type = 'sign_fisioIndiba';
        break;
      case 2002:
        $type = 'sign_sueloPelvico';
        break;
      default:
        $type = 'sign_gral';
        break;
    }
    
    $fileName = 'signs/' .$type.'-'. $uID .'-'.time().'.png';
    $path = storage_path('/app/' . $fileName);
    
    $oUser->setMetaContent($type,$fileName);

    $storage = \Illuminate\Support\Facades\Storage::disk('local');
    $storage->put($fileName, $decoded_image);
    
    return redirect('/resultado')->with(['success' => 'Firma Guardada']);
  }

  function signConsent($code,$control) {
    
    $data = \App\Services\LinksService::getLinkData($code,$control);
    $dView = [
      'name'=>'',  
      'file'=>'',  
      'tit' =>'',  
      'tmsg'=>'',  
      'msg' =>'',  
      'url' =>"/firmar-consentimiento/$code/$control",  
    ];
    if (!$data){
      abort(404);
      exit();
    }
    
    $oUser = User::find($data[0]);
    if (!$oUser){
      abort(404);
      exit();
    }
    switch ($data[1]){
      case 1001:
        $tit = 'CONSENTIMIENTO FISIOTERAPIA CON INDIBA';
        $doc = 'CONSENTIMIENTO-FISIOTERAPIA-CON-INDIBA';
        break;
      case 2002:
        $tit = 'CONSENTIMIENTO SUELO PELVICO';
        $doc = 'CONSENTIMIENTO-SUELO-PELVICO';
        break;
      default:
        $tit = '';
        $doc = '';
        break;
    }
    

    return view('customers.consentimiento', [
      'name'=>$oUser->name,  
      'file'=>$doc,  
      'tit' =>$tit,  
      'url' =>"/firmar-consentimiento/$code/$control", 
    ]);


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
