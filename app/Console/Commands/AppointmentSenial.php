<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rates;
use App\Models\Dates;
use Illuminate\Support\Facades\Mail;
use Log;
use App\Services\LogsService;
/**
 */
class AppointmentSenial extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'AppointmentSenial:proccess';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Delete Appointment with not pay';

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
      $this->sLog = new LogsService('schedule','Borrar Cita señal');
      $date = date('Y-m-d H:i',strtotime('-3 hours')).':01';
      $lst = Dates::where('senial',1)->whereDate('created_at','<',$date)->get();
      if (count($lst)>0){
        foreach ($lst as $item){
          $uRate = $item->uRates;
          if ($uRate){
            $uRate->delete();
            $item->delete();
            $sent = $this->sendEmail($item, $uRate->user, $uRate->rate,$item->coach);
            if ($sent == 'OK')
              $this->sLog->info('Enviado ',$item->id);
            else
              $this->sLog->error('Enviar: '.$sent,$item->id);
            
          }
        }
      } else {
        $this->sLog->info('No hay citas para '.$date);
      }
    } catch (\Exception $e) {
        var_dump($e->getMessage());die;
      $this->sLog->error('Exception: '.$e->getMessage());
    }
  }
  
  function sendEmail($oDate, $oUser, $oRate,$oCoach){
    
            $email= $oUser->email;
            $hour = $oDate->getHour();
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email.' no es un mail válido';
            try{
              $subj = 'Cancelación de su Cita de Evolutio';
              
              
              $rateLst = [$oRate->name];
              $extrs = explode(',',$oDate->getMetaContent('extrs'));
              if (count($extrs)>0){
                $rNames = Rates::whereIn('id',$extrs)->pluck('name');
                if ($rNames){
                  foreach($rNames as $n){
                    $rateLst[] = $n;
                  }
                }
  
              }

              $strtotime = strtotime($oDate->date);
              $day = date('d',$strtotime).' de '.getMonthSpanish(date('n',$strtotime),false);
              $mailData = [
                      'user'    => $oUser,
                      'date_type' => $oDate->date_type,
                      'rate'    => $oRate,
                      'rateLst'    => $rateLst,
                      'oCoach'  => $oCoach,
                      'hour'    => $hour,
                      'day'     => $day,
              ];
              
              $sended = Mail::send('emails._cancel_citaStripe', $mailData,
                  function ($message) use ($email,$subj) {
                      $message->subject($subj);
                      $message->from(config('mail.from.address'), config('mail.from.name'));
                      $message->to($email);
              });
            } catch (\Exception $ex) {
              return ($ex->getMessage());
            }
            return 'OK';
	}
}
