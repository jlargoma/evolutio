<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function rates() {
    return $this->hasMany('\App\Models\UserRates', 'id_user', 'id');
  }

  public function charges() {
    return $this->hasMany('\App\Models\Charges', 'id_user', 'id');
  }

  public function rateCoach() {
    return $this->hasOne('\App\Models\CoachRates', 'id_user', 'id');
  }
  public function userCoach() {
    return $this->hasOne('\App\Models\CoachUsers', 'id_user', 'id');
  }
  public function suscriptions() {
    return $this->hasMany('\App\Models\UsersSuscriptions', 'id_user', 'id');
  }

  
  /**********************************************************************/
  /////////  user_meta //////////////
  public function setMetaContent($key,$content) {
    DB::table('user_meta')
    ->updateOrInsert(
        ['user_id' => $this->id, 'meta_key' => $key],
        ['meta_value' => $content]
    );
  }
  public function getMetaContent($key) {
    
    $oMeta = DB::table('user_meta')
            ->where('user_id',$this->id)->where('meta_key',$key)->first();
    
    if ($oMeta) {
      return $oMeta->meta_value;
    }
    return null;
  }
  
  function getPayCard(){
      
    $paymentMethod = null;
    try {
    return $this->paymentMethods()->first();
    } catch (\Exception $ex) {
      return null;
      /*$ex->getMessage();*/
      
    }
    
//    $obj = $this->paymentMethods()->first();
  }
}
