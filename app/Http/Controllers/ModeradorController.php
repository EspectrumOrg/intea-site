<?php

namespace App\Http\Controllers;

use App\Models\{
    Interesse, 
    Postagem,
    PalavraProibida
};
use App\Services\ServicoModeracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeracaoController extends Controller
{
    public function painel($slugInteresse)
    {
        
        $interesse = Interesse::where('slug', $slugInteresse)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeModerar($interesse->id)) {
            abort(403, 'Acesso negado à moderação deste interesse');
        }

        $postagensParaRevisao = $interesse->postagensParaRevisao()
                                        ->with(['usuario', 'interesses'])
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        $estatisticas = ServicoModeracao::obterEstatisticasInteresse($interesse->id);
        $palavrasProibidas = $interesse->palavrasProibidas()->where('ativo', true)->get();

        return view('moderacao.painel', compact(
            'interesse', 
            'postagensParaRevisao',
            'estatisticas',
            'palavrasProibidas'
        ));
    }

    public function removerPostagem(Request $request, $postagemId)
    {
        $request->validate([
            'motivo' => 'required|string|min:10|max:500'
        ]);

        $postagem = Postagem::findOrFail($postagemId);
        $usuario = Auth::user();

        $sucesso = ServicoModeracao::removerPostagem(
            $postagem, 
            $usuario->id, 
            $request->motivo
        );

        if ($sucesso) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Postagem removida com sucesso'
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Erro ao remover postagem ou permissão negada'
        ], 400);
    }

    public function restaurarPostagem($postagemId)
    {
        $postagem = Postagem::findOrFail($postagemId);
        $usuario = Auth::user();

        $permissoes = [];
        foreach ($postagem->interesses as $interesse) {
            $permissoes[] = $usuario->podeModerar($interesse->id);
        }

        if (in_array(false, $permissoes)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Permissão negada para restaurar esta postagem'
            ], 403);
        }

        $postagem->restaurar();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Postagem restaurada com sucesso'
        ]);
    }

    public function adicionarPalavraProibida(Request $request, $interesseId)
    {
        $request->validate([
            'palavra' => 'required|string|max:100',
            'tipo' => 'required|in:exata,parcial',
            'motivo' => 'nullable|string|max:500'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->podeModerar($interesse->id)) {
            abort(403, 'Acesso negado');
        }

        $interesse->adicionarPalavraProibida(
            $request->palavra,
            $request->tipo,
            $usuario->id,
            $request->motivo
        );

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Palavra proibida adicionada com sucesso'
        ]);
    }

    public function expulsarUsuario(Request $request, $interesseId)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'motivo' => 'required|string|min:10|max:500',
            'permanente' => 'boolean',
            'dias_expulsao' => 'nullable|integer|min:1|max:365'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->podeModerar($interesse->id)) {
            abort(403, 'Acesso negado');
        }

        $expulsoAte = $request->permanente ? null : now()->addDays($request->dias_expulsao ?? 30);

        ServicoModeracao::expulsarUsuario(
            $request->usuario_id,
            $interesseId,
            $request->motivo,
            $usuario->id,
            $request->boolean('permanente', false),
            $expulsoAte
        );

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Usuário expulso do interesse com sucesso'
        ]);
    }
}