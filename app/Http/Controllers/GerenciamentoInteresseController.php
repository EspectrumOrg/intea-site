<?php

namespace App\Http\Controllers;

use App\Models\{Interesse, Usuario, PenalidadeUsuario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ServicoModeracao;


class GerenciamentoInteresseController extends Controller
{
    public function gerenciarModeradores($slugInteresse)
    {
        $interesse = Interesse::where('slug', $slugInteresse)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->isDonoInteresse($interesse->id)) {
            abort(403, 'Apenas o dono do interesse pode gerenciar moderadores');
        }

        $moderadores = $interesse->moderadores;
        $seguidores = $interesse->seguidores()->whereNotIn('usuario_id', $moderadores->pluck('id'))->get();

        return view('interesses.gerenciamento.moderadores', compact(
            'interesse',
            'moderadores',
            'seguidores'
        ));
    }

    public function adicionarModerador(Request $request, $interesseId)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->isDonoInteresse($interesseId)) {
            abort(403, 'Apenas o dono pode adicionar moderadores');
        }

        $sucesso = $usuario->adicionarModerador($interesseId, $request->usuario_id);

        if ($sucesso) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Moderador adicionado com sucesso'
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Erro ao adicionar moderador'
        ], 400);
    }

    public function removerModerador(Request $request, $interesseId)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->isDonoInteresse($interesseId)) {
            abort(403, 'Apenas o dono pode remover moderadores');
        }

        $sucesso = $usuario->removerModerador($interesseId, $request->usuario_id);

        if ($sucesso) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Moderador removido com sucesso'
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Erro ao remover moderador'
        ], 400);
    }

    public function estatisticasInteresse($slugInteresse)
    {
        $interesse = Interesse::where('slug', $slugInteresse)->firstOrFail();
        $usuario = Auth::user();

        if (!$usuario->podeModerar($interesse->id)) {
            abort(403, 'Acesso negado à moderação deste interesse');
        }

        $estatisticas = ServicoModeracao::obterEstatisticasInteresse($interesse->id);
        $penalidadesRecentes = PenalidadeUsuario::where('interesse_id', $interesse->id)
                                                ->with(['usuario', 'aplicadoPor'])
                                                ->orderBy('created_at', 'desc')
                                                ->limit(10)
                                                ->get();

        return view('interesses.gerenciamento.estatisticas', compact(
            'interesse',
            'estatisticas',
            'penalidadesRecentes'
        ));
    }
}