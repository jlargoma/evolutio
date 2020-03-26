<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateTypes extends Model
{
    protected $table = 'rate_types';

    public function user()
    {
        return $this->hasOne('\App\User', 'id', 'id_user');
    }

    public function rate()
    {
        return $this->hasMany('\App\Models\Rates', 'id', 'id_rate');
    }
    
    static function getNameBy_id() {
      $rates = self::all();
      $rateLst = [];
      foreach ($rates as $i){
        $rateLst[$i->id] = $i->name;
      }
      
      return $rateLst;
    }
}
