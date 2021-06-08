<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      Commands\Subscriptions::class,
      Commands\InfoMonth::class,
      Commands\RememberAppointment::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//      $schedule->command('inspire')->hourly();   
      $schedule->command('Subscriptions:createRates')->dailyAt('3:00')->timezone('Europe/Madrid');
      $schedule->command('InfoMonth:weekStatus')->weeklyOn(7, '9:00')->timezone('Europe/Madrid');
      $schedule->command('Remember:appointment')->dailyAt('7:00')->timezone('Europe/Madrid');
  }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
