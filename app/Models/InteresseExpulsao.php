<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteresseExpulsao extends Model
{
    protected $table = 'interesse_expulsoes';

    protected $fillable = [
        'usuario_id', 'interesse_id', 'motivo',
        'moderador_id', 'expulso_ate', 'permanente'
    ];

    protected $casts = [
        'expulso_ate' => 'datetime',
        'permanente' => 'boolean'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class);
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'moderador_id');
    }

    public function estaAtivo(): bool
    {
        if ($this->permanente) {
            return true;
        }

        return $this->expulso_ate && $this->expulso_ate->isFuture();
    }

    public function ehTemporario(): bool
    {
        return !$this->permanente && $this->expulso_ate;
    }
}