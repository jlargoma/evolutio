<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; //línea necesaria

class Dates extends Model
{
    use SoftDeletes; //Implementamos 
    protected $table = "appointment";
    public function service()
    {
        return $this->hasOne('\App\Models\Rates', 'id', 'id_rate');
    }

    public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_user');
    }

    public function coach()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_coach');
    }

    public function check()
    {
        return $this->hasOne('\App\Models\FisicCheck', 'id_date', 'id');
    }
}
