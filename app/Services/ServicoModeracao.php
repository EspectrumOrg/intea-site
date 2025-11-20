<?php

namespace App\Services;

use App\Models\{
    Usuario, Interesse, Postagem, PalavraProibidaGlobal,
    InfracaoSistema, PenalidadeUsuario, AlertaModeracao,
    InteresseExpulsao, PalavraProibida
};
use Illuminate\Support\Facades\Log;

class ServicoModeracao
{
    public static function notificarModeradoresNovaPostagem(Postagem $postagem): void
    {
        foreach ($postagem->interesses as $interesse) {
            if (!$interesse->moderacao_ativa) {
                continue;
            }

            $moderadores = $interesse->moderadores;
            
            foreach ($moderadores as $moderador) {
                Log::info("Notificando moderador {$moderador->nome} sobre nova postagem no interesse {$interesse->nome}");
            }
        }
    }

    public static function removerPostagem(Postagem $postagem, $moderadorId, $motivo): bool
    {
        $moderador = Usuario::find($moderadorId);
        $permissoes = [];

        foreach ($postagem->interesses as $interesse) {
            if (!$moderador->isAdministrador() && !$moderador->podeModerarInteresse($interesse->id)) {
                $permissoes[] = false;
            } else {
                $permissoes[] = true;
            }
        }

        if (in_array(false, $permissoes)) {
            return false;
        }

        $postagem->removerManual($moderadorId, $motivo);
        return true;
    }

    public static function criarAlertaAutomático($usuarioId, $interesseId, $postagemId, $motivo): AlertaModeracao
    {
        $interesse = Interesse::find($interesseId);
        
        $alerta = AlertaModeracao::create([
            'usuario_id' => $usuarioId,
            'interesse_id' => $interesseId,
            'postagem_id' => $postagemId,
            'motivo' => $motivo,
            'gravidade' => 'moderado',
            'moderador_id' => 1,
            'expiracao' => now()->addDays($interesse->dias_expiracao_alerta),
            'ativo' => true
        ]);

        self::verificarLimiteAlertas($usuarioId, $interesseId);
        return $alerta;
    }

    public static function criarAlertaManual($usuarioId, $interesseId, $postagemId, $motivo, $moderadorId, $gravidade = 'leve'): AlertaModeracao
    {
        $interesse = Interesse::find($interesseId);
        
        $alerta = AlertaModeracao::create([
            'usuario_id' => $usuarioId,
            'interesse_id' => $interesseId,
            'postagem_id' => $postagemId,
            'motivo' => $motivo,
            'gravidade' => $gravidade,
            'moderador_id' => $moderadorId,
            'expiracao' => now()->addDays($interesse->dias_expiracao_alerta),
            'ativo' => true
        ]);

        self::verificarLimiteAlertas($usuarioId, $interesseId);
        return $alerta;
    }

    public static function verificarLimiteAlertas($usuarioId, $interesseId): bool
    {
        $interesse = Interesse::find($interesseId);
        $alertasAtivos = $interesse->obterContadorAlertasUsuario($usuarioId);

        if ($alertasAtivos >= $interesse->limite_alertas_ban) {
            self::expulsarUsuario($usuarioId, $interesseId, 
                "Atingiu o limite de {$interesse->limite_alertas_ban} alertas ativos",
                1,
                false,
                now()->addDays(30)
            );
            return true;
        }

        return false;
    }

    public static function expulsarUsuario($usuarioId, $interesseId, $motivo, $moderadorId, $permanente = false, $expulsoAte = null): InteresseExpulsao
    {
        $usuario = Usuario::find($usuarioId);
        $usuario->deixarSeguirInteresse($interesseId);

        Postagem::where('usuario_id', $usuarioId)
                ->whereHas('interesses', function($query) use ($interesseId) {
                    $query->where('interesses.id', $interesseId);
                })
                ->get()
                ->each(function($postagem) {
                    $postagem->bloquearAutomaticamente('Usuário expulso do interesse');
                });

        return InteresseExpulsao::create([
            'usuario_id' => $usuarioId,
            'interesse_id' => $interesseId,
            'motivo' => $motivo,
            'moderador_id' => $moderadorId,
            'permanente' => $permanente,
            'expulso_ate' => $expulsoAte
        ]);
    }

    public static function verificarPostagem(Postagem $postagem): array
    {
        $violacoes = [];
        
        foreach ($postagem->interesses as $interesse) {
            $violacoesInteresse = PalavraProibida::verificarTexto(
                $postagem->texto_postagem, 
                $interesse->id
            );
            
            if (!empty($violacoesInteresse)) {
                $violacoes[$interesse->nome] = $violacoesInteresse;
            }
        }
        
        return $violacoes;
    }

    public static function obterEstatisticasInteresse($interesseId): array
    {
        $interesse = Interesse::findOrFail($interesseId);

        return [
            'postagens_visiveis' => $interesse->postagensVisiveis()->count(),
            'postagens_bloqueadas_auto' => $interesse->postagens()->where('bloqueada_auto', true)->count(),
            'postagens_removidas_manual' => $interesse->postagens()->where('removida_manual', true)->count(),
            'alertas_ativos' => $interesse->alertasModeracao()->where('ativo', true)->count(),
            'usuarios_expulsos' => $interesse->expulsoes()->count(),
        ];
    }

    public static function expirarAlertasAntigos(): int
    {
        return AlertaModeracao::where('ativo', true)
                            ->where('expiracao', '<', now())
                            ->update(['ativo' => false]);
    }

    public static function removerExpulsoesExpiradas(): int
    {
        return InteresseExpulsao::where('permanente', false)
                                ->where('expulso_ate', '<', now())
                                ->delete();
    }

    /**
     * Verificar conteúdo global (feed principal)
     */
    public static function verificarConteudoGlobal($texto, $usuarioId): array
    {
        $violacoes = PalavraProibidaGlobal::verificarTexto($texto);
        
        if (!empty($violacoes)) {
            self::registrarInfracaoSistema(
                $usuarioId,
                'discurso_odio',
                'Conteúdo contém palavras proibidas',
                $texto,
                null,
                null
            );
        }

        return $violacoes;
    }

    /**
     * Registrar infração no sistema
     */
    public static function registrarInfracaoSistema($usuarioId, $tipo, $descricao, $conteudoOriginal = null, $postagemId = null, $interesseId = null, $reportadoPor = null): InfracaoSistema
    {
        return InfracaoSistema::create([
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'conteudo_original' => $conteudoOriginal,
            'postagem_id' => $postagemId,
            'interesse_id' => $interesseId,
            'reportado_por' => $reportadoPor,
            'verificada' => false
        ]);
    }

    /**
     * Aplicar penalidade a usuário
     */
    public static function aplicarPenalidade($usuarioId, $tipo, $interesseId, $motivo, $peso, $aplicadoPor, $diasExpiracao = null): PenalidadeUsuario
    {
        $expiraEm = $diasExpiracao ? now()->addDays($diasExpiracao) : null;

        $penalidade = PenalidadeUsuario::create([
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'interesse_id' => $interesseId,
            'motivo' => $motivo,
            'peso' => $peso,
            'aplicado_por' => $aplicadoPor,
            'expira_em' => $expiraEm,
            'ativa' => true
        ]);

        // Verificar se deve banir do sistema
        if ($tipo === 'sistema') {
            self::verificarBanimentoSistema($usuarioId);
        }
        // Verificar se deve banir do interesse
        elseif ($tipo === 'interesse' && $interesseId) {
            self::verificarBanimentoInteresse($usuarioId, $interesseId);
        }

        return $penalidade;
    }

    /**
     * Verificar e aplicar banimento do sistema
     */
    public static function verificarBanimentoSistema($usuarioId): bool
    {
        $usuario = Usuario::find($usuarioId);
        
        if ($usuario->deveSerBanidoSistema()) {
            Log::info("Usuário {$usuarioId} deve ser banido do sistema - 3 penalidades");
            return true;
        }

        return false;
    }

    /**
     * Verificar e aplicar banimento do interesse
     */
    public static function verificarBanimentoInteresse($usuarioId, $interesseId): bool
    {
        $usuario = Usuario::find($usuarioId);
        
        if ($usuario->deveSerBanidoInteresse($interesseId)) {
            self::expulsarUsuario(
                $usuarioId,
                $interesseId,
                "Banido por acumular 3 penalidades no interesse",
                1, // Sistema
                true // Permanente
            );
            return true;
        }

        return false;
    }

    /**
     * Processar denúncia de conteúdo
     */
    public static function processarDenuncia($conteudo, $tipo, $usuarioReportador, $postagemId = null, $interesseId = null, $usuarioReportado = null)
    {
        // Verificar palavras proibidas globais
        $violacoesGlobais = self::verificarConteudoGlobal($conteudo, $usuarioReportado);

        // Verificar palavras proibidas do interesse
        $violacoesInteresse = [];
        if ($interesseId) {
            $violacoesInteresse = PalavraProibida::verificarTexto($conteudo, $interesseId);
        }

        // Registrar infração se houver violações
        if (!empty($violacoesGlobais) || !empty($violacoesInteresse)) {
            self::registrarInfracaoSistema(
                $usuarioReportado,
                'conteudo_improprio',
                'Conteúdo reportado contém violações',
                $conteudo,
                $postagemId,
                $interesseId,
                $usuarioReportador
            );
        }

        return [
            'violacoes_globais' => $violacoesGlobais,
            'violacoes_interesse' => $violacoesInteresse
        ];
    }

    /**
     * Obter estatísticas de moderação
     */
    public static function obterEstatisticasModeracao($interesseId = null): array
    {
        $estatisticas = [
            'infracoes_pendentes' => InfracaoSistema::where('verificada', false)->count(),
            'penalidades_ativas' => PenalidadeUsuario::ativas()->count(),
            'palavras_proibidas_globais' => PalavraProibidaGlobal::where('ativo', true)->count(),
        ];

        if ($interesseId) {
            $interesse = Interesse::find($interesseId);
            $estatisticas['interesse'] = [
                'palavras_proibidas' => $interesse->palavrasProibidas()->where('ativo', true)->count(),
                'alertas_ativos' => $interesse->alertasModeracao()->where('ativo', true)->count(),
                'moderadores' => $interesse->moderadores()->count(),
            ];
        }

        return $estatisticas;
    }

    /**
     * Aplicar penalidade automática por palavras proibidas
     */
    public static function aplicarPenalidadeAutomatica($usuarioId, $texto, $postagemId = null, $interesseId = null): bool
    {
        $violacoesGlobais = PalavraProibidaGlobal::verificarTexto($texto);
        
        if (!empty($violacoesGlobais)) {
            $penalidade = self::aplicarPenalidade(
                $usuarioId,
                'sistema',
                $interesseId,
                'Uso de palavras proibidas no conteúdo',
                1, // Peso leve para primeira infração
                1, // Sistema
                30 // Dias de expiração
            );

            // Registrar infração
            self::registrarInfracaoSistema(
                $usuarioId,
                'conteudo_proibido',
                'Conteúdo contém palavras proibidas automaticamente detectadas',
                $texto,
                $postagemId,
                $interesseId
            );

            return true;
        }

        return false;
    }

    /**
     * Processar postagem automaticamente
     */
    public static function processarPostagemAutomaticamente(Postagem $postagem): bool
    {
        $violacoes = self::verificarPostagem($postagem);
        
        if (!empty($violacoes)) {
            // Bloquear postagem automaticamente
            $postagem->bloquearAutomaticamente('Conteúdo viola regras automáticas');

            // Aplicar penalidade
            foreach ($violacoes as $interesseNome => $violacoesInteresse) {
                foreach ($postagem->interesses as $interesse) {
                    if ($interesse->nome === $interesseNome) {
                        self::aplicarPenalidadeAutomatica(
                            $postagem->usuario_id,
                            $postagem->texto_postagem,
                            $postagem->id,
                            $interesse->id
                        );
                        break;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Notificar usuário sobre penalidade
     */
    public static function notificarUsuarioPenalidade($usuarioId, $penalidade): void
    {
        $usuario = Usuario::find($usuarioId);
        
        Log::info("Usuário {$usuario->nome} recebeu penalidade: {$penalidade->motivo}");
    }

    /**
     * Verificar e processar banimentos automáticos
     */
    public static function processarBanimentosAutomaticos(): array
    {
        $resultados = [
            'sistema' => 0,
            'interesse' => 0
        ];

        // Buscar usuários com 3 penalidades de sistema
        $usuariosSistema = Usuario::whereHas('penalidades', function($query) {
            $query->ativas()->doTipo('sistema');
        })->get();

        foreach ($usuariosSistema as $usuario) {
            if ($usuario->deveSerBanidoSistema()) {
                $resultados['sistema']++;
            }
        }

        // Buscar usuários com 3 penalidades de interesse
        $usuariosInteresse = Usuario::whereHas('penalidades', function($query) {
            $query->ativas()->doTipo('interesse');
        })->get();

        foreach ($usuariosInteresse as $usuario) {
            $penalidadesPorInteresse = $usuario->penalidadesAtivas()
                                            ->doTipo('interesse')
                                            ->get()
                                            ->groupBy('interesse_id');

            foreach ($penalidadesPorInteresse as $interesseId => $penalidades) {
                if ($penalidades->count() >= 3) {
                    self::expulsarUsuario(
                        $usuario->id,
                        $interesseId,
                        "3 penalidades acumuladas no interesse",
                        1, // Sistema
                        true // Permanente
                    );
                    $resultados['interesse']++;
                }
            }
        }

        return $resultados;
    }
}