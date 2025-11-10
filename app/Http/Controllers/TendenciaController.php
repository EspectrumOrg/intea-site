<?php

namespace App\Http\Controllers;

use App\Models\Tendencia;
use App\Models\Postagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TendenciaController extends Controller
{
    /*MÉTODO PRIVADO: Centraliza a lógica de pesquisa*/
    private function aplicarPesquisa($query, $termoBusca)
    {
        if (!empty($termoBusca)) {
            $termo = strtolower(trim(str_replace('#', '', $termoBusca)));

            $query->where(function ($q) use ($termo) {
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
    try {
        Log::info('Acessando página de tendências', ['search' => $request->search]);

        $query = Tendencia::query();

        // Aplica pesquisa
        $this->aplicarPesquisa($query, $request->search);

        $tendenciasPopulares = Tendencia::populares(7)->get();

        $tendencias = $query->orderBy('contador_uso', 'desc')
            ->orderBy('ultimo_uso', 'desc')
            ->paginate(20)
            ->appends($request->query());

        Log::info('Tendências carregadas', ['count' => $tendencias->count()]);

        return view('feed.tendencias.index', compact('tendencias', 'tendenciasPopulares'));
        
    } catch (\Exception $e) {
        Log::error('Erro no controller de tendências: ' . $e->getMessage());
        
        $tendenciasPopulares = collect();
        return view('feed.tendencias.index', compact('tendenciasPopulares'))->with('tendencias', []);
    }
}
    /**
     * API para buscar tendências
     */
    public function apiTendencias(Request $request)
    {
        try {
            Log::info('API Tendências chamada', ['search' => $request->search]);

            $query = Tendencia::query();

            $this->aplicarPesquisa($query, $request->search);

            $tendencias = $query->populares(7)->get();

            Log::info('Tendências API retornadas', ['count' => $tendencias->count()]);

            return response()->json([
                'success' => true,
                'tendencias' => $tendencias
            ]);
        } catch (\Exception $e) {
            Log::error('Erro na API de tendências: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar tendências',
                'tendencias' => []
            ], 500);
        }
    }

    /**
     * API para tendências populares (usada nas abas)
     */
    public function apiPopulares()
    {
        try {
            Log::info('API Tendências Populares chamada');

            $tendencias = Tendencia::populares(7)->get();

            Log::info('Tendências populares retornadas', ['count' => $tendencias->count()]);

            return response()->json([
                'success' => true,
                'tendencias' => $tendencias
            ]);
        } catch (\Exception $e) {
            Log::error('Erro nas tendências populares: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar tendências',
                'tendencias' => []
            ], 500);
        }
    }

    /**
     * Exibe posts de uma tendência específica
     */
    public function show($slug)
    {
        try {
            Log::info('Acessando tendência específica', ['slug' => $slug]);

            $tendencia = Tendencia::where('slug', $slug)->firstOrFail();

            $postagens = Postagem::with(['usuario', 'imagens', 'tendencias'])
                ->whereHas('tendencias', function ($query) use ($tendencia) {
                    $query->where('tendencia_id', $tendencia->id);
                })
                ->orderByDesc('created_at')
                ->paginate(15);

            $tendenciasPopulares = Tendencia::populares(7)->get();

            Log::info('Tendência carregada', [
                'tendencia' => $tendencia->hashtag,
                'postagens_count' => $postagens->count()
            ]);

            return view('feed.tendencias.show', compact('tendencia', 'postagens', 'tendenciasPopulares'));
        } catch (\Exception $e) {
            Log::error('Erro ao mostrar tendência: ' . $e->getMessage());
            return redirect()->route('tendencias.index')->with('error', 'Tendência não encontrada.');
        }
    }

    /**
     * Busca em tempo real (AJAX)
     */
    public function search(Request $request)
    {
        try {
            Log::info('Busca em tempo real', ['q' => $request->q]);

            $query = Tendencia::query();

            $this->aplicarPesquisa($query, $request->q);

            $tendencias = $query->orderBy('contador_uso', 'desc')
                ->take(10)
                ->get();

            Log::info('Resultados da busca', ['count' => $tendencias->count()]);

            return response()->json([
                'success' => true,
                'tendencias' => $tendencias
            ]);
        } catch (\Exception $e) {
            Log::error('Erro na busca de tendências: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro na busca',
                'tendencias' => []
            ], 500);
        }
    }
}