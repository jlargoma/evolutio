<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersSuscriptions;
use App\Models\UserRates;
use App\Models\User;
use Log;
use App\Services\LogsService;
use Illuminate\Support\Facades\DB;

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
  private $sLog;

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
      $this->sLog = new LogsService('schedule', 'Suscripciones');
      $this->planControl();
      $this->createRates();
    } catch (\Exception $e) {
      $this->sLog->error('Exception: ' . $e->getMessage());
//    Log::error("Error creando suscripciones");
    }
  }

  function planControl() {
    $aRates = \App\Models\Rates::where('tarifa', 'fidelity')->pluck('price', 'id')->toArray();
    $aUID = User::where('status', 1)->pluck('id');
    $aPlans = DB::table('user_meta')->where('meta_key', 'plan')
                    ->whereIn('user_id', $aUID)->pluck('meta_value', 'user_id')->toArray();

    // Planes fidelity
    $lstSuscrip = \App\Models\UsersSuscriptions::whereIn('id_rate', array_keys($aRates))
                    ->whereIn('id_user', $aUID)
                    ->where('tarifa', '')->get();
    foreach ($lstSuscrip as $s) {
      $uID = $s->id_user;
      $price = $aRates[$s->id_rate];
      $plan = isset($aPlans[$uID]) ? $aPlans[$uID] : '';

      if ($plan == 'fidelity' && $price == $s->price) {
        $s->tarifa = 'fidelity';
        $s->save();
      }

      if ($plan == 'basic' && priceNoFidelity($price) == $s->price) {
        $s->tarifa = 'basic';
        $s->save();
      }
    }
  }

  function createRates() {


    $lst = UsersSuscriptions::select('users_suscriptions.*')
                    ->join('users', function ($join) {
                      $join->on('users.id', '=', 'id_user');
                      $join->on('users.status', '=', DB::raw("1"));
                    })->get();
    $year = date('Y');
    $month = date('m');
    if (count($lst) == 0)
      $this->sLog->info('No hay registros para ' . $month . '/' . $year);

    $creadas = $existentes = [];
    foreach ($lst as $s) {
      $uID = $s->id_user;
      $rID = $s->id_rate;
      $cID = $s->id_coach;
      $uRate = UserRates::where('id_user', $uID)
                      ->where('rate_year', $year)->where('rate_month', $month)
                      ->where('id_rate', $rID)->withTrashed()->first();
      if (!$uRate) {
        $uRate = new UserRates();
        $uRate->id_user = $uID;
        $uRate->id_rate = $rID;
        $uRate->coach_id = $cID;
        $uRate->price = $s->price;
        $uRate->tarifa = $s->tarifa;
        $uRate->rate_year = $year;
        $uRate->rate_month = $month;
        $uRate->save();
        $creadas[] = $uRate->id;
      } else {
        $existentes[] = $uRate->id;
      }
    }
    if (count($creadas) > 0)
      $this->sLog->info('tarifa creadas ', $creadas);
    if (count($existentes) > 0)
      $this->sLog->info('tarifa existentes ', $existentes);
  }

}
