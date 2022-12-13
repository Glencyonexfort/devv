<?php

namespace App\Console;

use App\Console\Commands\VendorCleanUpCommand;
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
        Commands\UpdateExchangeRates::class,
        Commands\AutoStopTimer::class,
        Commands\AutoQuote::class,
        Commands\AutoQuoteCleaning::class,
        Commands\AutoEmailEveryFourHour::class,
        Commands\XeroSyncing::class,
        Commands\MyobSyncing::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update-exchange-rate')->daily();
        $schedule->command('auto-stop-timer')->daily();
        $schedule->command('auto-quote')->cron('*/2	* * * *');
        $schedule->command('auto-quote-cleaning')->cron('*/4 * * * *');
        // $schedule->command('auto-email-every-four-hours')->cron('0 */4 * * *');
        $schedule->command('auto-email-every-four-hours')->cron('*/4 * * * *');
        $schedule->command('xero-syncing')->cron('*/4 * * * *');
        $schedule->command('myob-syncing')->cron('*/4 * * * *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
