<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        $usuario = auth()->user();
        
        if (!$usuario) {
            return $next($request);
        }

        // Verificar se precisa completar onboarding
        $rotasPermitidas = [
            'onboarding',
            'onboarding.salvar', 
            'onboarding.pular',
            'logout',
            'api.*'
        ];

        if (!$usuario->onboardingConcluido() && 
            !$request->routeIs($rotasPermitidas) &&
            !$request->is('api/*')) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}