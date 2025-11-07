<?php

namespace App\Providers;

use App\Models\Usuario;
use App\Models\Postagem;
use App\Observers\UsuarioObserver;
use App\Observers\PostagemObserver;
use Illuminate\Support\ServiceProvider;

class InteresseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar serviços
        $this->app->singleton('servico.interesses', function ($app) {
            return new \App\Services\ServicoInteresses();
        });
        
        $this->app->singleton('servico.moderacao', function ($app) {
            return new \App\Services\ServicoModeracao();
        });
    }

    public function boot()
    {
        // Registrar observers
        Usuario::observe(UsuarioObserver::class);
        Postagem::observe(PostagemObserver::class);
        
        // Compartilhar dados com todas as views
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'usuarioInteresses' => auth()->user()->interesses,
                    'interessesSugeridos' => auth()->user()->obterInteressesSugeridos(5)
                ]);
            }
        });
    }
}