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
      Commands\Salary::class,
      Commands\SubscPaymentNextMonth::class,
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
      $schedule->command('Subscriptions:createRates')->dailyAt('3:00');
      $schedule->command('InfoMonth:weekStatus')->weeklyOn(7, '9:00');
      $schedule->command('Remember:appointment')->dailyAt('7:00');
      $schedule->command('Salary:createMonthly')->monthlyOn(25, '5:00'); // que lo genere el 25

      $schedule->command('SubscPayment:chargeNextMonth')->monthlyOn(23, '5:00');
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
