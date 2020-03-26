<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{

    public function Rate()
    {
        return $this->hasOne('\App\Models\Rates','id', 'id_rate');
    }
}
