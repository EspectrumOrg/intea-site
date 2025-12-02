<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Interesse extends Model
{
    protected $table = 'interesses';

    protected $fillable = [
        'nome', 'slug', 'icone', 'icone_custom', 'cor', 'descricao', 'sobre', 
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

   public function getIconeAttribute($value)
{
    // Se tem ícone customizado, retorna a URL completa
    if ($this->icone_custom && $this->attributes['icone'] === 'custom') {
        // Verifica se o arquivo existe
        $path = $this->icone_custom;
        
        // Garante que o caminho está correto
        if (!empty($path)) {
            // Se o caminho não começa com 'storage/', adiciona
            if (!str_starts_with($path, 'storage/')) {
                // Verifica se o arquivo existe fisicamente
                $fullPath = storage_path('app/public/' . $path);
                
                if (file_exists($fullPath)) {
                    // Retorna URL usando Storage
                    return Storage::url($path);
                } else {
                    // Se não encontrou, tenta sem 'arquivos/'
                    if (str_starts_with($path, 'arquivos/')) {
                        $altPath = substr($path, 9); // Remove 'arquivos/'
                        $altFullPath = storage_path('app/public/' . $altPath);
                        
                        if (file_exists($altFullPath)) {
                            return Storage::url($altPath);
                        }
                    }
                    
                    // Se ainda não encontrou, retorna fallback
                    \Log::warning("Ícone custom não encontrado: {$path}");
                    return null;
                }
            }
            
            return $path;
        }
        
        return null;
    }
    
    // Se não é custom, retorna o valor original (nome do ícone Material)
    return $value;
}

/**
 * Accessor adicional para obter URL do ícone custom
 */
public function getIconeCustomUrlAttribute()
{
    if (!$this->icone_custom || $this->attributes['icone'] !== 'custom') {
        return null;
    }
    
    return Storage::url($this->icone_custom);
}

    /**
     * Acessor para obter o tipo de ícone
     */
    public function getTipoIconeAttribute()
    {
        return $this->icone_custom ? 'custom' : 'default';
    }

    /**
     * Acessor para obter o nome do arquivo do ícone customizado
     */
    public function getNomeIconeCustomAttribute()
    {
        if ($this->icone_custom) {
            return basename($this->icone_custom);
        }
        return null;
    }

    /**
     * Mutator para garantir que o slug seja sempre único
     */
    public function setSlugAttribute($value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $counter = 1;
        $originalSlug = $slug;
        
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $this->attributes['slug'] = $slug;
    }

    /**
     * Escopo para interesses ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Escopo para interesses em destaque
     */
    public function scopeDestaques($query)
    {
        return $query->where('destaque', true);
    }

    /**
     * Escopo para interesses populares
     */
    public function scopePopulares($query, $limite = 10)
    {
        return $query->orderBy('contador_membros', 'desc')->limit($limite);
    }

    /**
     * Escopo para buscar interesses por termo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
              ->orWhere('descricao', 'LIKE', "%{$termo}%")
              ->orWhere('sobre', 'LIKE', "%{$termo}%");
        });
    }

    /**
     * Método para deletar o ícone customizado quando o interesse for deletado
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($interesse) {
            // Deleta o arquivo do ícone customizado se existir
            if ($interesse->icone_custom) {
                Storage::disk('public')->delete($interesse->icone_custom);
            }
        });

        static::updating(function ($interesse) {
            // Se está atualizando e mudou o ícone customizado, deleta o antigo
            if ($interesse->isDirty('icone_custom') && $interesse->getOriginal('icone_custom')) {
                Storage::disk('public')->delete($interesse->getOriginal('icone_custom'));
            }
        });
    }

    /**
     * Método para criar um novo interesse - CORRIGIDO
     */
    public static function criar($dados)
    {
        // Gerar slug único
        $slug = \Illuminate\Support\Str::slug($dados['nome']);
        $counter = 1;
        $originalSlug = $slug;
        
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return static::create([
            'nome' => $dados['nome'],
            'slug' => $slug,
            'descricao' => $dados['descricao'],
            'sobre' => $dados['sobre'] ?? null,
            'icone' => $dados['icone'] ?? 'smartphone',
            'icone_custom' => $dados['icone_custom'] ?? null,
            'cor' => $dados['cor'] ?? '#3B82F6',
            'banner' => $dados['banner'] ?? null,
            'contador_membros' => 0, // Começa com 0, será incrementado quando o criador seguir
            'contador_postagens' => 0,
            'destaque' => $dados['destaque'] ?? false,
            'ativo' => $dados['ativo'] ?? true,
            'moderacao_ativa' => $dados['moderacao_ativa'] ?? true,
            'limite_alertas_ban' => $dados['limite_alertas_ban'] ?? 3,
            'dias_expiracao_alerta' => $dados['dias_expiracao_alerta'] ?? 30,
        ]);
    }

    /**
     * Método para adicionar um seguidor
     */
    public function adicionarSeguidor($usuarioId, $notificacoes = true)
    {
        $this->seguidores()->attach($usuarioId, [
            'notificacoes' => $notificacoes,
            'seguindo_desde' => now()
        ]);

        $this->increment('contador_membros');
    }

    /**
     * Método para remover um seguidor
     */
    public function removerSeguidor($usuarioId)
    {
        $this->seguidores()->detach($usuarioId);
        $this->decrement('contador_membros');
    }

    /**
     * Método para adicionar uma postagem
     */
    public function adicionarPostagem($postagemId, $tipo = 'manual', $categorizadoPor = null, $observacao = null)
    {
        $this->postagens()->attach($postagemId, [
            'tipo' => $tipo,
            'categorizado_por' => $categorizadoPor,
            'observacao' => $observacao,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->increment('contador_postagens');
    }

    /**
     * Método para remover uma postagem
     */
    public function removerPostagem($postagemId)
    {
        $this->postagens()->detach($postagemId);
        $this->decrement('contador_postagens');
    }

    /**
     * Método para verificar se uma palavra é proibida
     */
    public function palavraEhProibida($palavra, $tipo = 'exata')
    {
        return $this->palavrasProibidas()
                    ->where('tipo', $tipo)
                    ->where('palavra', $tipo === 'exata' ? $palavra : 'LIKE', "%{$palavra}%")
                    ->exists();
    }

    /**
     * Método para obter estatísticas do interesse
     */
    public function obterEstatisticas()
    {
        return [
            'membros' => $this->contador_membros,
            'postagens' => $this->contador_postagens,
            'moderadores' => $this->moderadores()->count(),
            'palavras_proibidas' => $this->palavrasProibidas()->count(),
            'criado_em' => $this->created_at->format('d/m/Y'),
            'ativo' => $this->ativo ? 'Sim' : 'Não',
            'moderacao_ativa' => $this->moderacao_ativa ? 'Sim' : 'Não',
        ];
    }

    public function postagensMaisCurtidas($limite = 20)
    {
        return $this->postagens()
            ->with(['usuario', 'imagens', 'interesses'])
            ->withCount(['curtidas', 'comentarios'])
            ->where('bloqueada_auto', false)
            ->where('removida_manual', false)
            ->orderBy('curtidas_count', 'desc')
            ->limit($limite)
            ->get();
    }
}