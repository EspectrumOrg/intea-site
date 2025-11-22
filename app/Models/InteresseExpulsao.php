<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteresseExpulsao extends Model
{
    protected $table = 'interesse_expulsoes';

    protected $fillable = [
        'usuario_id', 'interesse_id', 'motivo', 'moderador_id',
        'permanente', 'expulso_ate'
    ];

    protected $casts = [
        'permanente' => 'boolean',
        'expulso_ate' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class, 'interesse_id');
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'moderador_id');
    }

    public function expirou(): bool
    {
        return !$this->permanente && $this->expulso_ate && $this->expulso_ate->isPast();
    }

    public function scopeAtivas($query)
    {
        return $query->where(function($q) {
            $q->where('permanente', true)
              ->orWhere('expulso_ate', '>', now());
        });
    }
}