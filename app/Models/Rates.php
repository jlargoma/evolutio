<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    public function users()
    {
        return $this->hasMany('\App\Models\UserRates','id', 'id_rate');
    }

    public function typeRate()
    {
        return $this->hasOne('\App\Models\TypesRate','id', 'type');
    }
        
    static function getByTypeRate($type){
        return self::select('rates.*')
                ->join('types_rate','rates.type','=','types_rate.id')
                ->where('types_rate.type',$type)->get();
    }
}
