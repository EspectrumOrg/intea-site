<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DenunciaController extends Controller
{
    /**
     * Exibir lista de denúncias
     */
    public function index(Request $request)
    {
        $query = Denuncia::query()->where('status_denuncia', 'pendente');

        // Filtros
        if ($request->filled('motivo_denuncia')) {
            $query->where('motivo_denuncia', $request->motivo_denuncia);
        }

        // Ordenação
        $ordem = $request->input('ordem', 'desc');
        $query->orderBy('created_at', $ordem);

        // Carrega relações
        $denuncias = $query->with([
            'usuarioDenunciante',     // denunciante
            'usuarioDenunciado',     // denunciado
            'postagem.usuario',      // autor da postagem (se ttiver)
            'comentario.usuario',    // autor do comentário (se ttiver)
        ])->paginate(10);

        return view('admin.denuncia.index', compact('denuncias'));
    }

    /**
     * Cria uma denúncia (pode ser de usuário, postagem ou comentário)
     */
    public function store(Request $request)
    {
        $request->validate([
            'motivo_denuncia' => 'required|string|max:255',
            'texto_denuncia' => 'nullable|string|max:555',
            'tipo' => 'required|in:usuario,postagem,comentario',
            'id_alvo' => 'required|integer',
        ], [
            'tipo.in' => 'Tipo de denúncia inválido.',
            'id_alvo.required' => 'ID do alvo da denúncia é obrigatório.',
        ]);

        $dados = [
            'id_usuario_denunciante' => auth()->id(),
            'motivo_denuncia' => $request->motivo_denuncia,
            'texto_denuncia' => $request->texto_denuncia,
            'status_denuncia' => 'pendente',
        ];

        // Define o alvo conforme o tipo
        switch ($request->tipo) {
            case 'usuario':
                $dados['id_usuario_denunciado'] = $request->id_alvo;
                break;

            case 'postagem':
                $dados['id_postagem'] = $request->id_alvo;
                break;

            case 'comentario':
                $dados['id_comentario'] = $request->id_alvo;
                break;
        }

        Denuncia::create($dados);

        return back()->with('warning', 'Denúncia enviada com sucesso!');
    }

    /**
     * Marca uma denúncia (e relacionadas) como resolvidas
     */
    public function resolve($id)
    {
        $denuncia = Denuncia::findOrFail($id);
        $denuncia->status_denuncia = 'resolvida';
        $denuncia->save();

        // Resolve todas as relacionadas (mesmo alvo)
        Denuncia::where(function ($q) use ($denuncia) {
            $q->where('id_usuario_denunciado', $denuncia->id_usuario_denunciado)
              ->orWhere('id_postagem', $denuncia->id_postagem)
              ->orWhere('id_comentario', $denuncia->id_comentario);
        })->where('id', '!=', $denuncia->id)
          ->update(['status_denuncia' => 'resolvida']);

        return back()->with('success', 'Denúncia resolvida com sucesso!');
    }
}
