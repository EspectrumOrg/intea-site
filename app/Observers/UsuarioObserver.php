<?php

namespace App\Observers;

use App\Models\Usuario;

class UsuarioObserver
{
    public function created(Usuario $usuario)
    {
        // Seguir interesses padrão para novos usuários
        if (config('interesses.ativos', true)) {
            $interessesPadrao = \App\Models\Interesse::ativos()
                ->destaques()
                ->limit(3)
                ->pluck('id')
                ->toArray();
            
            //foreach ($interessesPadrao as $interesseId) {
               // $usuario->seguirInteresse($interesseId, true);
            //}
        }
    }

    public function updated(Usuario $usuario)
    {
        // Atualizar cache de interesses quando usuário mudar
        if ($usuario->isDirty(['onboarding_concluido'])) {
            cache()->forget("usuario_{$usuario->id}_interesses");
        }
    }

    /**
     * Handle the Usuario "deleted" event.
     */
    public function deleted(Usuario $usuario): void
    {
        //
    }

    /**
     * Handle the Usuario "restored" event.
     */
    public function restored(Usuario $usuario): void
    {
        //
    }

    /**
     * Handle the Usuario "force deleted" event.
     */
    public function forceDeleted(Usuario $usuario): void
    {
        //
    }
}
