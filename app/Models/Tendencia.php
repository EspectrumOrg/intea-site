<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relação muitos-para-muitos com postagens
    public function postagens()
    {
        return $this->belongsToMany(Postagem::class, 'tb_tendencia_postagem', 'tendencia_id', 'postagem_id')
                    ->withTimestamps();
    }

    // Scope para tendências populares
    public function scopePopulares($query, $limit = 10)
    {
        return $query->orderBy('contador_uso', 'desc')
                    ->orderBy('ultimo_uso', 'desc')
                    ->take($limit);
    }

    // Método para criar slug da hashtag
    public static function criarSlug($hashtag)
    {
        return strtolower(str_replace('#', '', $hashtag));
    }
}