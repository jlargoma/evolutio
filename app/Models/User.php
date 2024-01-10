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

  var $ratesTypes;

  public function rates()
  {
    return $this->hasMany('\App\Models\UserRates', 'id_user', 'id');
  }

  public function charges()
  {
    return $this->hasMany('\App\Models\Charges', 'id_user', 'id');
  }

  public function rateCoach()
  {
    return $this->hasOne('\App\Models\CoachRates', 'id_user', 'id');
  }
  public function userCoach()
  {
    return $this->hasOne('\App\Models\CoachUsers', 'id_user', 'id');
  }
  public function suscriptions()
  {
    return $this->hasMany('\App\Models\UsersSuscriptions', 'id_user', 'id');
  }
  public function bonos()
  {
    return $this->hasMany('\App\Models\UserBonos');
  }
  public function bonosServ($serv)
  {
    $lst = [];
    $total = 0;

    $oRate = Rates::find($serv);
    if (!$oRate) return [$total, $lst];

    $rate_subf = TypesRate::subfamily();
    $oType = $oRate->typeRate;
    $ubRsubfamily = ($oRate->subfamily) ? $oRate->subfamily : -1;
    $ubRateType   = ($oRate->type) ? $oRate->type : -1;
    $oBonos = UserBonos::where('user_id', $this->id)
      ->where(function ($query) use ($ubRateType, $ubRsubfamily) {
        $query->where("rate_subf", $ubRsubfamily)
          ->orWhere('rate_type', $ubRateType);
      })->get();
    if ($oBonos) {
      foreach ($oBonos as $b) {
        $name = '--';
        if ($b->rate_type) $name = $oType->name;
        if ($b->rate_subf) $name = $rate_subf[$b->rate_subf];
        $lst[] = [$b->id, $name, $b->qty];
        $total += $b->qty;
      }
    }
    return [$total, $lst];
  }

  static function whereCoachs($type = null, $includeAdmin = false)
  {

    switch ($type) {
      case 'fisio':
        $roles = ['fisio', 'teach_fisio'];
        break;
      case 'fisioG':
        $roles = ['fisioG'];
        break;
      case 'nutri':
        $roles = ['teach_nutri', 'nutri'];
        break;
      case 'teach':
        $roles = ['teach', 'teach_nutri', 'teach_fisio'];
        break;
      case 'empl':
        $roles = ['empl'];
        break;
      case 'esthetic':
        $roles = ['esthetic'];
        break;
      default:
        $roles = ['teach', 'fisio', 'nutri', 'empl', 'teach_nutri', 'teach_fisio', 'esthetic', 'fisioG'];
        break;
    }
    if ($includeAdmin) $roles[] = 'admin';

    return self::whereIn('role', $roles);
  }
  static function getCoachs($type = null, $includeAdmin = false)
  {
    return User::whereCoachs($type, $includeAdmin)
      ->where('status', 1)->orderBy('name')->orderBy('status', 'DESC')->get();
  }
  /**********************************************************************/
  /////////  user_meta //////////////
  public function newMetaContent($key, $content)
  {

    DB::table('user_meta')->insert(
      ['user_id' => $this->id, 'meta_key' => $key, 'meta_value' => $content]
    );
  }
  public function setMetaContent($key, $content)
  {


    $oMeta = DB::table('user_meta')
      ->where('user_id', $this->id)->where('meta_key', $key)->first();

    if ($oMeta) {
      DB::table('user_meta')->where('id', $oMeta->id)->update(['meta_value' => $content]);
    } else {
      DB::table('user_meta')->insert(
        ['user_id' => $this->id, 'meta_key' => $key, 'meta_value' => $content]
      );
    }
    return null;
  }

  public function getMetaContent($key)
  {

    $oMeta = DB::table('user_meta')
      ->where('user_id', $this->id)->where('meta_key', $key)->first();

    if ($oMeta) {
      return $oMeta->meta_value;
    }
    return null;
  }


  public function setMetaContentGroups($metaDataUPD, $metaDataADD)
  {
    if (count($metaDataUPD)) {
      $d = [];
      foreach ($metaDataUPD as $k => $v) {
        $oMeta = DB::table('user_meta')
          ->where('user_id', $this->id)->where('meta_key', $k)->first();
        if ($oMeta) {
          $updated =  DB::table('user_meta')->where('id', $oMeta->id)
            ->update(['meta_value' => $v]);
        } else {
          $metaDataADD[$k] = $v;
        }
      }
    }
    if (count($metaDataADD)) {
      $d = [];
      foreach ($metaDataADD as $k => $v) $d[] = ['user_id' => $this->id, 'meta_key' => $k, 'meta_value' => $v];
      DB::table('user_meta')->insert($d);
    }
  }

  public function getMetaContentGroups($keys)
  {

    return DB::table('user_meta')
      ->where('user_id', $this->id)->whereIn('meta_key', $keys)
      ->pluck('meta_value', 'meta_key')->toArray();
  }

  public function getMetaUserID_byKey($keys, $val = null)
  {

    $sql = DB::table('user_meta')
      ->where('meta_key', $keys);
    if ($val)
      $sql->where('meta_value', $val);

    return $sql->pluck('user_id')->toArray();
  }

  function getPayCard()
  {

    $paymentMethod = null;
    try {
      return $this->paymentMethods()->first();
    } catch (\Exception $ex) {
      return null;
    }
  }


  public function getPlan()
  {
    return $this->getMetaContent('plan');
  }

  static function changeColors($colors)
  {
    if (!$colors || count($colors) < 1) return $colors;
    $aColores = json_decode(Settings::getContent('usr_colors'), true);
    if (!$aColores) $aColores = [];
    foreach ($colors as $k => $v) {
      if (isset($aColores[$k]) && $aColores[$k] != '#000000') {
        $colors[$k] = $aColores[$k];
      }
    }
    return $colors;
  }


  function create_altaBajas($year, $month)
  {

    $create = false;
    if ((date('Y') == $year && date('m') < $month) || date('Y') < $year ) return;
    if (date('Y') == $year && date('m') == $month) {
      DB::table('user_alta_baja')->where('year_month', $year . '-' . $month)->delete();
      $create = true;
    } else {
      $exist = DB::table('user_alta_baja')->where('year_month', $year . '-' . $month)->count();
      if (!$exist || $exist == 0) $create = true;
    }

    if ($create) {
      $this->ratesTypes = Rates::getTypeRatesGroups(false,true);
      $sueloPelvico  = Rates::where('name', 'like', '%pelvico%')->pluck('id')->toArray();
      foreach($this->ratesTypes as $k=>$v){
        foreach($v as $k2=>$v2){
          if (in_array($v2,$sueloPelvico)){
            unset($this->ratesTypes[$k][$v2]);
          }
        }
      }
      $this->ratesTypes[99] = $sueloPelvico;
      if ($month == 1){
        $lastmonth = 12;
        $lastYear = $year-1;
      } else {
        $lastYear = $year;
        $lastmonth = $month-1;
      }
      $oUsrRates = UserRates::where('rate_year',$lastYear)->where('rate_month',$lastmonth)->get();
      $uRatesYesterday = [];
      foreach($oUsrRates as $item){
        $rTypeUsr = $this->getRateTypeUsr($item->id_rate);
        if ($rTypeUsr){
          if (array_key_exists($item->id_user,$uRatesYesterday)) $uRatesYesterday[$item->id_user] = [];
          $uRatesYesterday[$item->id_user][] = $rTypeUsr;
        }
      }

      $oUsrRates = UserRates::where('rate_year',$year)->where('rate_month',$month)->get();
      $uRatesNow = [];
      foreach($oUsrRates as $item){
        $rTypeUsr = $this->getRateTypeUsr($item->id_rate);
        if ($rTypeUsr){
          if (array_key_exists($item->id_user,$uRatesNow)) $uRatesNow[$item->id_user] = [];
          $uRatesNow[$item->id_user][] = $rTypeUsr;
        }
      }


      $keyMonth = $year . '-' . $month;
      $aItemsDB = [];
      /** altas */
      foreach($uRatesNow as $uId => $rTypes){
        $lstIds = [];
        if (array_key_exists($uId,$uRatesYesterday)){
          $aux = $uRatesYesterday[$uId];
          foreach($rTypes as $rtID){
            if (!in_array($rtID,$aux)){
              $lstIds[] =  $rtID;
            }
          }
        } else {
          $lstIds = $rTypes;
        }
        if (count($lstIds)){
          foreach($lstIds as $rtID)
            $aItemsDB[] = "($uId,'$keyMonth',$rtID,1)";
        }
      }
      /** bajas */
      foreach($uRatesYesterday as $uId => $rTypes){
        $lstIds = [];
        if (array_key_exists($uId,$uRatesNow)){
          $aux = $uRatesNow[$uId];
          foreach($rTypes as $rtID){
            if (!in_array($rtID,$aux)){
              $lstIds[] =  $rtID;
            }
          }
        } else {
          $lstIds = $rTypes;
        }
        if (count($lstIds)){
          foreach($lstIds as $rtID)
          $aItemsDB[] = "($uId,'$keyMonth',$rtID,0)";
        }
      }

      DB::select("INSERT user_alta_baja(`user_id`, `year_month`, `rate_type`, `active` ) VALUES ". implode(',',$aItemsDB));
    }
  }

  private function getRateTypeUsr($id_rate){
    foreach($this->ratesTypes as $rt => $rIds){
      if (in_array($id_rate,$rIds)){
        return $rt;
      }
    }
    return null;
  }



  static function altaBajas($year, $month)
  {

    // $sql = User::select('users.*','user_alta_baja.rate_type','user_alta_baja.active')->join('user_alta_baja', function ($join) {
    //   $join->on('users.id', '=', 'user_alta_baja.user_id');
    // })->where('year_month', $year . '-' . $month)->whereIn('rate_type', [1,2]);

    $lstAltBaj = UsersSuscriptions::where(function($query) use ($year, $month) {
          $query->whereYear('deleted_at', $year)->whereMonth('deleted_at',$month);
      })->orWhere(function($query) use ($year, $month) {
        $query->whereYear('created_at', $year)->whereMonth('created_at',$month);
    })->withTrashed()->get();
    $uIDs = [];
    if ($lstAltBaj){
      foreach ($lstAltBaj as $item) {
        $uIDs[$item->id_user] = $item->id_user;
      }
    }


    $sql = User::whereIn('id',$uIDs);




    return $sql;

  }

  static function usersRatesFamilyMonths($year, $month, $fFamily)
  {

    $sueloPelvico  = Rates::where('name', 'like', '%pelvico%')->pluck('id')->toArray();
    $sueloPelvico = implode(',',$sueloPelvico);
    $date = date('Y-m-d', strtotime($year . '-' . $month . '-01' . ' -1 month'));
    $sqlDate = [];
    $monthAux = date('m', strtotime($date));
    $yearAux = date('Y', strtotime($date));
    for ($i = 0; $i < 3; $i++) {

      $sqlDate[] = "( rate_month = $monthAux AND rate_year = $yearAux)";
      $next = strtotime($date . ' +1 month');
      $date = date('Y-m-d', $next);
      $monthAux = date('m', $next);
      $yearAux = date('Y', $next);
    }

    $sqlDate = '(' . implode(' OR ', $sqlDate) . ')';

    $returnIDs = [];

    if ($fFamily == -1) { //sueloPelvico



      $sql = 'SELECT users.id
              FROM `users`
              LEFT JOIN users_rates ON users_rates.id_user = users.id
              WHERE `users_rates`.`deleted_at` is null 
              AND users_rates.id_rate IN (' . $sueloPelvico . ') 
              AND ' . $sqlDate . '
              GROUP BY users.id
            ';
    } else {
      $sql = 'SELECT users.id
              FROM `users`
              LEFT JOIN users_rates ON users_rates.id_user = users.id
              INNER JOIN rates ON rates.id = users_rates.id_rate 
              WHERE `users_rates`.`deleted_at` is null 
              AND rates.type = '.$fFamily.'
              AND users_rates.id_rate NOT IN (' . $sueloPelvico . ') 
              AND ' . $sqlDate . '
              GROUP BY users.id
            ';

      
    }
    $lstUsrTypeRate = DB::select($sql);
    if ($lstUsrTypeRate){
      foreach($lstUsrTypeRate as $u)
      $returnIDs[] = $u->id;
    }
    return $returnIDs;
  }

  function getCoachAsig(){
    global $aUsrsCoachs;
    if (isset($aUsrsCoachs[$this->id])) return $aUsrsCoachs[$this->id];

    $oLstAux = UsersSuscriptions::where('id_user',$this->id)->get();
    if($oLstAux){
      foreach($oLstAux as $s){
        return $s->id_coach;
      }
    }

    return null;
  }


  function print_convenio(){
    if (!$this->convenio) return '--';
    $lstConvenios = \App\Models\Convenios::all()->pluck('name','id')->toArray();
    return array_key_exists($this->convenio,$lstConvenios) ? $lstConvenios[$this->convenio] : '--';
  }
}
