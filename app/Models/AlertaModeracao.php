<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaModeracao extends Model
{
    protected $table = 'alertas_moderacao';

    protected $fillable = [
        'usuario_id', 'interesse_id', 'postagem_id', 'motivo',
        'gravidade', 'moderador_id', 'expiracao', 'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'expiracao' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class, 'interesse_id');
    }

    public function postagem(): BelongsTo
    {
        return $this->belongsTo(Postagem::class, 'postagem_id');
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'moderador_id');
    }

    public function expirar(): void
    {
        $this->update(['ativo' => false]);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)
                    ->where(function($q) {
                        $q->whereNull('expiracao')
                          ->orWhere('expiracao', '>', now());
                    });
    }
}