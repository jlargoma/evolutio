<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Services\LogsService;
use App\Models\User;
use App\Models\CoachRates;
use App\Models\CoachLiquidation;

class Salary extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'Salary:createMonthly';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create monthly salaries';

    
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
      $this->sLog = new LogsService('schedule','Salary');
      $year = date('Y');
      $month = date('m');
      
      
      $e_noSalary = $i_loaders = $i_already = [];
      $users = User::whereCoachs()->select('id')
              ->where('status', 1)->pluck('id');
      foreach ($users as $uID) {
        $taxCoach = CoachRates::where('id_user', $uID)->first();
        if ($taxCoach) {
          if ($taxCoach->salary>0){
                
            $oLiq = CoachLiquidation::where('id_coach', $uID)
              ->whereYear('date_liquidation', '=', $year)
              ->whereMonth('date_liquidation', '=', $month)
              ->first();
            
            if (!$oLiq){
              $oLiq = new CoachLiquidation();
              $oLiq->date_liquidation = "$year-$month-01";
              $oLiq->id_coach = $uID;
              $oLiq->salary = $taxCoach->salary;
              $oLiq->save();
              $i_loaders[] = $uID.'=>'.$taxCoach->salary;
            } else {
              if ($oLiq->salary<1){
                $oLiq->salary = $taxCoach->salary;
                $oLiq->save();
                $i_loaders[] = $uID.'=>'.$taxCoach->salary;
              } else {
                $i_already[] = $uID.'=>'.$oLiq->salary;
              }
            }
          } else {
            $e_noSalary[] = $uID;
          }
          
        } else $e_noSalary[] = $uID;
    
      }
      
      if (count($i_loaders)>0)  $this->sLog->info('Salario cargados ',$i_loaders);
      if (count($i_already)>0)  $this->sLog->info('Salario ya cargados ',$i_already);
      if (count($e_noSalary)>0)  $this->sLog->info('Salario no cargado ',$e_noSalary);
    } catch (\Exception $e) {
      $this->sLog->error('Exception: '.$e->getMessage());
    }
  }
}
