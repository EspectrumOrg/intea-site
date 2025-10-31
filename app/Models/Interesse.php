<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interesse extends Model
{
    protected $table = 'interesses';

    protected $fillable = [
        'nome', 'slug', 'icone', 'cor', 'descricao', 'sobre', 
        'banner', 'contador_membros', 'contador_postagens',
        'destaque', 'ativo', 'moderacao_ativa', 'limite_alertas_ban', 'dias_expiracao_alerta'
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'ativo' => 'boolean',
        'moderacao_ativa' => 'boolean'
    ];

    public function seguidores(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'interesse_usuario')
                    ->withPivot('notificacoes', 'seguindo_desde')
                    ->withTimestamps();
    }

    public function postagens(): BelongsToMany
    {
        return $this->belongsToMany(Postagem::class, 'interesse_postagem')
                    ->withPivot('tipo', 'categorizado_por', 'observacao')
                    ->withTimestamps();
    }

    public function moderadores(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'interesse_moderadores')
                    ->withPivot('cargo')
                    ->withTimestamps();
    }

    public function alertasModeracao(): HasMany
    {
        return $this->hasMany(AlertaModeracao::class);
    }

    public function expulsoes(): HasMany
    {
        return $this->hasMany(InteresseExpulsao::class);
    }

    public function palavrasProibidas(): HasMany
    {
        return $this->hasMany(PalavraProibida::class);
    }

    public function postagensVisiveis()
    {
        return $this->postagens()
                    ->where('bloqueada_auto', false)
                    ->where('removida_manual', false)
                    ->orderBy('created_at', 'desc');
    }

    public function postagensParaRevisao()
    {
        return $this->postagens()
                    ->where('bloqueada_auto', false)
                    ->where('removida_manual', false)
                    ->orderBy('created_at', 'desc');
    }

    public function postagensModeradas()
    {
        return $this->postagens()
                    ->where(function($query) {
                        $query->where('bloqueada_auto', true)
                              ->orWhere('removida_manual', true);
                    })
                    ->orderBy('removida_em', 'desc');
    }

    public function postagensDestacadas($limite = 10)
    {
        return $this->postagensVisiveis()
                    ->withCount('curtidas', 'comentarios')
                    ->orderBy('curtidas_count', 'desc')
                    ->orderBy('comentarios_count', 'desc')
                    ->limit($limite)
                    ->get();
    }

    public function postagensRecentes($limite = 20)
    {
        return $this->postagensVisiveis()
                    ->with(['usuario', 'imagens'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limite)
                    ->get();
    }

    public function usuariosPopulares($limite = 10)
    {
        return $this->seguidores()
                    ->withCount(['postagens' => function($query) {
                        $query->whereHas('interesses', function($q) {
                            $q->where('interesses.id', $this->id);
                        });
                    }])
                    ->orderBy('postagens_count', 'desc')
                    ->limit($limite)
                    ->get();
    }

    public function usuarioSegue($usuarioId): bool
    {
        return $this->seguidores()->where('usuario_id', $usuarioId)->exists();
    }

    public function usuarioEstaExpulso($usuarioId): bool
    {
        return $this->expulsoes()
                    ->where('usuario_id', $usuarioId)
                    ->where(function($query) {
                        $query->where('permanente', true)
                              ->orWhere('expulso_ate', '>', now());
                    })
                    ->exists();
    }

    public function obterContadorAlertasUsuario($usuarioId): int
    {
        return $this->alertasModeracao()
                    ->where('usuario_id', $usuarioId)
                    ->where('ativo', true)
                    ->where(function($query) {
                        $query->whereNull('expiracao')
                              ->orWhere('expiracao', '>', now());
                    })
                    ->count();
    }

    public function usuarioPodePostar($usuarioId): bool
    {
        if (!$this->moderacao_ativa) {
            return true;
        }

        if ($this->usuarioEstaExpulso($usuarioId)) {
            return false;
        }

        $alertasAtivos = $this->obterContadorAlertasUsuario($usuarioId);
        return $alertasAtivos < $this->limite_alertas_ban;
    }

    public function sugerirPostagem(Postagem $postagem): bool
    {
        $palavrasChave = $this->obterPalavrasChave();
        $conteudo = strtolower($postagem->texto_postagem);
        
        $correspondencias = 0;
        foreach ($palavrasChave as $palavra) {
            if (str_contains($conteudo, $palavra)) {
                $correspondencias++;
            }
        }
        
        return $correspondencias >= 2;
    }

    public function obterPalavrasChave(): array
    {
        $palavrasChave = [
            'tecnologia' => [
                'tecnologia', 'tech', 'programação', 'código', 'software', 'aplicativo',
                'web', 'digital', 'inteligência artificial', 'ia', 'robot', 'computador',
                'internet', 'smartphone', 'aplicativo', 'desenvolvimento', 'startup'
            ],
            'esportes' => [
                'esporte', 'futebol', 'bola', 'jogo', 'competição', 'atleta', 'olimpíada',
                'corrida', 'campo', 'time', 'campeonato', 'treino', 'vitória', 'derrota'
            ],
            'musica' => [
                'música', 'canção', 'banda', 'artista', 'show', 'festival', 'álbum',
                'single', 'cantor', 'cantora', 'concerto', 'letra', 'instrumento', 'som'
            ],
            'games' => [
                'game', 'jogo', 'gaming', 'player', 'console', 'nível', 'personagem',
                'missão', 'online', 'multiplayer', 'steam', 'xbox', 'playstation'
            ],
            'filmes-series' => [
                'filme', 'série', 'cinema', 'streaming', 'ator', 'diretor', 'episódio',
                'temporada', 'crítica', 'roteiro', 'cena', 'netflix', 'amazon prime'
            ],
            'arte-design' => [
                'arte', 'design', 'criativo', 'pintura', 'fotografia', 'ilustração',
                'desenho', 'exposição', 'galeria', 'criatividade', 'conceito', 'visual'
            ],
            'ciencia' => [
                'ciência', 'pesquisa', 'experimento', 'laboratório', 'descoberta',
                'teoria', 'hipótese', 'artigo', 'científico', 'inovação', 'estudo'
            ],
            'viagens' => [
                'viagem', 'turismo', 'destino', 'hotel', 'passagem', 'viajar',
                'paisagem', 'cultura', 'aventura', 'lugares', 'turístico'
            ],
            'culinaria' => [
                'culinária', 'comida', 'receita', 'restaurante', 'chef', 'gourmet',
                'cozinha', 'ingrediente', 'sabor', 'prato', 'gastronomia'
            ],
            'moda' => [
                'moda', 'estilo', 'roupa', 'vestido', 'modelo', 'beleza',
                'coleção', 'desfile', 'tendência', 'acessório', 'maquiagem'
            ],
            'negocios' => [
                'negócio', 'empresa', 'mercado', 'investimento', 'lucro',
                'cliente', 'produto', 'serviço', 'empreendedor', 'startup'
            ],
            'saude' => [
                'saúde', 'fitness', 'exercício', 'academia', 'alimentação',
                'nutrição', 'medicamento', 'tratamento', 'bem-estar', 'corpo'
            ]
        ];

        return $palavrasChave[$this->slug] ?? [$this->nome];
    }

    public function adicionarPalavraProibida($palavra, $tipo, $usuarioId, $motivo = null): void
    {
        $this->palavrasProibidas()->create([
            'palavra' => $palavra,
            'tipo' => $tipo,
            'adicionado_por' => $usuarioId,
            'motivo' => $motivo
        ]);
    }

    public function atualizarContadores(): void
    {
        $this->update([
            'contador_membros' => $this->seguidores()->count(),
            'contador_postagens' => $this->postagens()->count(),
        ]);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    public function scopePopulares($query, $limite = 10)
    {
        return $query->orderBy('contador_membros', 'desc')->limit($limite);
    }
}