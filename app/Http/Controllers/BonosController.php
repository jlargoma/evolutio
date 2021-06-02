<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use Stripe;
use App\Models\Bonos;
use App\Models\UserBonos;

class BonosController extends Controller {

  public function index() {
    return view('/admin/bonos/index', [
        'objs' => Bonos::where('status', 1)->orderBy('name', 'asc')->get(),
        'old' => Bonos::where('status', 0)->orderBy('name', 'asc')->get(),
        'rateFilter'=>\App\Models\Rates::getTypeRatesGroups()
    ]);
  }
  
  public function create(Request $request) {
    $oObj = new Bonos();

    $oObj->name = $request->input('name');
    $oObj->qty = $request->input('qty');
    $oObj->price = $request->input('price');
//    $oObj->value = $request->input('value');
    $oObj->status = 1;
    $rate = $request->input('rate');
    if ($rate){
      $oObj->rate_id = null;
      $oObj->rate_type = null;
      if ($rate != 'all'){
        $filerRate = explode('-', $rate);
        if (count($filerRate) == 2) $oObj->rate_id = $filerRate[1];
        else $oObj->rate_type = $filerRate[0];
      }
    }
    $oObj->save();
    return redirect()->back()->with(['success'=>'Bono agregado']);
  }

  public function update(Request $request) {

    $id = $request->input('id');
    $oObj = Bonos::find($id);
    $oObj->name = $request->input('name');
    $oObj->qty = $request->input('qty');
//    $oObj->value = $request->input('value');
    $oObj->price = $request->input('price');
    $rate = $request->input('rate');
    
    if ($rate){
      $oObj->rate_id = null;
      $oObj->rate_type = null;
      if ($rate != 'all'){
        $filerRate = explode('-', $rate);
        if (count($filerRate) == 2) $oObj->rate_id = $filerRate[1];
        else $oObj->rate_type = $filerRate[0];
      }
    }
    
    if ($oObj->save()) {
      die('OK');
    }
    die('error');
  }

  public function delete($id) {
    $oObj = Bonos::find($id);
    $oObj->status = 0;
    if ($oObj->save()) {
        return redirect()->back()->with(['success'=>'Bono eliminado']);
    }
  }
  
  ///////////////////////////////////////////////////////////////////////////
  
  public function purcharse(Request $req) {
    $uID = $req->input('id_user', 0);
    $bID = $req->input('id_bono', 0);
    
    $oUser = \App\Models\User::find($uID);
    if (!$oUser || $oUser->id != $uID)
      return redirect()->back()->withErrors(['Usuario no encontrado']);
    
    $oBono = Bonos::find($bID);
    if (!$oBono || $oBono->id != $bID)
      return redirect()->back()->withErrors(['Bono no encontrado']);
    
     
    $tpay = $req->input('type_payment','cash');
    $value = $oBono->price;
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
    
    $resp = $this->asignBono($oUser,$oBono,$tpay,$idStripe,$cStripe);

    if ($resp[0] == 'error') {
        return redirect()->back()->withErrors([$resp[1]]);
    }
    
    $type = $req->input('type', 0);
    $id_back = $req->input('id_back', 0);
    if ($id_back>0){
        switch ($type){
            case 'nutri':
               return redirect('/admin/citas-nutricion/edit/'.$id_back)->with('success', $resp[1]);
            case 'fisio':
               return redirect('/admin/citas-fisioterapia/edit/'.$id_back)->with('success', $resp[1]);
//            case 'ficha':
//               return redirect('/admin/usuarios/informe/'.$id_back.'#bono')->with('success', $resp[1]);
            case 'pt':
                die('error');
        }
    }
    return redirect()->back()->with('success', $resp[1]);
  }
  
  function asignBono($oUser,$oBono,$tpay,$idStripe=null,$cStripe=null){
    $date = date('Y-m-d');
    //BEGIN PAYMENTS
        $oCobro = new \App\Models\Charges();
        $oCobro->id_user = $oUser->id;
        $oCobro->date_payment = $date;
        $oCobro->id_rate = 0;
        $oCobro->type_payment = $tpay;
        $oCobro->type = 1;
        $oCobro->import = $oBono->price;
        $oCobro->discount = 0;
        $oCobro->type_rate = 0;
        $oCobro->bono_id = $oBono->id;
        $oCobro->id_stripe = $idStripe;
        $oCobro->customer_stripe = $cStripe;
        $oCobro->save();
    //END PAYMENTS
        
        $oUsrBono = $oBono->getBonoUser($oUser->id);
        if ($oUsrBono){
          $oUsrBono->qty = $oUsrBono->qty +$oBono->qty;
        } else {
          $oUsrBono = new UserBonos();
          $oUsrBono->user_id   = $oUser->id;
          $oUsrBono->rate_type = $oBono->rate_type;
          $oUsrBono->rate_id   = $oBono->rate_id;
          $oUsrBono->qty = $oBono->qty;
        }

        $oUsrBono->save(); 
        $oUsrBono->saveLogIncr($oBono,$oCobro->id);
    $statusPayment = 'Pago realizado correctamente, por ' . payMethod($tpay);
    /*************************************************************/
    $sent = MailController::sendEmailPayBono($oUser, $oBono,$tpay);
    return ['OK',$statusPayment];
    if ($sent == 'OK') return ['OK', $statusPayment,$oCobro->id];
    else return ['error', $sent,$oCobro->id];
  }


  
  /**
   * 
   * /admin/bonos/comprar/3000
   * @param type $uId
   * @return type
   */
  public function show_purcharse($uId,$t,$id) {
    
    $oUser = \App\Models\User::find($uId);
    if ($oUser && $oUser->id == $uId){
      
      //--------------------------------//
      //--  BEGIN: STRIPE    ----------//
      $pStripe = null;
      $card = null;
      $paymentMethod = $oUser->getPayCard();
      if ($paymentMethod) {
        $aux = $paymentMethod->toArray();
        $card['brand'] = $aux['card']['brand'];
        $card['exp_month'] = $aux['card']['exp_month'];
        $card['exp_year'] = $aux['card']['exp_year'];
        $card['last4'] = $aux['card']['last4'];
      }
      //--  END: STRIPE       ----------//
      //--------------------------------//
    
      return view('admin.usuarios.clientes.bono', [
        'user'=>$oUser,
        'card'=>$card,
        'oBonos' => Bonos::where('status', 1)->orderBy('name', 'asc')->get(),
        'type'=>$t,
        'id_back'=>$id,
        'rates'=>\App\Models\Rates::pluck('name','id')->toArray(),
        'typesRate'=> \App\Models\TypesRate::pluck('name','id')->toArray(),
    ]);
      
    }
    die('usuario no encontrado');
  }
  
  function printBonologs($id){
    $logs = \App\Models\UserBonosLogs::getLst($id);
    if ($logs){
      include_once app_path().'/Blocks/bonosLogs.php';
    }
  }

}
