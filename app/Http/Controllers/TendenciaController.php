<?php

namespace App\Http\Controllers;

use App\Models\Tendencia;
use App\Models\Postagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TendenciaController extends Controller
{
    /**
     * MÉTODO PRIVADO: Centraliza a lógica de pesquisa
     */
    private function aplicarPesquisa($query, $termoBusca)
    {
        if (!empty($termoBusca)) {
            // Remove # e espaços, converte para minúsculas
            $termo = strtolower(trim(str_replace('#', '', $termoBusca)));
            
            $query->where(function($q) use ($termo) {
                $q->where(DB::raw('LOWER(hashtag)'), 'LIKE', "%{$termo}%")
                  ->orWhere(DB::raw('LOWER(slug)'), 'LIKE', "%{$termo}%");
            });
        }
        return $query;
    }

    /**
     * Lista todas as tendências com pesquisa
     */
    public function index(Request $request)
    {
        $query = Tendencia::query();
        
        // Aplica pesquisa
        $this->aplicarPesquisa($query, $request->search);

        $tendencias = $query->orderBy('contador_uso', 'desc')
                          ->orderBy('ultimo_uso', 'desc')
                          ->paginate(20)
                          ->appends($request->query());

        return view('feed.tendencias.index', compact('tendencias'));
    }

    /**
     * API para buscar tendências
     */
    public function apiTendencias(Request $request)
    {
        $query = Tendencia::query();
        
        $this->aplicarPesquisa($query, $request->search);

        $tendencias = $query->populares(10)->get();
        
        return response()->json(['tendencias' => $tendencias]);
    }

    /**
     * Exibe posts de uma tendência específica
     */
    public function show($slug)
    {
        $tendencia = Tendencia::where('slug', $slug)->firstOrFail();
        
        $postagens = Postagem::with(['usuario', 'imagens', 'tendencias'])
            ->whereHas('tendencias', function($query) use ($tendencia) {
                $query->where('tendencia_id', $tendencia->id);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $tendenciasPopulares = Tendencia::populares(10)->get();

        return view('feed.tendencias.show', compact('tendencia', 'postagens', 'tendenciasPopulares'));
    }

    /**
     * Busca em tempo real (AJAX)
     */
    public function search(Request $request)
    {
        $query = Tendencia::query();
        
        $this->aplicarPesquisa($query, $request->q);

        $tendencias = $query->orderBy('contador_uso', 'desc')
                          ->take(10)
                          ->get();

        return response()->json(['tendencias' => $tendencias]);
    }
}