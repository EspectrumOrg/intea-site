<?php

namespace App\Observers;

use App\Models\Postagem;

class PostagemObserver
{
   public function created(Postagem $postagem)
    {
        // Processamento automático em background
        if (config('interesses.ativos', true)) {
            \Illuminate\Support\Facades\Queue::push(function () use ($postagem) {
                $postagem->sugerirInteressesAutomaticos();
                
                // Verificar violações apenas se moderação ativa
                if (config('interesses.moderacao_automatica', true)) {
                    $violacoes = $postagem->verificarViolacoesPalavrasProibidas();
                    
                    if (!empty($violacoes)) {
                        $palavras = [];
                        foreach ($violacoes as $interesseViolacoes) {
                            foreach ($interesseViolacoes as $violacao) {
                                $palavras[] = $violacao->palavra;
                            }
                        }
                        
                        $postagem->bloquearAutomaticamente(
                            "Palavras proibidas: " . implode(', ', array_slice(array_unique($palavras), 0, 3))
                        );
                    }
                }
            });
        }
    }

    public function updated(Postagem $postagem)
    {
        // Reprocessar interesses se conteúdo mudou
        if ($postagem->isDirty('texto_postagem')) {
            $postagem->interesses()->detach();
            $postagem->sugerirInteressesAutomaticos();
        }
    }

    public function deleted(Postagem $postagem)
    {
        // Atualizar contadores dos interesses
        foreach ($postagem->interesses as $interesse) {
            $interesse->atualizarContadores();
        }
    }

    /**
     * Handle the Postagem "restored" event.
     */
    public function restored(Postagem $postagem): void
    {
        //
    }

    /**
     * Handle the Postagem "force deleted" event.
     */
    public function forceDeleted(Postagem $postagem): void
    {
        //
    }
}
