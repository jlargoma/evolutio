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

    public function coach()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_coach');
    }

    public function uRates()
    {
        return $this->hasOne('\App\Models\UserRates', 'id', 'id_user_rates');
    }
    public function getHour()
    {
      if ($this->customTime){
        $hour= $this->customTime;
        $aux = explode(':', $hour);
        if (is_array($aux) && count($aux)>2) $hour = $aux[0].':'.$aux[1];
        return $hour;
      }
      
      $dateTime = strtotime($this->date);
      return date('H:i',$dateTime);
    }
    public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_user');
    }
}
