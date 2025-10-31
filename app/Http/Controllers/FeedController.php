<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\Interesse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        if ($usuario->interesses()->count() > 0) {
            $postagens = $this->obterFeedPersonalizado($usuario);
            $tipoFeed = 'personalizado';
        } else {
            $postagens = $this->obterFeedGeral();
            $tipoFeed = 'geral';
        }

        $interessesSugeridos = $usuario->obterInteressesSugeridos(6);

        return view('feed.index', compact('postagens', 'tipoFeed', 'interessesSugeridos'));
    }

    private function obterFeedPersonalizado($usuario)
    {
        return $usuario->obterFeedInteresses(20);
    }

    private function obterFeedGeral()
    {
        return Postagem::with(['usuario', 'imagens', 'interesses'])
                    ->withCount(['curtidas', 'comentarios'])
                    ->where('bloqueada_auto', false)
                    ->where('removida_manual', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
    }

    public function porInteresse($slugInteresse)
    {
        $interesse = Interesse::where('slug', $slugInteresse)->firstOrFail();
        
        $postagens = $interesse->postagensVisiveis()
                    ->with(['usuario', 'imagens'])
                    ->withCount(['curtidas', 'comentarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        $usuarioSegue = Auth::user()->segueInteresse($interesse->id);

        return view('feed.interesse', compact('postagens', 'interesse', 'usuarioSegue'));
    }

    public function interessesMistos(Request $request)
    {
        $usuario = Auth::user();
        $interessesIds = $request->get('interesses', $usuario->interesses()->pluck('interesses.id')->toArray());
        
        $postagens = Postagem::with(['usuario', 'imagens', 'interesses'])
                    ->whereHas('interesses', function($query) use ($interessesIds) {
                        $query->whereIn('interesses.id', $interessesIds);
                    })
                    ->where(function($query) {
                        $query->where('visibilidade_interesse', 'publico')
                              ->orWhere(function($q) {
                                  $q->where('visibilidade_interesse', 'seguidores');
                              });
                    })
                    ->where('bloqueada_auto', false)
                    ->where('removida_manual', false)
                    ->withCount(['curtidas', 'comentarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return response()->json([
            'sucesso' => true,
            'postagens' => $postagens,
            'interesses_selecionados' => $interessesIds
        ]);
    }
}