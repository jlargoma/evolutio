<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    public function users()
    {
        return $this->hasMany('\App\UserRates','id', 'id_rate');
    }

    public function typeRate()
    {
        return $this->hasOne('\App\Models\RateTypes','id', 'type');
    }
    
    
    static function getRatesBy_name() {
      $rates = self::all();
      $rateLst = [];
      foreach ($rates as $i){
        $rateLst[$i->name] = [$i->id,$i->cost];
      }
      
      return $rateLst;
    }
    
    static function getRatesNameBy_id() {
      $rates = self::all();
      $rateLst = [];
      foreach ($rates as $i){
        $rateLst[$i->id] = $i->name;
      }
      
      return $rateLst;
    }
    static function getRatesNameBy_type() {
      $rates = self::all();
      $rateLst = [];
      foreach ($rates as $i){
        if (!isset($rateLst[$i->type])) $rateLst[$i->type] = [];
        $rateLst[$i->type][$i->id] = $i->name;
      }
      
      return $rateLst;
    }
    
    static function getRatestypeBy_id() {
      $rates = self::all();
      $rateLst = [];
      foreach ($rates as $i){
        $rateLst[$i->id] = $i->type;
      }
      
      return $rateLst;
    }
}
