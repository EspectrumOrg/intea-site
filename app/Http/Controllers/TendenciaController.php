<?php

namespace App\Http\Controllers;

use App\Models\Tendencia;
use App\Models\Postagem;
use Illuminate\Http\Request;

class TendenciaController extends Controller
{
    /* Exibe posts de uma tendência específica */
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

    /* "API" - Obviamente está pegando do banco - para buscar tendências (usado no frontend)*/
    public function apiTendencias()
    {
        $tendencias = Tendencia::populares(10)->get();
        
        return response()->json([
            'tendencias' => $tendencias
        ]);
    }

    /* Lista todas as tendências */
    public function index()
    {
        $tendencias = Tendencia::orderBy('contador_uso', 'desc')
                            ->orderBy('ultimo_uso', 'desc')
                            ->paginate(20);

        return view('feed.tendencias.index', compact('tendencias'));
    }
}