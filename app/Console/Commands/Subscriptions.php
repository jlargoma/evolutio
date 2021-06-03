<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersSuscriptions;
use App\Models\UserRates;
use Log;

class Subscriptions extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'Subscriptions:createRates';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create monthly rates';

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
      $month = date('m');
      foreach ($lst as $s){
        $uID = $s->id_user;
        $rID = $s->id_rate;
        $cID = $s->id_coach;
        $uRate = UserRates::where('id_user',$uID)
                ->where('rate_year',$year)->where('rate_month',$month)
                ->where('id_rate', $rID)->first();
        if (!$uRate){
          $uRate = new UserRates();
          $uRate->id_user = $uID;
          $uRate->id_rate = $rID;
          $uRate->coach_id = $cID;
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
