<?php

namespace App\Console;

use App\Helpers\IntegratorHelper;
use App\Helpers\AnuncioHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $integratoHelper = new IntegratorHelper();
            $integratoHelper->run();
        })->everyTwoHours();

        $schedule->call(function () {
            AnuncioHelper::processaLicencasVencidas();
            AnuncioHelper::processaAnunciosVencidos();
        })->dailyAt('00:01');
        
        $schedule->call(function () {
            AnuncioHelper::enviarRelatorioCliques();
        })->dailyAt('18:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
