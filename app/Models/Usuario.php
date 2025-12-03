<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Banimento[] $banimentos
 * @method \Illuminate\Database\Eloquent\Relations\HasMany banimentos()
 */

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tb_usuario';

    public $fillable = [
        'nome',
        'user',
        'apelido',
        'email',
        'senha',
        'cpf',
        'genero',
        'data_nascimento',
        'foto',
        'descricao',
        'visibilidade',
        'tipo_usuario',
        'status_conta',
        'tema_preferencia',
        'tema_interface',
        'onboarding_concluido',
        'onboarding_concluido_em',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'onboarding_concluido' => 'boolean',
        'onboarding_concluido_em' => 'datetime'
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'usuario_id');
    }

    public function autista()
    {
        return $this->hasOne(Autista::class, 'usuario_id');
    }

    public function comunidade()
    {
        return $this->hasOne(Comunidade::class, 'usuario_id');
    }

    public function profissionalsaude()
    {
        return $this->hasOne(ProfissionalSaude::class, 'usuario_id');
    }

    public function responsavel()
    {
        return $this->hasOne(Responsavel::class, 'usuario_id');
    }

    public function telefones()
    {
        return $this->hasMany(FoneUsuario::class, 'usuario_id');
    }

    public function postagens()
    {
        return $this->hasMany(Postagem::class, 'usuario_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_usuario');
    }

    public function getPostagensCountAttribute()
    {
        return $this->postagens()->count();
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'id_usuario_denunciante');
    }

    public function banimentos(): HasMany
    {
        return $this->hasMany(Banimento::class, 'id_usuario');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero');
    }

    public function respostaSuporte()
    {
        return $this->hasMany(RespostaSuporte::class, 'usuario_id');
    }

    public function seguindo()
    {
        return $this->belongsToMany(
            Usuario::class,
            'tb_seguir',
            'segue_id',
            'seguindo_id'
        )->withTimestamps();
    }

    public function grupos()
    {
        return $this->belongsToMany(
            GruposModel::class,
            'tb_gruposdacomunidade_usuarios',
            'idusuario',
            'idGruposComunidade'
        );
    }

    public function seguidores()
    {
        return $this->belongsToMany(
            self::class,
            'tb_seguir',
            'seguindo_id',
            'segue_id'
        )->withTimestamps();
    }

    public function interesses()
    {
        return $this->belongsToMany(Interesse::class, 'interesse_usuario')
            ->withPivot('notificacoes', 'seguindo_desde')
            ->withTimestamps();
    }

    /**
     * RELAÇÕES PARA O SISTEMA DE MODERAÇÃO E PROPRIEDADE
     */

    // Interesses onde é dono
    public function interessesComoDono()
    {
        return $this->belongsToMany(Interesse::class, 'interesse_moderadores')
                    ->wherePivot('cargo', 'dono')
                    ->withTimestamps();
    }

    // Interesses onde é moderador (incluindo dono)
    public function interessesComoModerador()
    {
        return $this->belongsToMany(Interesse::class, 'interesse_moderadores')
                    ->withPivot('cargo')
                    ->withTimestamps();
    }

    public function alertasModeracao()
    {
        return $this->hasMany(AlertaModeracao::class);
    }

    public function expulsoesInteresses()
    {
        return $this->hasMany(InteresseExpulsao::class);
    }

    public function postagensModeradas()
    {
        return $this->hasMany(Postagem::class, 'removida_por');
    }

    // Penalidades do usuário
    public function penalidades()
    {
        return $this->hasMany(PenalidadeUsuario::class, 'usuario_id');
    }

    // Infrações do usuário
    public function infracoes()
    {
        return $this->hasMany(InfracaoSistema::class, 'usuario_id');
    }

    // Palavras proibidas adicionadas pelo usuário
    public function palavrasProibidasAdicionadas()
    {
        return $this->hasMany(PalavraProibida::class, 'adicionado_por');
    }

    // Palavras proibidas globais adicionadas pelo usuário
    public function palavrasProibidasGlobaisAdicionadas()
    {
        return $this->hasMany(PalavraProibidaGlobal::class, 'adicionado_por');
    }

    /**
     * MÉTODOS DE INTERESSES
     */

    public function postagensNoInteresse($interesseId)
    {
        return $this->postagens()
            ->whereHas('interesses', function ($query) use ($interesseId) {
                $query->where('interesses.id', $interesseId);
            })
            ->get();
    }

    public function seguirInteresse($interesseId, $notificacoes = true): void
    {
        $this->interesses()->attach($interesseId, [
            'notificacoes' => $notificacoes,
            'seguindo_desde' => now()
        ]);

        $interesse = Interesse::find($interesseId);
        $interesse->atualizarContadores();
    }

    public function deixarSeguirInteresse($interesseId): void
    {
        $this->interesses()->detach($interesseId);

        $interesse = Interesse::find($interesseId);
        $interesse->atualizarContadores();
    }

    public function segueInteresse($interesseId): bool
    {
        return $this->interesses()->where('interesses.id', $interesseId)->exists();
    }

    public function estaExpulsoDe($interesseId): bool
    {
        return $this->expulsoesInteresses()
            ->where('interesse_id', $interesseId)
            ->where(function ($query) {
                $query->where('permanente', true)
                    ->orWhere('expulso_ate', '>', now());
            })
            ->exists();
    }

    public function obterFeedInteresses($limite = 20)
    {
        $interessesIds = $this->interesses()->pluck('interesses.id');

        return Postagem::with(['usuario', 'imagens', 'interesses'])
            ->whereHas('interesses', function ($query) use ($interessesIds) {
                $query->whereIn('interesses.id', $interessesIds);
            })
            ->where(function ($query) {
                $query->where('visibilidade_interesse', 'publico')
                    ->orWhere(function ($q) {
                        $q->where('visibilidade_interesse', 'seguidores');
                    });
            })
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

    public function obterInteressesSugeridos($limite = 6)
    {
        return Interesse::ativos()
            ->whereNotIn('id', $this->interesses()->pluck('interesses.id'))
            ->populares($limite)
            ->get();
    }

    public function obterEstatisticasModeracao()
    {
        return [
            'alertas_ativos' => $this->alertasModeracao()->where('ativo', true)->count(),
            'expulsoes' => $this->expulsoesInteresses()->count(),
            'postagens_moderadas' => $this->postagensModeradas()->count(),
        ];
    }

    public function onboardingConcluido(): bool
    {
        return $this->onboarding_concluido ?? false;
    }

    public function completarOnboarding()
    {
        $this->update([
            'onboarding_concluido' => true,
            'onboarding_concluido_em' => now()
        ]);
    }

    public function temInteresses(): bool
    {
        return $this->interesses()->exists();
    }

    public function obterIdsInteresses(): array
    {
        return $this->interesses()->pluck('interesses.id')->toArray();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * MÉTODOS DE VERIFICAÇÃO DE CARGO E PROPRIEDADE
     */

    // Verificar se é dono de um interesse
    public function isDonoInteresse($interesseId): bool
    {
        return $this->interessesComoModerador()
                    ->where('interesse_id', $interesseId)
                    ->wherePivot('cargo', 'dono')
                    ->exists();
    }

    // Verificar se é moderador de um interesse (incluindo dono)
    public function isModeradorInteresse($interesseId): bool
    {
        return $this->interessesComoModerador()
                    ->where('interesse_id', $interesseId)
                    ->exists();
    }

    // Verificar se pode moderar (dono, moderador ou admin)
    public function podeModerarInteresse($interesseId): bool
    {
        return $this->isModeradorInteresse($interesseId) || $this->isAdministrador();
    }

    /**
     * MÉTODOS DE PENALIDADES
     */

    // Verificar penalidades ativas
    public function penalidadesAtivas()
    {
        return $this->penalidades()->ativas();
    }

    // Verificar se tem 3 penalidades de sistema
    public function deveSerBanidoSistema(): bool
    {
        $penalidadesSistema = $this->penalidadesAtivas()
                                ->doTipo('sistema')
                                ->count();
        
        return $penalidadesSistema >= 3;
    }

    // Verificar se tem 3 penalidades de um interesse específico
    public function deveSerBanidoInteresse($interesseId): bool
    {
        $penalidadesInteresse = $this->penalidadesAtivas()
                                    ->doTipo('interesse')
                                    ->doInteresse($interesseId)
                                    ->count();
        
        return $penalidadesInteresse >= 3;
    }

    // Obter contagem de penalidades por tipo
    public function contadorPenalidades($tipo = null, $interesseId = null): int
    {
        $query = $this->penalidadesAtivas();

        if ($tipo) {
            $query = $query->doTipo($tipo);
        }

        if ($interesseId) {
            $query = $query->doInteresse($interesseId);
        }

        return $query->count();
    }

    /**
     * MÉTODOS DE GERENCIAMENTO DE MODERADORES E PROPRIEDADE
     */

    // Adicionar moderador a um interesse (apenas donos)
    public function adicionarModerador($interesseId, $usuarioId): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        // Não permitir adicionar a si mesmo
        if ($this->id == $usuarioId) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        
        // Verificar se já é moderador
        if ($interesse->moderadores()->where('usuario_id', $usuarioId)->exists()) {
            return false;
        }

        $interesse->moderadores()->attach($usuarioId, [
            'cargo' => 'moderador',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return true;
    }

    // Remover moderador de um interesse (apenas donos)
    public function removerModerador($interesseId, $usuarioId): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        // Não permitir remover a si mesmo
        if ($this->id == $usuarioId) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        $interesse->moderadores()->detach($usuarioId);

        return true;
    }

    // Promover moderador a dono (apenas donos atuais)
    public function promoverADono($interesseId, $usuarioId): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        
        // Atualizar cargo para dono
        $interesse->moderadores()->updateExistingPivot($usuarioId, [
            'cargo' => 'dono',
            'updated_at' => now()
        ]);

        return true;
    }

    /**
     * MÉTODOS DE CRIAÇÃO E GERENCIAMENTO DE INTERESSES
     */

    // Criar um novo interesse (torna-se dono automaticamente)
    public function criarInteresse($dados)
    {
        $interesse = Interesse::criar($dados);
        
        // Tornar-se dono do interesse
        $interesse->moderadores()->attach($this->id, [
            'cargo' => 'dono',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Seguir o interesse automaticamente
        $this->seguirInteresse($interesse->id, true);

        return $interesse;
    }

    // Editar interesse (apenas dono)
    public function editarInteresse($interesseId, $dados): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        
        if (!$interesse) {
            return false;
        }

        try {
            $interesse->update($dados);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Deletar interesse (apenas dono)
    public function deletarInteresse($interesseId): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        
        if (!$interesse) {
            return false;
        }

        try {
            $interesse->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Remover postagem do interesse (dono ou moderador)
    public function removerPostagemDoInteresse($interesseId, $postagemId, $motivo = null): bool
    {
        if (!$this->isModeradorInteresse($interesseId)) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        $postagem = Postagem::find($postagemId);

        if (!$interesse || !$postagem) {
            return false;
        }

        try {
            // Remover do interesse
            $interesse->removerPostagem($postagemId);

            // Registrar ação de moderação
            \App\Models\HistoricoModeracao::create([
                'interesse_id' => $interesseId,
                'usuario_id' => $this->id,
                'postagem_id' => $postagemId,
                'acao' => 'remocao_postagem',
                'motivo' => $motivo,
                'created_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Transferir propriedade (apenas dono atual)
    public function transferirPropriedade($interesseId, $novoDonoId): bool
    {
        if (!$this->isDonoInteresse($interesseId)) {
            return false;
        }

        // Não permitir transferir para si mesmo
        if ($this->id == $novoDonoId) {
            return false;
        }

        $interesse = Interesse::find($interesseId);
        
        // Verificar se o novo dono é moderador
        if (!$interesse->moderadores()->where('usuario_id', $novoDonoId)->exists()) {
            // Adicionar como moderador primeiro
            $interesse->moderadores()->attach($novoDonoId, [
                'cargo' => 'moderador',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Atualizar cargo do novo dono para dono
        $interesse->moderadores()->updateExistingPivot($novoDonoId, [
            'cargo' => 'dono',
            'updated_at' => now()
        ]);

        // Atualizar cargo do dono atual para moderador
        $interesse->moderadores()->updateExistingPivot($this->id, [
            'cargo' => 'moderador',
            'updated_at' => now()
        ]);

        return true;
    }

    /**
     * MÉTODOS DE BANIMENTO
     */

    // Verificar se está banido do sistema
    public function estaBanidoSistema(): bool
    {
        return $this->banimentos()
                    ->where('ativo', true)
                    ->where(function($query) {
                        $query->where('permanente', true)
                              ->orWhere('data_expiracao', '>', now());
                    })
                    ->exists();
    }

    // Verificar se está banido de um interesse específico
    public function estaBanidoInteresse($interesseId): bool
    {
        return $this->expulsoesInteresses()
                    ->where('interesse_id', $interesseId)
                    ->where(function($query) {
                        $query->where('permanente', true)
                              ->orWhere('expulso_ate', '>', now());
                    })
                    ->exists();
    }

    /**
     * MÉTODOS DE MODERAÇÃO DE CONTEÚDO
     */

    // Verificar se pode postar em um interesse
    public function podePostarNoInteresse($interesseId): bool
    {
        $interesse = Interesse::find($interesseId);
        
        if (!$interesse) {
            return false;
        }

        // Verificar se está banido do sistema
        if ($this->estaBanidoSistema()) {
            return false;
        }

        // Verificar se está banido do interesse
        if ($this->estaBanidoInteresse($interesseId)) {
            return false;
        }

        // Verificar se a moderação está ativa e se tem muitas penalidades
        if ($interesse->moderacao_ativa) {
            $alertasAtivos = $interesse->obterContadorAlertasUsuario($this->id);
            return $alertasAtivos < $interesse->limite_alertas_ban;
        }

        return true;
    }

    // Verificar se pode comentar em uma postagem
    public function podeComentar($postagemId): bool
    {
        $postagem = Postagem::find($postagemId);
        
        if (!$postagem) {
            return false;
        }

        // Verificar banimento do sistema
        if ($this->estaBanidoSistema()) {
            return false;
        }

        // Para cada interesse da postagem, verificar se está banido
        foreach ($postagem->interesses as $interesse) {
            if ($this->estaBanidoInteresse($interesse->id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * MÉTODOS DE VERIFICAÇÃO DE PERMISSÃO
     */

    public function podeGerenciarInteresse($interesseId): bool
    {
        return $this->isModeradorInteresse($interesseId) || $this->isAdministrador();
    }

    public function podeEditarInteresse($interesseId): bool
    {
        return $this->isDonoInteresse($interesseId) || $this->isAdministrador();
    }

    public function podeDeletarInteresse($interesseId): bool
    {
        return $this->isDonoInteresse($interesseId) || $this->isAdministrador();
    }

    public function podeRemoverPostagem($interesseId): bool
    {
        return $this->isModeradorInteresse($interesseId) || $this->isAdministrador();
    }

    public function podeAdicionarModerador($interesseId): bool
    {
        return $this->isDonoInteresse($interesseId) || $this->isAdministrador();
    }

    // Obter interesses que pode gerenciar
    public function obterInteressesGerenciáveis()
    {
        return $this->interessesComoModerador()
                    ->withCount(['seguidores', 'postagens'])
                    ->get()
                    ->map(function($interesse) {
                        $interesse->pode_editar = $this->isDonoInteresse($interesse->id);
                        $interesse->pode_deletar = $this->isDonoInteresse($interesse->id);
                        $interesse->pode_moderar = $this->isModeradorInteresse($interesse->id);
                        return $interesse;
                    });
    }

    /**
     * MÉTODOS DE ESTATÍSTICAS
     */

    // Obter estatísticas de moderação do usuário
    public function obterEstatisticasModeracaoCompleta(): array
    {
        return [
            'alertas_ativos' => $this->alertasModeracao()->where('ativo', true)->count(),
            'expulsoes' => $this->expulsoesInteresses()->count(),
            'postagens_moderadas' => $this->postagensModeradas()->count(),
            'penalidades_sistema' => $this->contadorPenalidades('sistema'),
            'penalidades_interesse' => $this->contadorPenalidades('interesse'),
            'infracoes_reportadas' => $this->infracoes()->count(),
            'palavras_adicionadas' => $this->palavrasProibidasAdicionadas()->count(),
            'palavras_globais_adicionadas' => $this->palavrasProibidasGlobaisAdicionadas()->count(),
        ];
    }

    // Obter interesses moderados pelo usuário
    public function obterInteressesModerados()
    {
        return $this->interessesComoModerador()
                    ->withCount(['seguidores', 'postagens'])
                    ->get();
    }

    /**
     * MÉTODOS DE NOTIFICAÇÃO
     */

    // Notificar moderadores sobre nova postagem
    public function notificarModeradoresPostagem($postagem, $interesseId): void
    {
        $interesse = Interesse::find($interesseId);
        $moderadores = $interesse->moderadores;

        foreach ($moderadores as $moderador) {
            // Aqui você implementaria o sistema de notificações
            // $moderador->notify(new NovaPostagemParaModeracao($postagem, $interesse));
        }
    }

    /**
     * MÉTODOS DE VERIFICAÇÃO DE CONTEÚDO
     */

    // Verificar se o conteúdo do usuário viola regras
    public function verificarConteudoParaPostagem($texto, $interesseId = null): array
    {
        $violacoes = [];

        // Verificar palavras proibidas globais
        $violacoesGlobais = PalavraProibidaGlobal::verificarTexto($texto);
        if (!empty($violacoesGlobais)) {
            $violacoes['globais'] = $violacoesGlobais;
        }

        // Verificar palavras proibidas do interesse específico
        if ($interesseId) {
            $violacoesInteresse = PalavraProibida::verificarTexto($texto, $interesseId);
            if (!empty($violacoesInteresse)) {
                $violacoes['interesse'] = $violacoesInteresse;
            }
        }

        return $violacoes;
    }

    /**
     * MÉTODOS DE LIMPEZA E MANUTENÇÃO
     */

    // Limpar penalidades expiradas
    public function limparPenalidadesExpiradas(): int
    {
        return $this->penalidades()
                    ->where('ativa', true)
                    ->where('expira_em', '<', now())
                    ->update(['ativa' => false]);
    }

    // Obter histórico de moderação
    public function obterHistoricoModeracao($limite = 20)
    {
        return [
            'penalidades' => $this->penalidades()
                                ->with(['interesse', 'aplicadoPor'])
                                ->orderBy('created_at', 'desc')
                                ->limit($limite)
                                ->get(),
            'alertas' => $this->alertasModeracao()
                            ->with(['interesse', 'moderador'])
                            ->orderBy('created_at', 'desc')
                            ->limit($limite)
                            ->get(),
            'expulsoes' => $this->expulsoesInteresses()
                            ->with(['interesse', 'moderador'])
                            ->orderBy('created_at', 'desc')
                            ->limit($limite)
                            ->get(),
        ];
    }

    /**
     * MÉTODOS DE RELATÓRIOS
     */

    // Gerar relatório de atividades de moderação
    public function gerarRelatorioModeracao($periodoInicio, $periodoFim): array
    {
        return [
            'periodo' => [
                'inicio' => $periodoInicio,
                'fim' => $periodoFim
            ],
            'penalidades_aplicadas' => $this->penalidades()
                                        ->whereBetween('created_at', [$periodoInicio, $periodoFim])
                                        ->count(),
            'postagens_moderadas' => $this->postagensModeradas()
                                        ->whereBetween('removida_em', [$periodoInicio, $periodoFim])
                                        ->count(),
            'alertas_emitidos' => $this->alertasModeracao()
                                    ->whereBetween('created_at', [$periodoInicio, $periodoFim])
                                    ->count(),
            'palavras_adicionadas' => $this->palavrasProibidasAdicionadas()
                                        ->whereBetween('created_at', [$periodoInicio, $periodoFim])
                                        ->count(),
        ];
    }

    /**
     * MÉTODOS DE SEGURANÇA
     */

    // Verificar se o usuário tem permissões administrativas
public function isAdministrador(): bool
{
    // Verificar se é administrador pelo tipo_usuario
    if ($this->tipo_usuario == 1 || $this->tipo_usuario === '1') {
        return true;
    }
    
    // Verificar também pela relação admin() para compatibilidade
    return $this->admin()->exists() || ($this->tipo_usuario ?? 0) == 1;
}

    // Verificar se o usuário pode acessar painel de moderação
    public function podeAcessarPainelModeracao(): bool
    {
        return $this->isAdministrador() || 
               $this->interessesComoModerador()->exists() ||
               $this->penalidades()->exists() && $this->contadorPenalidades() > 0;
    }

    /**
     * MÉTODOS DE CONVENIÊNCIA
     */

    // Obter todos os interesses que o usuário pode moderar
    public function obterInteressesModeraveis()
    {
        if ($this->isAdministrador()) {
            return Interesse::ativos()->get();
        }

        return $this->interessesComoModerador()->get();
    }

    // Verificar se o usuário tem alguma penalidade ativa
    public function temPenalidadesAtivas(): bool
    {
        return $this->penalidadesAtivas()->exists();
    }

    // Obter resumo do status de moderação do usuário
    public function obterResumoStatusModeracao(): array
    {
        return [
            'banido_sistema' => $this->estaBanidoSistema(),
            'penalidades_ativas' => $this->penalidadesAtivas()->count(),
            'alertas_ativos' => $this->alertasModeracao()->where('ativo', true)->count(),
            'pode_postar' => !$this->estaBanidoSistema() && $this->penalidadesAtivas()->count() < 3,
            'eh_moderador' => $this->interessesComoModerador()->exists(),
            'eh_administrador' => $this->isAdministrador(),
        ];
    }
}