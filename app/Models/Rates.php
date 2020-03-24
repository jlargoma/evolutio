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
        return $this->hasOne('\App\TypesRate','id', 'type');
    }
}
