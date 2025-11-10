<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define os commands da aplicação
     */
    protected $commands = [
        Commands\LimparModeracaoExpirada::class,
        Commands\SugerirInteressesPostagens::class,
    ];

    /**
     * Define o agendamento dos commands
     */
    protected function schedule(Schedule $schedule)
    {
        // Executar DIARIAMENTE às 3:00 AM
        $schedule->command('moderacao:limpar-expirada')->dailyAt('03:00');
        
        // Executar a cada HORA
        $schedule->command('interesses:sugerir --todas')->hourly();
    }

    /**
     * Registrar os commands
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}