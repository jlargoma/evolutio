<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use Mail;
use App\Models\User;
use App\Models\UserRates;
use \App\Traits\ValoracionTraits;

class CustomerController extends Controller {

  use ValoracionTraits;
  
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
      
    $payment = false;
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
          $disc  = isset($data[5]) ? $data[5] : 0;
          $id_coach = isset($data[6]) ? $data[6] : null;
          break;
        case 'nutri': //$dID,$oUser->id,$importe*100,$oRate->id;
        case 'fisio': //$dID,$oUser->id,$importe*100,$oRate->id;
          $oDate = \App\Models\Dates::find($data[0]);
          $uRates = $oDate->uRates;
          if ($uRates->id_charges) $payment = true;
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
          $hour = $oDate->getHour();
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
    
    //--------------------------------------------------------------//
    //--------------------------------------------------------------//
    $checkout = null;
    if (!$payment){
      $checkout = $sStripe->newCheckout($oUser, $amount,$name);
      if (is_string($checkout)){
        die($checkout);
      }

      $oSession = $checkout->jsonSerialize();
  //    $payment_id = $oSession->id;
      $iStripe = $oSession['payment_intent'];
      $cStripe = $oSession['customer'];
      $price = round($amount / 100, 2);
      switch ($typeKey) {
            case 'rate':
              $time = strtotime($data[0] . '-' . $data[1] . '-01');
              \App\Models\Stripe3DS::addNew($oUser->id,$iStripe,$cStripe,'generatePayment',
                      [
                        'time'=>$time,'rID'=>$data[4], 
                        'value'=>$price,'disc'=>$disc,'id_coach'=>$id_coach
                      ]
              );
              break;
            case 'nutri':
            case 'fisio':
              \App\Models\Stripe3DS::addNew($oUser->id,$iStripe,$cStripe,'cita',['dID'=>$oDate->id]);
              break;
          }
    }
    //--------------------------------------------------------------//
    //--------------------------------------------------------------//
    //--------------------------------------------------------------//

    return view('customers.payments.stripe_payment', [
        'keyStripe' => config('cashier.key'),
        'amount' => $amount,
        'name' => $name,
        'type' => $type,
        'token' => $token,
        'control' => $control,
        'items' => $items,
        'disc'=>$disc,
        'email' => $oUser->email,
        'checkout' => $checkout,
        'payment' => $payment,
    ]);
  }


  public function showResult(Request $request) {
    return view('customers.message');
  }
  public function paymentSuccess(Request $request) {
    return view('customers.payments.stripe_response',['success'=>true,'cancel'=>false]);
  }
  public function paymentCancel(Request $request) {
    return view('customers.payments.stripe_response',['success'=>false,'cancel'=>true]);
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
