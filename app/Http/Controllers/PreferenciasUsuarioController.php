<?php

namespace App\Http\Controllers;

use App\Models\Interesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenciasUsuarioController extends Controller
{
    public function onboarding()
    {
        // ✅ CORRIGIDO - usando route('post.index') que existe
        if (Auth::user()->onboardingConcluido()) {
            return redirect()->route('post.index');
        }

        $interesses = Interesse::ativos()->destaques()->get();
    
        return view('auth.onboarding', compact('interesses'));
    }

    public function editar()
    {
        $interesses = Interesse::ativos()->get();
        $interessesUsuario = Auth::user()->interesses()->pluck('interesses.id')->toArray();

        return view('usuario.preferencias.editar', compact('interesses', 'interessesUsuario'));
    }

    public function salvarOnboarding(Request $request)
    {
        $request->validate([
            'interesses' => 'nullable|array',
            'interesses.*' => 'exists:interesses,id'
        ]);

        $usuario = Auth::user();
        
        if ($request->has('interesses')) {
            foreach ($request->interesses as $interesseId) {
                $usuario->seguirInteresse($interesseId, true);
            }
        }

        $usuario->completarOnboarding();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Interesses salvos com sucesso!',
            // ✅ CORRIGIDO: usar post.index em vez de feed.principal
            'redirecionar' => route('post.index')
        ]);
    }

    public function atualizar(Request $request)
    {
        $request->validate([
            'interesses' => 'nullable|array',
            'interesses.*' => 'exists:interesses,id'
        ]);

        $usuario = Auth::user();
        
        $interessesAtuais = $usuario->interesses()->pluck('interesses.id')->toArray();
        $novosInteresses = $request->interesses ?? [];
        
        $adicionar = array_diff($novosInteresses, $interessesAtuais);
        foreach ($adicionar as $interesseId) {
            $usuario->seguirInteresse($interesseId, true);
        }
        
        $remover = array_diff($interessesAtuais, $novosInteresses);
        foreach ($remover as $interesseId) {
            $usuario->deixarSeguirInteresse($interesseId);
        }

        return redirect()
            ->route('usuario.preferencias.editar')
            ->with('sucesso', 'Interesses atualizados com sucesso!');
    }

    public function pularOnboarding()
    {
        Auth::user()->completarOnboarding();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Onboarding pulado com sucesso!',
            // ✅ CORRIGIDO: usar post.index em vez de feed.principal
            'redirecionar' => route('post.index')
        ]);
    }
}