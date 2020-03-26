<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachSessions extends Model
{

    public function Rate()
    {
        return $this->hasOne('\App\Models\Rates','id', 'id_rate');
    }
}
