<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalavraProibidaGlobal extends Model
{
    protected $table = 'palavras_proibidas_globais';

    protected $fillable = [
        'palavra', 'tipo', 'motivo', 'adicionado_por', 'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function adicionadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'adicionado_por');
    }

    public static function verificarTexto($texto): array
    {
        $palavrasProibidas = self::where('ativo', true)->get();
        $violacoes = [];

        foreach ($palavrasProibidas as $palavra) {
            if ($palavra->tipo === 'exata') {
                if (preg_match("/\b" . preg_quote($palavra->palavra, '/') . "\b/i", $texto)) {
                    $violacoes[] = $palavra;
                }
            } else {
                if (stripos($texto, $palavra->palavra) !== false) {
                    $violacoes[] = $palavra;
                }
            }
        }

        return $violacoes;
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}