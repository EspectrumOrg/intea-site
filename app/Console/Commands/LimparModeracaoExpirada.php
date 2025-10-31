<?php

namespace App\Console\Commands;

use App\Services\ServicoModeracao;
use Illuminate\Console\Command;

class LimparModeracaoExpirada extends Command
{
    protected $signature = 'moderacao:limpar-expirada';
    
    protected $description = 'Limpar alertas e expulsões expiradas do sistema de moderação';

    public function handle()
    {
        $this->info('Limpando moderação expirada...');
        
        $alertasExpirados = ServicoModeracao::expirarAlertasAntigos();
        $this->info("{$alertasExpirados} alertas expirados desativados");
        
        $expulsoesExpiradas = ServicoModeracao::removerExpulsoesExpiradas();
        $this->info("{$expulsoesExpiradas} expulsões expiradas removidas");
        
        $this->info('Limpeza concluída!');
    }
}