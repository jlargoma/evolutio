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
}
