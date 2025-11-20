<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfracaoSistema extends Model
{
    protected $table = 'infracoes_sistema';

    protected $fillable = [
        'usuario_id', 'tipo', 'descricao', 'conteudo_original',
        'postagem_id', 'interesse_id', 'reportado_por', 
        'moderador_id', 'verificada', 'verificada_em'
    ];

    protected $casts = [
        'verificada' => 'boolean',
        'verificada_em' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function postagem(): BelongsTo
    {
        return $this->belongsTo(Postagem::class, 'postagem_id');
    }

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class, 'interesse_id');
    }

    public function reportadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'reportado_por');
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'moderador_id');
    }

    public function marcarComoVerificada($moderadorId): void
    {
        $this->update([
            'verificada' => true,
            'moderador_id' => $moderadorId,
            'verificada_em' => now()
        ]);
    }

    public function scopePendentes($query)
    {
        return $query->where('verificada', false);
    }

    public function scopeVerificadas($query)
    {
        return $query->where('verificada', true);
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeRecentes($query, $limite = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limite);
    }
}