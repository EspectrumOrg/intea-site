<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postagem extends Model
{
    use HasFactory;

    protected $table = "tb_postagem";

    protected $fillable = [
        'usuario_id',
        'texto_postagem',
        'bloqueada_auto',
        'removida_manual', 
        'motivo_remocao',
        'removida_em',
        'removida_por',
        'visibilidade_interesse'
    ];

    protected $appends = [
        'curtidas_count',
        'comentario_count',
        'curtidas_usuario',
    ];

    protected $casts = [
        'bloqueada_auto' => 'boolean',
        'removida_manual' => 'boolean',
        'removida_em' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_postagem');
    }

    public function imagens()
    {
        return $this->hasMany(ImagemPostagem::class, 'id_postagem');
    }

    public function curtidas()
    {
        return $this->hasMany(Curtida::class, 'id_postagem');
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'id_postagem');
    }

    public function interesses()
{
    return $this->belongsToMany(Interesse::class, 'interesse_postagem', 'postagem_id', 'interesse_id')
                ->withPivot('tipo', 'categorizado_por', 'observacao')
                ->withTimestamps();
}

    public function tendencias()
    {
        return $this->belongsToMany(Tendencia::class, 'tb_tendencia_postagem', 'postagem_id', 'tendencia_id')
                    ->withTimestamps();
    }

    public function removidaPor()
    {
        return $this->belongsTo(Usuario::class, 'removida_por');
    }

    public function getCurtidasCountAttribute()
    {
        return $this->curtidas()->count();
    }

    public function getComentariosCountAttribute()
    {
        return $this->comentarios()->count();
    }

    public function getCurtidasUsuarioAttribute()
    {
        return $this->curtidas()->where('id_usuario', auth()->id())->exists();
    }

    public function estaVisivel(): bool
    {
        return !$this->bloqueada_auto && !$this->removida_manual;
    }

    public function foiBloqueadaAuto(): bool
    {
        return $this->bloqueada_auto;
    }

    public function foiRemovidaManual(): bool
    {
        return $this->removida_manual;
    }

    public function bloquearAutomaticamente($motivo = null): void
    {
        $this->update([
            'bloqueada_auto' => true,
            'motivo_remocao' => $motivo ?? 'Violação de palavras proibidas',
            'removida_em' => now()
        ]);

        foreach ($this->interesses as $interesse) {
            \App\Services\ServicoModeracao::criarAlertaAutomático(
                $this->usuario_id,
                $interesse->id,
                $this->id,
                "Postagem bloqueada automaticamente: " . ($motivo ?? 'Conteúdo inadequado')
            );
        }
    }

    public function removerManual($moderadorId, $motivo): void
    {
        $this->update([
            'removida_manual' => true,
            'motivo_remocao' => $motivo,
            'removida_por' => $moderadorId,
            'removida_em' => now()
        ]);

        foreach ($this->interesses as $interesse) {
            \App\Services\ServicoModeracao::criarAlertaManual(
                $this->usuario_id,
                $interesse->id,
                $this->id,
                "Postagem removida: " . $motivo,
                $moderadorId,
                'leve'
            );
        }
    }

    public function restaurar(): void
    {
        $this->update([
            'bloqueada_auto' => false,
            'removida_manual' => false,
            'motivo_remocao' => null,
            'removida_por' => null,
            'removida_em' => null
        ]);
    }

    public function verificarViolacoesPalavrasProibidas(): array
    {
        $violacoes = [];

        foreach ($this->interesses as $interesse) {
            $violacoesInteresse = PalavraProibida::verificarTexto($this->texto_postagem, $interesse->id);
            if (!empty($violacoesInteresse)) {
                $violacoes[$interesse->id] = $violacoesInteresse;
            }
        }

        return $violacoes;
    }

    public function categorizarInteresse($interesseId, $tipo = 'manual', $moderadorId = null, $observacao = null): void
    {
        $this->interesses()->attach($interesseId, [
            'tipo' => $tipo,
            'categorizado_por' => $moderadorId,
            'observacao' => $observacao
        ]);

        $interesse = Interesse::find($interesseId);
        $interesse->atualizarContadores();
        
        if ($tipo === 'manual') {
            \App\Services\ServicoModeracao::notificarModeradoresNovaPostagem($this);
        }
    }

    public function sugerirInteressesAutomaticos(): void
    {
        $interesses = Interesse::ativos()->get();
        
        foreach ($interesses as $interesse) {
            $relevancia = $interesse->sugerirPostagem($this);
            
            if ($relevancia) {
                $this->interesses()->attach($interesse->id, [
                    'tipo' => 'sugerido',
                    'categorizado_por' => null,
                    'observacao' => 'Sugerido automaticamente pelo sistema'
                ]);

                $interesse->atualizarContadores();
            }
        }
    }

    public function pertenceAoInteresse($interesseId): bool
    {
        return $this->interesses()->where('interesses.id', $interesseId)->exists();
    }

    public function obterInteressesPrincipais($limite = 3)
    {
        return $this->interesses()
                    ->limit($limite)
                    ->get();
    }

    public function processarHashtags($texto)
    {
        preg_match_all('/#(\w+)/', $texto, $matches);
        
        $hashtags = $matches[1] ?? [];
        $tendenciasIds = [];

        foreach ($hashtags as $tag) {
            $hashtagCompleta = '#' . $tag;
            $slug = Tendencia::criarSlug($hashtagCompleta);
            
            $tendencia = Tendencia::firstOrCreate(
                ['slug' => $slug],
                [
                    'hashtag' => $hashtagCompleta,
                    'contador_uso' => 0,
                    'ultimo_uso' => now()
                ]
            );

            $tendencia->increment('contador_uso');
            $tendencia->update(['ultimo_uso' => now()]);
            
            $tendenciasIds[] = $tendencia->id;
        }

        if (!empty($tendenciasIds)) {
            $this->tendencias()->sync($tendenciasIds);
        }

        return $texto;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($postagem) {
            $postagem->processarHashtags($postagem->texto_postagem);
            $postagem->sugerirInteressesAutomaticos();
            
            $violacoes = $postagem->verificarViolacoesPalavrasProibidas();
            
            if (!empty($violacoes)) {
                $palavras = [];
                foreach ($violacoes as $interesseViolacoes) {
                    foreach ($interesseViolacoes as $violacao) {
                        $palavras[] = $violacao->palavra;
                    }
                }
                
                $postagem->bloquearAutomaticamente(
                    "Palavras proibidas detectadas: " . implode(', ', array_slice(array_unique($palavras), 0, 5))
                );
            }
        });

        static::updated(function ($postagem) {
            if ($postagem->isDirty('texto_postagem')) {
                $postagem->tendencias()->detach();
                $postagem->processarHashtags($postagem->texto_postagem);
                $postagem->sugerirInteressesAutomaticos();
            }
        });

        static::deleting(function ($postagem) {
            foreach ($postagem->interesses as $interesse) {
                $interesse->atualizarContadores();
            }
            
            $tendencias = $postagem->tendencias()->get();
            $postagem->tendencias()->detach();
            
            foreach ($tendencias as $tendencia) {
                if ($tendencia->postagens()->count() === 0) {
                    $tendencia->delete();
                }
            }
        });
    }
}