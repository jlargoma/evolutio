<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\Rates;
use App\Models\UserRates;
use App\Models\Charges;

class ChargesController extends Controller {

    public function updateCobro(Request $request, $id) {
        $charge = Charges::find($id);

        if (!$charge)
            return view('admin.popup_msg');
        $uRate = UserRates::where('id_charges', $charge->id)->first();

        if ($uRate) {
            $date = getMonthSpanish($uRate->rate_month, false) . ' ' . $uRate->rate_year;
        } else {
            $time = strtotime($charge->date_payment);
            $date = getMonthSpanish(date('n', $time), false) . ' ' . date('Y', $time);
        }

        return view('admin.charges.cobro_update', [
            'taxes' => Rates::all(),
            'rate' => Rates::find($charge->id_rate),
            'date' => $date,
            'user' => $charge->user,
            'importe' => $charge->import,
            'charge' => $charge
        ]);
    }

    public function updateCharge(Request $request, $id) {
        $charge = Charges::find($id);
        if (!$charge) {
            return redirect()->back()->withErrors(['cobro no encontrado']);
        }
        if ($request->input('deleted')) {
            UserRates::where('id_charges',$id)->update(['id_charges' => null]);
            \App\Models\Dates::where('id_charges',$id)->update(['charged' => 0]);
            
            $charge->delete();
            return redirect()->back()->with('success', 'cobro Eliminado');
        } else {
            $charge->type_payment = $request->input('type_payment');
            $charge->import = $request->input('importe');
            $charge->discount = $request->input('discount');
            $charge->save();
            UserRates::where('id_charges',$id)->update(['price' => $charge->import]);
            return redirect()->back()->with('success', 'cobro actualizado');
        }
    }

    public function cobrar(Request $req) {

      $id_uRate = $req->input('id_uRate', null);
      $uRate = UserRates::find($id_uRate);
      
      if (!$uRate) {
        return redirect()->back()->withErrors(['Tarifa no encontrada']);
      }
      
      $time = strtotime($uRate->rate_year.'/'.$uRate->rate_month.'/01');
      $uID = $uRate->id_user;
      $rID = $uRate->id_rate;
      $tpay = $req->input('type_payment','cash');
      $value = $req->input('importe', 0);
      $disc = $req->input('discount', '0');
        

        $idStripe=null;$cStripe=null;
        if ($tpay == 'card'){
            $cc_number = $req->input('cc_number', null);
            $cc_expide_mm = $req->input('cc_expide_mm', null);
            $cc_expide_yy = $req->input('cc_expide_yy', null);
            $cc_cvc = $req->input('cc_cvc', null);
            $cardLoaded = $req->input('cardLoaded', null);
            $oUser = \App\Models\User::find($uID);
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
                            ->withErrors($resp)
                            ->withInput();
                }
            }
            /***********************************/
            /** COBRAR POR STRIPE **/
            /***********************************/
            $resp = $sStripe->automaticCharge($oUser,round($value*100));
            if ( $resp[0] != 'OK'){
                return redirect()->back()
                        ->withErrors([$resp[1]])
                        ->withInput();
            }
            $idStripe = $resp[1];
            $cStripe = $resp[2];
        }
        $resp = $this->generateePayment($time, $uID, $rID, $tpay, $value, $disc,$idStripe,$cStripe);
       
        if ($resp[0] == 'error') {
            return redirect()->back()->withErrors([$resp[1]]);
        }
        return redirect()->back()->with('success', $resp[1]);
    }

    public function chargeUser(Request $req) {
        $month = $req->input('date_payment', null);
        $operation = $req->input('type', 'all');
        if ($month)
            $time = strtotime($month);
        else
            $time = time();
        $uID = $req->input('id_user', null);
        $rID = $req->input('id_rate', null);
        $tpay = $req->input('type_payment','cash');
        $value = $req->input('importe', 0);
        $disc = $req->input('discount', 0);
        $oUser = \App\Models\User::find($uID);
        /************************************************************/
        $resp = ['error','Error al procesar su cobro'];
        if ($operation == 'all' || !$operation){
            $idStripe=null;$cStripe=null;
            if ($tpay == 'card'){
                 
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
            $resp = $this->generateePayment($time, $uID, $rID, $tpay, $value, $disc,$idStripe,$cStripe);
        } else {
          $u_email = $req->input('u_email',null);
          if ($u_email && $oUser->email != $u_email){
            $oUser->email = $u_email;
            $oUser->save();
          }
          $u_phone = $req->input('u_phone',null);
          if ($u_phone && $oUser->telefono != $u_phone){
            $oUser->telefono = $u_phone;
            $oUser->save();
          }
          return $this->generateStripeLink($time, $uID, $rID, $tpay, $value, $disc,$operation);
        }

        if ($resp[0] == 'error') {
            return redirect()->back()->withErrors([$resp[1]]);
        }
        return redirect()->back()->with('success', $resp[1]);
    }

    public static function savePaymentRate($time, $uID, $rID, $tpay, $value, $disc,$idStripe,$cStripe){
        $objet = new ChargesController();
        return $objet->generateePayment($time, $uID, $rID, $tpay, $value, $disc,$idStripe,$cStripe);
    }
            
    function generateePayment($time, $uID, $rID, $tpay, $value, $disc=0,$idStripe=null,$cStripe=null) {
        $month = date('Y-m-d', $time);
        $oUser = \App\Models\User::find($uID);
        if (!$oUser)
            return ['error', 'Usuario no encontrado'];


        $oRate = Rates::find($rID);
        if (!$oRate)
            return ['error', 'Tarifa no encontrada'];
        $dataMail = [
            'fecha_pago' => $month,
            'type_payment' => $tpay,
            'importe' => $value,
        ];
        if(!$disc) $disc = 0;
        //BEGIN PAYMENTS MONTH
        for ($i = 0; $i < $oRate->mode; $i++) {

            $oCobro = new Charges();
            $oCobro->id_user = $oUser->id;
            $oCobro->date_payment = $month;
            $oCobro->id_rate = $oRate->id;
            $oCobro->type_payment = $tpay;
            $oCobro->type = 1;
            $oCobro->import = $value;
            $oCobro->discount = $disc;
            $oCobro->type_rate = $oRate->type;
            $oCobro->id_stripe = $idStripe;
            $oCobro->customer_stripe = $cStripe;
            $oCobro->save();

            /**************************************************** */

            $oUserRate = UserRates::where('id_user', $oUser->id)
                    ->where('id_rate', $oRate->id)
                    ->where('rate_month', date('n', $time))
                    ->where('rate_year', date('Y', $time))
                    ->whereNull('id_charges')
                    ->first();
            if ($oUserRate) {
                $oUserRate->id_charges = $oCobro->id;
                $oUserRate->save();
            } else { //si no tenia asignada la tarifa del mes
                $newRate = new UserRates();
                $newRate->id_user = $oUser->id;
                $newRate->id_rate = $oRate->id;
                $newRate->rate_year = date('Y', $time);
                $newRate->rate_month = date('n', $time);
                $newRate->id_charges = $oCobro->id;
                $newRate->price = $value;
                $newRate->save();
            }
            /**************************************************/
            //Next month
            $time = strtotime($month . ' +1 month');
            $month = date('Y-m-d', $time);
            $value = 0; //solo se factura el primer mes
            $disc = 0; //solo se factura el primer mes
        }
        //END PAYMENTS MONTH
        $statusPayment = 'Pago realizado correctamente, por ' . payMethod($tpay);
        /*************************************************************/
        MailController::sendEmailPayRate($dataMail, $oUser, $oRate);
        return ['OK', $statusPayment,$oCobro->id];
    }

    function generateStripeLink($time, $uID, $rID, $tpay, $importe, $disc=0,$operation) {

        $month = date('Y-m-d', $time);
        $oUser = \App\Models\User::find($uID);
        if (!$oUser)
            return ['error', 'Usuario no encontrado'];


        $oRate = Rates::find($rID);
        if (!$oRate)
            return ['error', 'Tarifa no encontrada'];
     
        if(!$disc) $disc = 0;
        //BEGIN PAYMENTS MONTH
        $auxTime = $time;
        for ($i = 0; $i < $oRate->mode; $i++) {
                //si no tenia asignada la tarifa del mes
                $newRate = new UserRates();
                $newRate->id_user = $oUser->id;
                $newRate->id_rate = $oRate->id;
                $newRate->rate_year = date('Y', $auxTime);
                $newRate->rate_month = date('n', $auxTime);
                $newRate->id_charges = null;
                $newRate->price = $oRate->price;
                
                $newRate->save();
            /************************************************** */
            //Next month
            $auxTime = strtotime($month . ' +1 month');
            $month = date('Y-m-d', $auxTime);
            $value = 0; //solo se factura el primer mes
            $disc = 0; //solo se factura el primer mes
        }
        //END PAYMENTS MONTH
        /************************************************************** */
        
        $data = [date('Y', $time),date('m', $time),$uID,$importe*100,$rID,$disc];
        $sStripe = new \App\Services\StripeService();
        $pStripe = url($sStripe->getPaymentLink('rate',$data));
        switch ($operation){
          case 'mail':
            $dataMail = [
              'fecha_pago' => $month,
              'type_payment' => $tpay,
              'importe' => $importe,
            ];
            $sent = MailController::sendEmailPayRateByStripe($dataMail, $oUser, $oRate,$pStripe);
            if ($sent == 'OK') return ['OK', 'Se ha enviado un email con el link de pago'];
            return ['error', $sent];
            break;
               case 'wsp':
                $msg = 'Te adjuntamos el enlace para el pago de **'.$oRate->name.'** en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
            case 'copy':
                $msg = 'Te adjuntamos el enlace para el pago de '.$oRate->name.' en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
        }
        return response()->json(['error','error']);
    }

    
    public function getPriceTax(Request $request) {
        $tax = Rates::find($request->idTax);
        return $tax->price;
    }
    
    public function sendCobroMail(Request $request) {
        $uRate = UserRates::find($request->input('u_rate'));
        if (!$uRate){
          return ['error', 'Tarifa no encontrada'];
        }
        
        $time = strtotime($uRate->rate_year.'/'.$uRate->rate_month.'/01');
        $importe = ($request->input('importe'));
        $u_email = ($request->input('u_email'));
        $u_phone = ($request->input('u_phone'));
        $type = ($request->input('type'));
        $disc = $request->input('discount', 0);
        $oUser = $uRate->user;
        if (!$oUser)
            return ['error', 'Usuario no encontrado'];

        $oRate = $uRate->rate;
        if (!$oRate)
            return ['error', 'Tarifa no encontrada'];
        
        if ($oUser->email != $u_email){
            $oUser->email = $u_email;
            $oUser->save();
        }
        if (!empty($u_phone) && $oUser->telefono != $u_phone){
            $oUser->telefono = $u_phone;
            $oUser->save();
        }
        
        $data = [date('Y', $time),date('m', $time),$oUser->id,$importe*100,$oRate->id,$disc];
        $sStripe = new \App\Services\StripeService();
        $pStripe = url($sStripe->getPaymentLink('rate',$data));
        
        switch ($type){
            case 'mail':
                $dataMail = [
                    'fecha_pago' => date('Y-m-d', $time),
                    'type_payment' => 'card',
                    'importe' => $importe,
                ];
                
                $sentErr = MailController::sendEmailPayRateByStripe($dataMail, $oUser, $oRate,$pStripe);
                if ($sentErr == 'OK')  return response()->json(['OK', 'Se ha enviado un email con el link de pago']);
                  return response()->json(['error', $sentErr]);
                break;
            case 'wsp':
                $msg = 'Te adjuntamos el enlace para el pago de **'.$oRate->name.'** en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
            case 'copy':
                $msg = 'Te adjuntamos el enlace para el pago de '.$oRate->name.' en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
        }
            
        return response()->json(['error','error']);

    }
    
    
    
    public function sendCobroGral(Request $request) {
        $dID = $request->input('idDate');
        
        $oDate = \App\Models\Dates::find($dID);
        if (!$oDate || $oDate->id != $dID) {
            return response()->json(['error','Cita No encontrada']);
        }
        
        $coach = $oDate->coach;
        $importe = $oDate->price;
        $u_email = ($request->input('u_email'));
        $u_phone = ($request->input('u_phone'));
        $type = ($request->input('type'));
        $oUser = $oDate->user;
        if (!$oUser)
            return ['error', 'Usuario no encontrado'];

        $oRate = $oDate->service;
        if (!$oRate)
            return ['error', 'Servicio no encontrado'];
        
        if (!empty($u_email) && $oUser->email != $u_email){
            $oUser->email = $u_email;
            $oUser->save();
        }
        if (!empty($u_phone) && $oUser->telefono != $u_phone){
            $oUser->telefono = $u_phone;
            $oUser->save();
        }
        
        $data = [$dID,$oUser->id,$importe*100,$oRate->id];
        $sStripe = new \App\Services\StripeService();
        $rType = \App\Models\TypesRate::find($oRate->type);
        $pStripe = url($sStripe->getPaymentLink($rType->type,$data));
        
        switch ($type){
            case 'mail':
                MailController::sendEmailPayDateByStripe($oDate, $oUser, $oRate,$coach,$pStripe,$importe);
                return response()->json(['OK', 'Se ha enviado un email con el link de pago']);
                break;
            case 'wsp':
                $msg = 'Te adjuntamos el enlace para el pago de **'.$oRate->name.'** en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
            case 'copy':
                $msg = 'Te adjuntamos el enlace para el pago de '.$oRate->name.' en Evolutio '.$pStripe;
                return response()->json(['OK',$msg]);
                break;
        }
            
        return response()->json(['error','error']);

    }

}
