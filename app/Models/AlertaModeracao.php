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
        'expiracao' => 'datetime',
        'ativo' => 'boolean'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class);
    }

    public function postagem(): BelongsTo
    {
        return $this->belongsTo(Postagem::class);
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'moderador_id');
    }

    public function estaExpirado(): bool
    {
        return $this->expiracao && $this->expiracao->isPast();
    }

    public function expirar(): void
    {
        $this->update(['ativo' => false]);
    }
}