<?php

namespace App\Services;

use App\Models\Rates;
use App\Models\User;
use App\Models\Bonos;
use App\Models\UserBonos;
use App\Models\Charges;

class BonoService {

  /**
   * 
   * @param type $user_id
   * @param type $pID
   * @param type $cID
   * @param type $aData
   */
  function asignBono3DS($user_id,$pID,$cID,$aData) {
    
    $alreadyCharge = Charges::where('id_stripe',$pID)
            ->where('customer_stripe',$cID)->first();
    if (!$alreadyCharge){
      $oUser = User::find($user_id);
      $oBono = Bonos::find($aData->bono);
      $this->asignBono($oUser, $oBono, $aData->tpay, $pID, $cID);
    }
  }
  
  /**
   * 
   * @param type $oUser
   * @param type $oBono
   * @param type $tpay
   * @param type $idStripe
   * @param type $cStripe
   * @return type
   */
  function asignBono($oUser, $oBono, $tpay, $idStripe = null, $cStripe = null) {
    $date = date('Y-m-d');
    //BEGIN PAYMENTS
    $oCobro = new Charges();
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
    if ($oUsrBono) {
      $oUsrBono->qty = $oUsrBono->qty + $oBono->qty;
    } else {
      $oUsrBono = new UserBonos();
      $oUsrBono->user_id = $oUser->id;
      $oUsrBono->rate_type = $oBono->rate_type;
      $oUsrBono->rate_id = $oBono->rate_id;
      $oUsrBono->qty = $oBono->qty;
    }

    $oUsrBono->save();
    $oUsrBono->saveLogIncr($oBono, $oCobro->id);
    $statusPayment = 'Pago realizado correctamente, por ' . payMethod($tpay);
    /*     * ********************************************************** */
    $sent = MailsService::sendEmailPayBono($oUser, $oBono, $tpay);
    return ['OK', $statusPayment];
    if ($sent == 'OK')
      return ['OK', $statusPayment, $oCobro->id];
    else
      return ['error', $sent, $oCobro->id];
  }

}
