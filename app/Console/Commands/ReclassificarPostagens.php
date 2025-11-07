<?php

namespace App\Console\Commands;

use App\Services\ServicoInteresses;
use Illuminate\Console\Command;

class ReclassificarPostagens extends Command
{
    protected $signature = 'interesses:reclassificar 
                            {--limite=100 : Número de postagens para processar}
                            {--usuarios : Migrar usuários antigos também}';
    
    protected $description = 'Reclassificar postagens antigas no sistema de interesses';

    public function handle()
    {
        $servico = app(ServicoInteresses::class);
        
        $this->info('Iniciando reclassificação de postagens...');
        
        // Reclassificar postagens
        $resultados = $servico->reclassificarPostagensAntigas($this->option('limite'));
        
        $this->info("✅ Postagens processadas: {$resultados['total_processadas']}");
        $this->info("✅ Interesses atribuídos: {$resultados['interesses_atribuidos']}");
        $this->info("✅ Postagens sem interesse: {$resultados['postagens_sem_interesse']}");
        
        // Migrar usuários se solicitado
        if ($this->option('usuarios')) {
            $this->info('\nMigrando usuários antigos...');
            $resultadosUsuarios = $servico->migrarUsuariosAntigos(50);
            
            $this->info("✅ Usuários processados: {$resultadosUsuarios['total_processados']}");
            $this->info("✅ Onboarding concluído: {$resultadosUsuarios['onboarding_concluido']}");
            $this->info("✅ Interesses atribuídos: {$resultadosUsuarios['interesses_atribuidos']}");
        }
        
        $this->info('\n🎉 Reclassificação concluída!');
    }
}