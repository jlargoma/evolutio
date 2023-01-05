<?php

namespace App\Console\Commands;

use App\Models\CashBoxs;
use Illuminate\Console\Command;
use App\Models\User;
use App\Services\MailsService;
use Log;
use App\Services\LogsService;
include_once app_path().'/Functions.php';
/**
 * /checkcrom/Remember/appointment
 */
class SendCashBox extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CashBox:sendMonthly';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send the CashBoxs of the month';

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
    $this->sLog = new LogsService('schedule','CashBoxs');
    try {
       $year = date('Y');
       /** Send Mail */
       $month = date('m');
       $aCoachs = User::getCoachs()->pluck('name', 'id');
       $lstCashbox = CashBoxs::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date')->get();
       $tableMail = '';
       if ($lstCashbox){
           $tableMail = '<table class="table"><tr><th>Día</th><th>Saldo</th><th>Ajuste</th><th>Concepto</th><th>Cierre por</th></tr>';
           $totalArqueo = 0;
           foreach($lstCashbox as $c){
               $tableMail .= '<tr>';
               $tableMail .= '<td class="nowrap">'.$c->date.'</td>';
               $tableMail .= '<td class="nowrap">'.moneda($c->saldo).'</td>';
               $tableMail .= '<td class="nowrap">'.moneda($c->ajuste).'</td>';
               $tableMail .= '<td>'.$c->concept.'</td>';
               $tableMail .= '<td>'. ( isset($aCoachs[$c->user_id]) ? $aCoachs[$c->user_id] : ' - ' ).'</td>';
               $tableMail .= '</tr>';
               $totalArqueo += $c->ajuste;
           }
           $tableMail .= '</table>';
           $tableMail .= '<p style="text-align: center;background-color: #e9e9e9;padding: 7px;"><b>Total Arqueos:</b> '.moneda($totalArqueo).'</p>';
       }
       $lstMonthsSpanish = lstMonthsSpanish();
       $MailsService = new MailsService();
       $month = intval($month);
       $MailsService->sendEmail_CashBoxs('',$lstMonthsSpanish[$month].' '.$year,$tableMail);
       /** Send Mail */

     
    } catch (\Exception $e) {
      $this->sLog->error('Exception: '.$e->getMessage());
    }
  }
  
}
