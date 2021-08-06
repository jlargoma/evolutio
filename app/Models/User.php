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
  public function bonos() {
    return $this->hasMany('\App\Models\UserBonos');
  }
  public function bonosServ($serv) {
    $lst = [];
    $total = 0;
    
    $oRate = Rates::find($serv);
    if(!$oRate) return [$total,$lst];
    
    $rate_subf = TypesRate::subfamily();
    $oType = $oRate->typeRate;
    $oBonos = UserBonos::where('user_id',$this->id)
            ->where(function ($query) use ($oRate) {
                $query->where("rate_subf",$oRate->subfamily)
                ->orWhere('rate_type',$oRate->type);
            })->get();
    if ($oBonos){
      foreach ($oBonos as $b){
        $name = '--';
        if ($b->rate_type) $name = $oType->name;
        if ($b->rate_subf) $name = $rate_subf[$b->rate_subf];
        $lst[] = [$b->id,$name,$b->qty];
        $total += $b->qty;
      }
    }
    return [$total,$lst];
  }

  static function getCoachs($type=null) {
    $sql = User::where('status', 1);
    
    if($type)  $sql->where('role', $type);
    else $sql->whereIn('role', ['teach','fisio','nutri']);
    
    return $sql->orderBy('status', 'DESC')->get();
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
  
  
  public function setMetaContentGroups($metaDataUPD,$metaDataADD) {
    $d = [];
    if (count($metaDataUPD))
      foreach ($metaDataUPD as $k=>$v) $d[] = ['user_id'=>$this->id,'meta_key'=>$k,'meta_value'=>$v];
    
    if (count($metaDataADD))
      foreach ($metaDataADD as $k=>$v) $d[] = ['user_id'=>$this->id,'meta_key'=>$k,'meta_value'=>$v];
    
    if (count($d))
      DB::table('user_meta')->upsert($d, ['user_id', 'meta_key'], ['meta_value']);

  }
  
  public function getMetaContentGroups($keys) {
    
    return DB::table('user_meta')
            ->where('user_id',$this->id)->whereIn('meta_key',$keys)
            ->pluck('meta_value','meta_key')->toArray();
    
  }
  
  function getPayCard(){
      
    $paymentMethod = null;
    try {
    return $this->paymentMethods()->first();
    } catch (\Exception $ex) {
      return null;
    }
  }
}
