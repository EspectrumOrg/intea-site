<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->onboardingConcluido()) {
            // Permitir acesso apenas ao onboarding, logout e rotas de API necessárias
            $allowedRoutes = [
                'onboarding',
                'onboarding.salvar', 
                'onboarding.pular',
                'logout',
                'update-theme-preference'
            ];
            
            $currentRoute = $request->route()->getName();
            
            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('onboarding');
            }
        }

        return $next($request);
    }
}