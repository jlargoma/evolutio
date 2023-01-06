<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use User;

class UsersSuscriptions extends Model
{
        
    use SoftDeletes; //Implementamos 
    public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_user');
    }
    public function rate()
    {
        return $this->hasOne('\App\Models\Rates', 'id', 'id_rate');
    }

    static function getCountBySuscipt($plan){

        $count = DB::select('SELECT count(*) as cant
        FROM users 
        INNER JOIN user_meta ON user_meta.user_id = users.id 
        WHERE users.status = 1 AND user_meta.meta_key = "plan" AND user_meta.meta_value = "'.$plan.'"');
        if ($count) return $count[0]->cant;
        
        return 0;
    }
}
