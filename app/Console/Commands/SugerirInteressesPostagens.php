<?php

namespace App\Console\Commands;

use App\Models\Postagem;
use Illuminate\Console\Command;

class SugerirInteressesPostagens extends Command
{
    protected $signature = 'interesses:sugerir 
                            {--postagem-id= : Processar postagem específica}
                            {--todas : Processar todas as postagens}';
    
    protected $description = 'Sugerir interesses para postagens baseado no conteúdo';

    public function handle()
    {
        if ($this->option('postagem-id')) {
            $postagem = Postagem::find($this->option('postagem-id'));
            
            if ($postagem) {
                $postagem->sugerirInteressesAutomaticos();
                $this->info("Postagem #{$postagem->id} processada!");
            } else {
                $this->error("Postagem não encontrada!");
            }
            
            return;
        }

        if ($this->option('todas')) {
            $this->processarTodasPostagens();
            return;
        }

        $this->info("Use --postagem-id=ID ou --todas para processar postagens");
    }

    private function processarTodasPostagens()
    {
        $total = Postagem::count();
        $bar = $this->output->createProgressBar($total);
        
        $this->info("Processando {$total} postagens...");
        
        Postagem::chunk(100, function ($postagens) use ($bar) {
            foreach ($postagens as $postagem) {
                try {
                    $postagem->sugerirInteressesAutomaticos();
                } catch (\Exception $e) {
                    $this->error("Erro ao processar postagem #{$postagem->id}: {$e->getMessage()}");
                }
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->info("\nTodas as postagens processadas!");
    }
}