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
    static function getByTypeRateID($id){
        return self::select('rates.*')
                ->join('types_rate','rates.type','=','types_rate.id')
                ->where('types_rate.id',$id)->get();
    }
    static function getRatesTypeRates(){
        return self::join('types_rate','rates.type','=','types_rate.id')
                ->pluck('types_rate.name','rates.id')->toArray();
    }
    
    static function getTypeRatesGroups(){
      $rateFilter = [];
      $oTypes = \App\Models\TypesRate::all();
      foreach ($oTypes as $item){
        $aux  = \App\Models\Rates::where('type',$item->id)->get();
        $aux2 = [];
        foreach ($aux as $a){
          $aux2[$a->id] = $a->name;
        }
        $rateFilter[$item->id] = ['n' => $item->name,'l'=>$aux2];
      }
      return $rateFilter;
    }
}
