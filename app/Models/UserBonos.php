<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; //línea necesaria

class UserBonos extends Model
{
    protected $table = 'users_bonos';
    use SoftDeletes; //Implementamos 
	public function user()
    {
        return $this->hasOne('\App\Models\User', 'id', 'id_user');
    }

    public function bono()
    {
        return $this->hasOne('\App\Models\Bonos', 'id', 'id_bono');
    }
    public function charges()
    {
        return $this->hasOne('\App\Models\Charges', 'id', 'id_charges');
    }

    function check($IDs,$uID){
      if (!$IDs || count($IDs) == 0)
        return 'Debe seleccionar almenos un Bono';

      $lst = self::whereIN('id',$IDs)
              ->where('id_user',$uID)
              ->whereNull('charges_to')
              ->get();
      if (count($IDs) != count($lst))
        return 'Algunos Bonos son inválidos';
      return 'OK';
    }
    
    function usar($IDs,$charge){
      $lst = self::whereIN('id',$IDs)
              ->whereNull('charges_to')
              ->get();
    
      foreach ($lst as $item){
        $item->charges_to = $charge;
        $item->save();
      }
      return 'OK';
    }
}
