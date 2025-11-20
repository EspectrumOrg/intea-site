<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenalidadeUsuario extends Model
{
    protected $table = 'penalidades_usuarios';

    protected $fillable = [
        'usuario_id', 'tipo', 'interesse_id', 'motivo',
        'peso', 'aplicado_por', 'expira_em', 'ativa'
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'expira_em' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class, 'interesse_id');
    }

    public function aplicadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aplicado_por');
    }

    public function expirar(): void
    {
        $this->update(['ativa' => false]);
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true)
                    ->where(function($q) {
                        $q->whereNull('expira_em')
                          ->orWhere('expira_em', '>', now());
                    });
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDoInteresse($query, $interesseId)
    {
        return $query->where('interesse_id', $interesseId);
    }

    public function scopeRecentes($query, $limite = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limite);
    }
}