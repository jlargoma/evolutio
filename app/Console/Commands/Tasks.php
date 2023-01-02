<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserRates;
use App\Models\Rates;
use App\Models\User;
use Illuminate\Support\Facades\DB;

include_once app_path() . '/Functions.php';

class Tasks extends Command
{

  /**
   * The name and signature of the console command.
   *
   * @var string
   * /opt/plesk/php/7.3/bin/php artisan Tasks:start
   */
  protected $signature = 'Tasks:start';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Realizar tareas por consola';

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
  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {

    return null;

    $oLst = DB::table('user_meta')->where('meta_key', 'plan')->where('meta_value', 'fidelity')->pluck('user_id');
    if ($oLst) {
      foreach ($oLst as $uid) {

        $exists = DB::table('users_bonos')->where('user_id', $uid)->count();
        if ($exists<2) {

          $user = DB::table('users')->where('id', $uid)->first();
          echo 'https://desarrollo.evolutio.fit/admin/usuarios/informe/'.$uid."\n";
          echo $user->name."\n";
          DB::table('user_meta')
          ->where('meta_key','has_fidelity')
          ->where('user_id', $uid)
          ->update([ 'meta_value' => 0]);
        }
      }
    }
   

  }
}
