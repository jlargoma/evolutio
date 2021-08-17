<?php

namespace App\Services;

use App\Models\Rates;
use App\Models\User;
use App\Models\Bonos;
use App\Models\UserBonos;
use App\Models\UserRates;
use App\Models\Charges;

class ValoracionService {

  private static $bIDV = 42;
  
  static function RateLstID($uID,&$aLst) {
    if (in_array(self::$bIDV, $aLst)) return;
    $bono = UserBonos::where('user_id',$uID)
            ->where('qty','>',0)
            ->where('rate_subf','v01')->first();
    if ($bono)  $aLst[] = self::$bIDV;
  }
  
  static function bonosServ($uID,$oRate, &$total, &$lst) {
    dd($oRate);
  }
  
  static function getURate($uID,$rID,$timeCita,$cID) {
    if ($rID != self::$bIDV) return  null;
    
    $UserBonos = UserBonos::where('user_id',$uID)
              ->where('rate_subf','v01')->first();
    if (!$UserBonos) return null;
    
    $oRate = Rates::find($rID);
    if (!$oRate) return null;
    
    // crear $uRate
    $uRate = new UserRates();
    $uRate->id_user  = $uID;
    $uRate->id_rate  = $rID;
    $uRate->coach_id = $cID;
    $uRate->active   = 0;
    $uRate->price    = 0;
    $uRate->rate_year  = date('Y', $timeCita);
    $uRate->rate_month = date('m', $timeCita);
    $uRate->save();
    
    //crear cobro (para usar el bono)
    $oCobro = new Charges();
    $oCobro->id_user = $uID;
    $oCobro->date_payment = date('Y-m-d');
    $oCobro->id_rate = $oRate->id;
    $oCobro->type_payment = 'bono';
    $oCobro->type = 1;
    $oCobro->import = 0;
    $oCobro->discount = 0;
    $oCobro->type_rate = $oRate->type;
    $oCobro->save();

    //Aplicar el bono
    $resp = $UserBonos->usar($oCobro->id, 'valora', date('Y-m-d', $timeCita));
    if ($resp != 'OK') {
      $uRate->delete();
      $oCobro->delete();
      return null;
    }
    
    $uRate->id_charges = $oCobro->id;
    $uRate->save();
    return $uRate;
  }
}
