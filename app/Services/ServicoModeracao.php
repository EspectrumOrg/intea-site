<?php

namespace App\Services;

use App\Models\{
    Interesse, 
    Usuario, 
    Postagem, 
    AlertaModeracao,
    InteresseExpulsao,
    PalavraProibida
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
            if (!$moderador->podeModerar($interesse->id)) {
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
}