<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonos extends Model
{
  public function users()
  {
    return $this->hasMany('\App\Models\UserBonos','id', 'id_rate');
  }

}
