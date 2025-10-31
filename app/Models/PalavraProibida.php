<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalavraProibida extends Model
{
    protected $table = 'palavras_proibidas';

    protected $fillable = [
        'interesse_id', 'palavra', 'tipo', 'ativo', 'adicionado_por', 'motivo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function interesse(): BelongsTo
    {
        return $this->belongsTo(Interesse::class);
    }

    public function adicionadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'adicionado_por');
    }

    public static function verificarTexto($texto, $interesseId): array
    {
        $palavrasProibidas = self::where('interesse_id', $interesseId)
                                ->where('ativo', true)
                                ->get();

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
}