<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Services\LogsService;
use App\Models\User;
use App\Models\UserRates;

class LoadCharged extends Command
{

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'LoadCharged:proccess';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Load Charged value on uRates';


  private $sLog;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    try {

      $year = date('Y');
      $this->createData($year);
      $this->addSenial($year);

      $year--;
      $this->createData($year);
      $this->addSenial($year);

      $year--;
      $this->createData($year);
      $this->addSenial($year);
      
    } catch (\Exception $e) {
      dd($e->getMessage());
      $this->sLog->error('Exception: ' . $e->getMessage());
    }
  }

  function createData($year)
  {
    $uRates = UserRates::select('users_rates.id', 'users_rates.charged', 'users_rates.charged_method', 'charges.type_payment', 'charges.import')
      ->where('rate_year', $year)->leftJoin('charges', 'id_charges', 'charges.id')->get(); //->where('users_rates.id', 21097)
    foreach ($uRates as $item) {
      if (!$item->import) {
        UserRates::where('id', $item->id)
          ->update(['charged' => 0, 'charged_method' => null]);
      }
      if ($item->import != $item->charged || $item->type_payment != $item->charged_method) {
        UserRates::where('id', $item->id)
          ->update(['charged' => $item->import, 'charged_method' => $item->type_payment]);
      }
    }
  }

  function addSenial(){
    $lst = \DB::select('SELECT T2.id,T3.meta_value FROM appointment as T1 INNER JOIN users_rates AS T2 ON T1.id_user_rates = T2.id INNER JOIN `appointment_meta` as T3 ON T1.id = T3.appoin_id WHERE T2.rate_year = 2023 AND T3.meta_key = "senial_price"');
    if($lst){
      foreach($lst as $v){
        UserRates::where('id', $v->id)->increment('charged', $v->meta_value);
      }
    }

    
  }
}
