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
/*
    $oLst = DB::table('user_meta')->where('meta_key', 'plan')->where('meta_value', 'fidelity')->pluck('user_id');
    if ($oLst) {
      foreach ($oLst as $uid) {

        $exists = DB::table('user_meta')
          ->where('meta_key', 'has_fidelity')
          ->where('user_id', $uid)->first();

        if (!$exists) {
          DB::table('user_meta')->insert([
            'meta_key' => 'has_fidelity',
            'user_id' => $uid,
            'meta_value' => 1
          ]);
        }
      }
    }
    */

  }
}
