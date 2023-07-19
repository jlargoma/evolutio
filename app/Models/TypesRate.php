<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypesRate extends Model
{
    protected $table = 'types_rate';

    public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_user');
    }

    public function rate()
    {
        return $this->hasMany('\App\Models\Rates', 'id', 'id_rate');
    }
    static function subfamily($key=null) {
      $lst = [
        'f01'=>'FISIOTERAPIA',
        'f02'=>'FISIOTERAPIA INFANTIL',
        'f03'=>'SUELO PELVICO',
        'f04'=>'ESTETICA',
        'f05'=>'APARATOLOGÍA',
        'f06'=>'MEDICO',
        'v01'=>'VALORACIÓN INICIAL',
        'e01'=>'FACIAL',
        'e02'=>'CORPORAL',
        'p01'=>'prod. FISIOTERAPIA',
        'p02'=>'prod. ESTETICA',
        'p03'=>'prod. NUTRICIÓN',
        't01'=>'FUTBOL 11',
        't02'=>'FUTBOL SALA',
      ];
      if ($key){
        return isset($lst[$key]) ? $lst[$key] : '--';
      }
      return $lst;
    }
    
    static function getWithsubfamily($key=null) {
      $lst = self::OrderBy('name')->get();
      $subfs = self::subfamily();
      $result = [];
      foreach ($lst as $i){
        $subtipes = $i->subtipes;
        $aux = [];
        if ($subtipes){
          foreach ($subfs as $k=>$v){
            if (substr($k,0,1) == $subtipes)
                $aux[$k]=$v;    
          }
        }
        
        $result[$i->id] = ['n' => $i->name,'l'=>$aux];
      }
      return $result;
    }

    static function getsubfamilyArray($id=null) {
      $obj = self::find($id);
      $subfs = self::subfamily();
      $result = [];
      $subtipes = $obj->subtipes;
      if ($subtipes){
        foreach ($subfs as $k=>$v){
          if (substr($k,0,1) == $subtipes)
              $result[]=$k;    
        }
      }
      return $result;
    }
}
