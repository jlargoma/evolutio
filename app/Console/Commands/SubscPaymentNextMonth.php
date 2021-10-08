<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersSuscriptions;
use App\Models\UserRates;
use Log;

class SubscPaymentNextMonth extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'SubscPayment:chargeNextMonth';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Charge rates to the next monthly';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    try {
      $lst = UsersSuscriptions::all();
      $year = date('Y');
      $month = date('m', strtotime('+1 months'));
      foreach ($lst as $s){
        $uID = $s->id_user;
        $rID = $s->id_rate;
        $uRate = UserRates::where('id_user',$uID)
                ->where('rate_year',$year)->where('rate_month',$month)
                ->where('id_rate', $rID)->first();
        if (!$uRate){
          $uRate = new UserRates();
          $uRate->id_user = $uID;
          $uRate->id_rate = $rID;
          $uRate->price   = $s->price;
          $uRate->rate_year = $year;
          $uRate->rate_month = $month;
          $uRate->save();

        }
      }
    } catch (\Exception $e) {
    Log::error("Error creando suscripciones");
    }
  }
}
