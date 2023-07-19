<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonos extends Model
{
  public function users()
  {
    return $this->hasMany('\App\Models\UserBonos','id', 'id_rate');
  }
  
  public function getBonoUser($uID) {
    if ($this->rate_type){
      $obj = UserBonos::where('user_id',$uID)
              ->where('rate_type',$this->rate_type)->first();
      if ($obj) return $obj;
    }
    
    if ($this->rate_subf){
      $obj = UserBonos::where('user_id',$uID)
              ->where('rate_subf',$this->rate_subf)->first();
      if ($obj) return $obj;
    }
    
    return null;
  }

  static function listBonos(){
    $aBonos =  Bonos::orderBy('name')->get();
    $lstBonos = [];
    foreach ($aBonos as $k=>$v){
      $rateType = null;
      if ($v->rate_subf){
        if(str_contains($v->rate_subf,'f')) $rateType = 8;
        if(str_contains($v->rate_subf,'v')) $rateType = 11;
        if(str_contains($v->rate_subf,'e')) $rateType = 12;
        if(str_contains($v->rate_subf,'p')) $rateType = 13;
        if(str_contains($v->rate_subf,'t')) $rateType = 2;
      } else {
        $rateType = $v->rate_type;
      }
      $lstBonos[$v->id] = $rateType;
    }

    return $lstBonos;
  }
}
