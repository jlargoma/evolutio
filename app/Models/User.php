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
    $ubRsubfamily = ($oRate->subfamily) ? $oRate->subfamily : -1;
    $ubRateType   = ($oRate->type) ? $oRate->type : -1; 
    $oBonos = UserBonos::where('user_id',$this->id)
            ->where(function ($query) use ($ubRateType,$ubRsubfamily) {
                $query->where("rate_subf",$ubRsubfamily)
                ->orWhere('rate_type',$ubRateType);
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

  static function whereCoachs($type=null,$includeAdmin=false) {
    
    switch ($type){
      case 'fisio':
        $roles = ['fisio','teach_fisio'];
        break;
      case 'nutri':
        $roles = ['teach_nutri','nutri'];
        break;
      case 'teach':
        $roles = ['teach','teach_nutri','teach_fisio'];
        break;
      case 'empl':
        $roles = ['empl'];
        break;
      case 'esthetic':
        $roles = ['esthetic'];
        break;
      default:
        $roles = ['teach','fisio','nutri','empl','teach_nutri','teach_fisio','esthetic'];
        break;
    }
    if ($includeAdmin) $roles[] = 'admin';
    
    return self::whereIn('role',$roles);
   
  }
  static function getCoachs($type=null,$includeAdmin=false) {
    return User::whereCoachs($type,$includeAdmin)
            ->where('status', 1)->orderBy('name')->orderBy('status', 'DESC')->get();
    
  }
  /**********************************************************************/
  /////////  user_meta //////////////
  public function newMetaContent($key,$content) {
    
      DB::table('user_meta')->insert(
            ['user_id' => $this->id, 'meta_key' => $key,'meta_value' => $content]
        );
  }
  public function setMetaContent($key,$content) {
    
       
    $oMeta = DB::table('user_meta')
            ->where('user_id',$this->id)->where('meta_key',$key)->first();
    
    if ($oMeta) {
      DB::table('user_meta')->where('id',$oMeta->id)->update(['meta_value' => $content]);
    }else {
      DB::table('user_meta')->insert(
            ['user_id' => $this->id, 'meta_key' => $key,'meta_value' => $content]
        );
    }
    return null;
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
    if (count($metaDataUPD)){
      $d = [];
      foreach ($metaDataUPD as $k=>$v){
        $oMeta = DB::table('user_meta')
            ->where('user_id',$this->id)->where('meta_key',$k)->first();
        if ($oMeta){
          $updated =  DB::table('user_meta')->where('id',$oMeta->id)
              ->update(['meta_value' => $v]);
          
        } else {
          $metaDataADD[$k] = $v;
        }
      }
    }
    if (count($metaDataADD)){
      $d = [];
      foreach ($metaDataADD as $k=>$v) $d[] = ['user_id'=>$this->id,'meta_key'=>$k,'meta_value'=>$v];
      DB::table('user_meta')->insert($d);
    }

  }
  
  public function getMetaContentGroups($keys) {
    
    return DB::table('user_meta')
            ->where('user_id',$this->id)->whereIn('meta_key',$keys)
            ->pluck('meta_value','meta_key')->toArray();
    
  }
  
  public function getMetaUserID_byKey($keys,$val=null) {
    
    $sql = DB::table('user_meta')
            ->where('meta_key',$keys);
    if ($val)
      $sql->where('meta_value',$val);
     
    return $sql->pluck('user_id')->toArray();
    
  }
  
  function getPayCard(){
      
    $paymentMethod = null;
    try {
    return $this->paymentMethods()->first();
    } catch (\Exception $ex) {
      return null;
    }
  }
  
  
  public function getPlan() {
    return $this->getMetaContent('plan');
  }

  static function changeColors($colors){
    if (!$colors || count($colors)<1) return $colors;
    $aColores = json_decode(Settings::getContent('usr_colors'),true);
    if (!$aColores) $aColores = [];
    foreach($colors as $k=>$v){
      if (isset($aColores[$k]) && $aColores[$k] != '#000000'){
        $colors[$k] = $aColores[$k];
      }
    }
    return $colors;
  }


  static function create_altaBajas($year,$month){

    $create = false;

    if (date('Y') == $year && date('m') == $month){
      DB::table('user_alta_baja')->where('year_month',$year.'-'.$month)->delete();
      $create = true;
    }
    else {
      $exist = DB::table('user_alta_baja')->where('year_month',$year.'-'.$month)->count();
      if ( !$exist || $exist==0 ) $create = true;
    }
 
    if ( $create ){
          $lstUser = "INSERT user_alta_baja(`user_id`, `year_month`) (SELECT users.id, '".$year.'-'.$month."' AS 'year_mont'  FROM users INNER JOIN user_meta ON users.id = user_meta.user_id
          WHERE role = 'user' AND ( 
                                    ( 
                                      ( meta_key = 'activate' OR meta_key = 'disable') 
                                      AND YEAR(user_meta.created_at) = $year AND MONTH(user_meta.created_at) = $month
                                    ) 
                                    OR 
                                    (
                                      YEAR(users.created_at) = $year AND MONTH(users.created_at) = $month
                                    )
                                  )
                                  group by users.id
          )
        ";
        DB::select($lstUser);
    }


  }
  static function altaBajas($year,$month){

    $sql = User::select('users.*')->join('user_alta_baja', function ($join) {
      $join->on('users.id', '=', 'user_alta_baja.user_id');
    })->where('year_month',$year.'-'.$month);
    return $sql;

  }

}
