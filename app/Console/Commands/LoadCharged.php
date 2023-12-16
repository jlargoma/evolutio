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
      $uRates = UserRates::select('users_rates.id','users_rates.charged','users_rates.charged_method', 'charges.type_payment', 'charges.import')
        ->where('rate_year', $year)->leftJoin('charges', 'id_charges', 'charges.id')->get();
      foreach ($uRates as $item) {
        if ($item->import != $item->charged || $item->type_payment != $item->charged_method){
          dd($item);
          UserRates::where('id', $item->id)
          ->update(['charged' => $item->import, 'charged_method' => $item->type_payment]);
        }
      }

      $year--;
      $uRates = UserRates::select('users_rates.id','users_rates.charged','users_rates.charged_method', 'charges.type_payment', 'charges.import')
        ->where('rate_year', $year)->leftJoin('charges', 'id_charges', 'charges.id')->get();
      foreach ($uRates as $item) {
        if ($item->import != $item->charged || $item->type_payment != $item->charged_method)
          UserRates::where('id', $item->id)
            ->update(['charged' => $item->import, 'charged_method' => $item->type_payment]);
      }

    } catch (\Exception $e) {
      dd($e->getMessage());
      $this->sLog->error('Exception: ' . $e->getMessage());
    }

  }
}
