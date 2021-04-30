<?php

namespace App\Services\temps;

use Illuminate\Support\Facades\DB;
use App\Models\User;
/*
 * UPDATE `0_temp_users2`  SET user_id = null;
UPDATE `0_temp_users2`  SET user_id = (SELECT users.id FROM users WHERE users.name = `0_temp_users2`.`name`)
 */
class ImportCustomers {

  public function __construct() {
    
  }
  
  public function import($tipe) {
    switch ($tipe){
      case 'clientes':
        $this->importCustomer();
        break;
      case 'rates':
        $this->loadRates();
        break;
    }
  }
  public function importCustomer() {
    
    
//    $cutomers = DB::table('0_temp_users2')->get();
//    foreach ($cutomers as $c){
//      
//      
//      $email = str_replace(' ','_', $c->name).'@evolutio.fit';
//      $email = str_replace('__','_',$email);
//      
//      DB::table('0_temp_users2')->where('id',$c->id)->update(['email'=>$email]);
//    }

    $cutomers = DB::table('0_temp_users2')->whereNull('user_id')->get();
    $lst = [];
    foreach ($cutomers as $c){
      if(!isset($lst[$c->email])){
        $lst[$c->email] = $c;
      }
    }
    foreach ($lst as $c){
      $exist = User::where('email',$c->email)->first();
      if ($exist){
        DB::table('0_temp_users2')->where('email',$c->email)->update(['user_id'=>$exist->id]);
      } else {
        $obj = new User();
        $obj->name = $c->name;
        $obj->email = $c->email;
        $obj->password = str_random(60);
        $obj->remember_token = str_random(60);
        $obj->role = 'user';
        $obj->telefono = '';
        $obj->save();
        DB::table('0_temp_users2')->where('email',$c->email)->update(['user_id'=>$obj->id]);
      }
      
    }
    
  }
  public function loadRates() {
    $cutomers = DB::table('0_temp_users2')->get();
    $tRates = \App\Models\Rates::pluck('type','id')->toArray();
    foreach ($cutomers as $c){
      $oCobro = new \App\Models\Charges();
      $oCobro->id_user = $c->user_id;
      $oCobro->date_payment = $c->date;
      $oCobro->id_rate = $c->rate_id;
      $oCobro->type_payment = strtolower($c->tpay);
      $oCobro->type = 1;
      $oCobro->import = $c->price;
      $oCobro->discount = 0;
      $oCobro->type_rate = $tRates[$c->rate_id];
      $oCobro->save();

      /**************************************************** */

      $newRate = new \App\Models\UserRates();
      $newRate->id_user = $c->user_id;
      $newRate->id_rate = $c->rate_id;
      $newRate->rate_year = date('Y', strtotime($c->date));
      $newRate->rate_month = date('n', strtotime($c->date));
      $newRate->id_charges = $oCobro->id;
      $newRate->coach = $c->coach;
      $newRate->save();
      
    }
    
  }
  
}
