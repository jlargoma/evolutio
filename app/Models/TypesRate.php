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
      ];
      
          
      if ($key){
        return isset($lst[$key]) ? $lst[$key] : '--';
      }
      return $lst;
    }
}
