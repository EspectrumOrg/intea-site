<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tendencia extends Model
{
    protected $table = 'tb_tendencias';
    
    protected $fillable = [
        'hashtag',
        'slug', 
        'contador_uso',
        'ultimo_uso'
    ];

    protected $casts = [
        'ultimo_uso' => 'datetime'
    ];

    /**
     * Relação muitos-para-muitos com postagens
     * CORRIGIDO: Usando o nome correto da tabela pivô
     */
    public function postagens(): BelongsToMany
    {
        return $this->belongsToMany(Postagem::class, 'tb_tendencia_postagem', 'tendencia_id', 'postagem_id')
                    ->withTimestamps();
    }

    /**
     * Scope para tendências populares
     */
    public function scopePopulares($query, $limit = 10)
    {
        return $query->orderBy('contador_uso', 'desc')
                    ->orderBy('ultimo_uso', 'desc')
                    ->take($limit);
    }

    /**
     * Método para criar slug da hashtag
     */
    public static function criarSlug($hashtag)
    {
        return strtolower(str_replace('#', '', $hashtag));
    }

    /**
     * Verificar se a tendência tem postagens
     */
    public function temPostagens(): bool
    {
        return $this->postagens()->exists();
    }

    /**
     * Obter contagem real de postagens
     */
    public function obterContagemPostagens(): int
    {
        return $this->postagens()->count();
    }
}