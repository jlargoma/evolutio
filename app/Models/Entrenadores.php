<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrenadores extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];
    
    static function getRatesNameBy_id() {
      $obj = self::all();
      $rLst = [];
      foreach ($obj as $i){
        $rLst[$i->id] = $i->name;
      }
      
      return $rLst;
    }
}