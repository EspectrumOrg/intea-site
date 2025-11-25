<?php

namespace App\Http\Controllers;

use App\Models\{
    Interesse, 
    Postagem,
    PalavraProibida,
    PalavraProibidaGlobal,
    InfracaoSistema,
    PenalidadeUsuario,
    InteresseExpulsao,
    Usuario
};
use App\Services\ServicoModeracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeracaoController extends Controller
{
    /**
     * PAINEL DE MODERAÇÃO DE INTERESSE
     */
    public function painel($slugInteresse)
    {
        $interesse = Interesse::where('slug', $slugInteresse)->firstOrFail();
        $usuario = Auth::user();

        // ADMIN tem acesso a TODOS os interesses
        // Moderador tem acesso apenas aos interesses que modera
        if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($interesse->id)) {
            abort(403, 'Acesso negado à moderação deste interesse');
        }

        $postagensParaRevisao = $interesse->postagensParaRevisao()
                                        ->with(['usuario', 'interesses'])
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        $estatisticas = ServicoModeracao::obterEstatisticasInteresse($interesse->id);
        $palavrasProibidas = $interesse->palavrasProibidas()->where('ativo', true)->get();
        
        $estatisticasUsuario = $usuario->obterEstatisticasModeracaoCompleta();

        return view('moderacao.painel', compact(
            'interesse', 
            'postagensParaRevisao',
            'estatisticas',
            'palavrasProibidas',
            'estatisticasUsuario'
        ));
    }

    /**
     * PAINEL DE MODERAÇÃO GLOBAL (SISTEMA)
     */
    public function painelGlobal()
    {
        $usuario = Auth::user();
        
        // Apenas administradores podem acessar
        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado ao painel global de moderação. Apenas administradores.');
        }

        $infracoesPendentes = InfracaoSistema::where('verificada', false)
                                            ->with(['usuario', 'postagem', 'interesse', 'reportadoPor'])
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(20);

        $penalidadesRecentes = PenalidadeUsuario::with(['usuario', 'interesse', 'aplicadoPor'])
                                                ->orderBy('created_at', 'desc')
                                                ->limit(10)
                                                ->get();

        $estatisticas = ServicoModeracao::obterEstatisticasModeracao();
        $palavrasProibidasGlobais = PalavraProibidaGlobal::where('ativo', true)->get();

        return view('moderacao.painel-global', compact(
            'infracoesPendentes',
            'penalidadesRecentes',
            'estatisticas',
            'palavrasProibidasGlobais'
        ));
    }

    /**
     * AÇÕES DE MODERAÇÃO DE POSTAGENS
     */
    public function removerPostagem(Request $request, $postagemId)
    {
        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
            'aplicar_penalidade' => 'boolean'
        ]);

        $postagem = Postagem::findOrFail($postagemId);
        $usuario = Auth::user();

        $sucesso = ServicoModeracao::removerPostagem(
            $postagem, 
            $usuario->id, 
            $request->motivo
        );

        if ($sucesso) {
            // Aplicar penalidade se solicitado
            if ($request->boolean('aplicar_penalidade', false)) {
                foreach ($postagem->interesses as $interesse) {
                    if ($usuario->isAdministrador() || $usuario->podeModerarInteresse($interesse->id)) {
                        ServicoModeracao::aplicarPenalidade(
                            $postagem->usuario_id,
                            'interesse',
                            $interesse->id,
                            $request->motivo,
                            1, // Peso
                            $usuario->id,
                            30 // Dias de expiração
                        );
                        break;
                    }
                }
            }

            return redirect()->back()->with('success', 'Postagem removida com sucesso' . ($request->aplicar_penalidade ? ' e penalidade aplicada' : ''));
        }

        return redirect()->back()->with('error', 'Erro ao remover postagem ou permissão negada');
    }

    public function restaurarPostagem($postagemId)
    {
        $postagem = Postagem::findOrFail($postagemId);
        $usuario = Auth::user();

        $permissoes = [];
        foreach ($postagem->interesses as $interesse) {
            $permissoes[] = $usuario->isAdministrador() || $usuario->podeModerarInteresse($interesse->id);
        }

        if (in_array(false, $permissoes)) {
            return redirect()->back()->with('error', 'Permissão negada para restaurar esta postagem');
        }

        $postagem->restaurar();

        return redirect()->back()->with('success', 'Postagem restaurada com sucesso');
    }

    /**
     * GERENCIAMENTO DE PALAVRAS PROIBIDAS
     */
    public function adicionarPalavraProibida(Request $request, $interesseId)
    {
        $request->validate([
            'palavra' => 'required|string|max:100',
            'tipo' => 'required|in:exata,parcial',
            'motivo' => 'nullable|string|max:500'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($interesse->id)) {
            abort(403, 'Acesso negado');
        }

        $interesse->adicionarPalavraProibida(
            $request->palavra,
            $request->tipo,
            $usuario->id,
            $request->motivo
        );

        return redirect()->back()->with('success', 'Palavra proibida adicionada com sucesso');
    }

    public function adicionarPalavraProibidaGlobal(Request $request)
    {
        $request->validate([
            'palavra' => 'required|string|max:100',
            'tipo' => 'required|in:exata,parcial',
            'motivo' => 'required|string|max:500'
        ]);

        $usuario = Auth::user();
        
        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        PalavraProibidaGlobal::create([
            'palavra' => $request->palavra,
            'tipo' => $request->tipo,
            'motivo' => $request->motivo,
            'adicionado_por' => $usuario->id,
            'ativo' => true
        ]);

        return redirect()->route('moderacao.global')->with('success', 'Palavra proibida global adicionada com sucesso');
    }

    public function removerPalavraProibida($palavraId)
    {
        $palavra = PalavraProibida::findOrFail($palavraId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($palavra->interesse_id)) {
            abort(403, 'Acesso negado');
        }

        $palavra->update(['ativo' => false]);

        return redirect()->back()->with('success', 'Palavra proibida removida com sucesso');
    }

    public function removerPalavraProibidaGlobal($palavraId)
    {
        $palavra = PalavraProibidaGlobal::findOrFail($palavraId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        $palavra->update(['ativo' => false]);

        return redirect()->route('moderacao.global')->with('success', 'Palavra proibida global removida com sucesso');
    }

    /**
     * GERENCIAMENTO DE USUÁRIOS
     */
    public function expulsarUsuario(Request $request, $interesseId)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'motivo' => 'required|string|min:10|max:500',
            'permanente' => 'boolean',
            'dias_expulsao' => 'nullable|integer|min:1|max:365',
            'aplicar_penalidade' => 'boolean'
        ]);

        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($interesse->id)) {
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

        // Aplicar penalidade se solicitado
        if ($request->boolean('aplicar_penalidade', true)) {
            ServicoModeracao::aplicarPenalidade(
                $request->usuario_id,
                'interesse',
                $interesseId,
                $request->motivo . ' (Expulso do interesse)',
                2, // Peso médio para expulsão
                $usuario->id,
                $request->dias_expulsao ?? 30
            );
        }

        return redirect()->back()->with('success', 'Usuário expulso do interesse com sucesso');
    }

    public function banirUsuarioSistema(Request $request, $usuarioId)
    {
        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
            'permanente' => 'boolean',
            'dias_banimento' => 'nullable|integer|min:1|max:365'
        ]);

        $usuarioAlvo = Usuario::findOrFail($usuarioId);
        $moderador = Auth::user();

        if (!$moderador->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        // Aqui você implementaria a lógica de banimento do sistema
        // $usuarioAlvo->banir($request->motivo, $request->permanente, $request->dias_banimento);

        // Aplicar penalidade de sistema
        ServicoModeracao::aplicarPenalidade(
            $usuarioId,
            'sistema',
            null,
            $request->motivo . ' (Banido do sistema)',
            3, // Peso alto para banimento
            $moderador->id,
            $request->permanente ? null : ($request->dias_banimento ?? 30)
        );

        return redirect()->back()->with('success', 'Usuário banido do sistema com sucesso');
    }

    /**
     * GERENCIAMENTO DE INFRAÇÕES
     */
    public function listarInfracoesPendentes()
    {
        $usuario = Auth::user();
        
        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        $infracoes = InfracaoSistema::where('verificada', false)
                                    ->with(['usuario', 'postagem', 'interesse', 'reportadoPor'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return response()->json([
            'sucesso' => true,
            'infracoes' => $infracoes
        ]);
    }

    public function verificarInfracao(Request $request, $infracaoId)
    {
        $request->validate([
            'acao' => 'required|in:ignorar,aplicar_penalidade',
            'motivo_penalidade' => 'required_if:acao,aplicar_penalidade|string|min:10|max:500',
            'peso_penalidade' => 'required_if:acao,aplicar_penalidade|integer|min:1|max:3',
            'dias_expiracao' => 'nullable|integer|min:1|max:365'
        ]);

        $infracao = InfracaoSistema::findOrFail($infracaoId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        $infracao->marcarComoVerificada($usuario->id);

        if ($request->acao === 'aplicar_penalidade') {
            $tipoPenalidade = $infracao->interesse_id ? 'interesse' : 'sistema';

            ServicoModeracao::aplicarPenalidade(
                $infracao->usuario_id,
                $tipoPenalidade,
                $infracao->interesse_id,
                $request->motivo_penalidade ?: $infracao->descricao,
                $request->peso_penalidade,
                $usuario->id,
                $request->dias_expiracao
            );

            $mensagem = 'Infração verificada e penalidade aplicada';
        } else {
            $mensagem = 'Infração verificada e ignorada';
        }

        return redirect()->route('moderacao.global')->with('success', $mensagem);
    }

    /**
     * ESTATÍSTICAS E RELATÓRIOS
     */
    public function obterEstatisticasInteresse($interesseId)
    {
        $interesse = Interesse::findOrFail($interesseId);
        $usuario = Auth::user();

        if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($interesse->id)) {
            abort(403, 'Acesso negado');
        }

        $estatisticas = ServicoModeracao::obterEstatisticasInteresse($interesseId);
        $penalidadesRecentes = PenalidadeUsuario::where('interesse_id', $interesseId)
                                                ->with(['usuario', 'aplicadoPor'])
                                                ->orderBy('created_at', 'desc')
                                                ->limit(10)
                                                ->get();

        return response()->json([
            'sucesso' => true,
            'estatisticas' => $estatisticas,
            'penalidades_recentes' => $penalidadesRecentes
        ]);
    }

    public function obterEstatisticasGlobais()
    {
        $usuario = Auth::user();
        
        if (!$usuario->isAdministrador()) {
            abort(403, 'Acesso negado');
        }

        $estatisticas = ServicoModeracao::obterEstatisticasModeracao();
        $processamentoAutomatico = ServicoModeracao::processarBanimentosAutomaticos();

        return response()->json([
            'sucesso' => true,
            'estatisticas' => $estatisticas,
            'processamento_automatico' => $processamentoAutomatico
        ]);
    }

    public function gerarRelatorioModeracao(Request $request)
    {
        $request->validate([
            'periodo_inicio' => 'required|date',
            'periodo_fim' => 'required|date|after:periodo_inicio',
            'interesse_id' => 'nullable|exists:interesses,id'
        ]);

        $usuario = Auth::user();
        $relatorio = [];

        if ($request->interesse_id) {
            if (!$usuario->isAdministrador() && !$usuario->podeModerarInteresse($request->interesse_id)) {
                abort(403, 'Acesso negado');
            }
            $relatorio = $usuario->gerarRelatorioModeracao(
                $request->periodo_inicio,
                $request->periodo_fim
            );
        } else {
            if (!$usuario->isAdministrador()) {
                abort(403, 'Acesso negado');
            }
            // Relatório global
            $relatorio = [
                'periodo' => [
                    'inicio' => $request->periodo_inicio,
                    'fim' => $request->periodo_fim
                ],
                'total_infracoes' => InfracaoSistema::whereBetween('created_at', [$request->periodo_inicio, $request->periodo_fim])->count(),
                'total_penalidades' => PenalidadeUsuario::whereBetween('created_at', [$request->periodo_inicio, $request->periodo_fim])->count(),
                'total_expulsoes' => InteresseExpulsao::whereBetween('created_at', [$request->periodo_inicio, $request->periodo_fim])->count(),
                'postagens_removidas' => Postagem::where('removida_manual', true)
                                                ->whereBetween('removida_em', [$request->periodo_inicio, $request->periodo_fim])
                                                ->count(),
            ];
        }

        return response()->json([
            'sucesso' => true,
            'relatorio' => $relatorio
        ]);
    }

    /**
     * AÇÕES EM MASSA
     */
    public function acaoEmMassaPostagens(Request $request)
    {
        $request->validate([
            'postagens_ids' => 'required|array',
            'postagens_ids.*' => 'exists:postagens,id',
            'acao' => 'required|in:remover,restaurar',
            'motivo' => 'required_if:acao,remover|string|min:10|max:500'
        ]);

        $usuario = Auth::user();
        $resultados = [
            'sucessos' => 0,
            'erros' => 0,
            'detalhes' => []
        ];

        foreach ($request->postagens_ids as $postagemId) {
            try {
                $postagem = Postagem::find($postagemId);
                
                // Verificar permissão para cada postagem
                $temPermissao = false;
                foreach ($postagem->interesses as $interesse) {
                    if ($usuario->isAdministrador() || $usuario->podeModerarInteresse($interesse->id)) {
                        $temPermissao = true;
                        break;
                    }
                }

                if (!$temPermissao) {
                    $resultados['erros']++;
                    $resultados['detalhes'][] = "Sem permissão para postagem #{$postagemId}";
                    continue;
                }

                if ($request->acao === 'remover') {
                    $sucesso = ServicoModeracao::removerPostagem($postagem, $usuario->id, $request->motivo);
                    if ($sucesso) {
                        $resultados['sucessos']++;
                    } else {
                        $resultados['erros']++;
                    }
                } else {
                    $postagem->restaurar();
                    $resultados['sucessos']++;
                }

            } catch (\Exception $e) {
                $resultados['erros']++;
                $resultados['detalhes'][] = "Erro na postagem #{$postagemId}: " . $e->getMessage();
            }
        }

        if ($resultados['erros'] === 0) {
            return redirect()->back()->with('success', "{$resultados['sucessos']} postagens processadas com sucesso");
        } else {
            return redirect()->back()->with('error', "{$resultados['sucessos']} sucessos, {$resultados['erros']} erros");
        }
    }

    public function adicionarPalavraGlobal(Request $request)
    {
        try {
            $request->validate([
                'palavra' => 'required|string|max:255',
                'tipo' => 'required|in:exata,parcial',
                'motivo' => 'nullable|string'
            ]);

            PalavraProibidaGlobal::create([
                'palavra' => $request->palavra,
                'tipo' => $request->tipo,
                'motivo' => $request->motivo,
                'adicionado_por' => Auth::id(),
                'ativo' => true
            ]);

            return redirect()->route('moderacao.global')->with('success', 'Palavra adicionada com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('moderacao.global')->with('error', 'Erro ao adicionar palavra: ' . $e->getMessage());
        }
    }

    public function removerPalavraGlobal($id)
    {
        try {
            $palavra = PalavraProibidaGlobal::findOrFail($id);
            $palavra->delete();

            return redirect()->route('moderacao.global')->with('success', 'Palavra removida com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('moderacao.global')->with('error', 'Erro ao remover palavra');
        }
    }

    public function processarBanimentosAutomaticos()
    {
        try {
            $resultados = ServicoModeracao::processarBanimentosAutomaticos();
            return redirect()->route('moderacao.global')->with('success', 'Banimentos automáticos processados: ' . ($resultados['sistema'] ?? 0) . ' sistema, ' . ($resultados['interesse'] ?? 0) . ' interesse');
        } catch (\Exception $e) {
            return redirect()->route('moderacao.global')->with('error', 'Erro ao processar banimentos: ' . $e->getMessage());
        }
    }

    /**
     * DEBUG - Remover após testes
     */
    public function debugPermissoes()
    {
        $usuario = Auth::user();
        
        return response()->json([
            'usuario_id' => $usuario->id,
            'nome' => $usuario->nome,
            'tipo_usuario' => $usuario->tipo_usuario,
            'is_admin' => $usuario->is_admin ?? 'não definido',
            'eh_administrador' => $usuario->isAdministrador(),
            'interesses_como_moderador' => $usuario->interessesComoModerador->pluck('nome')
        ]);
    }
}