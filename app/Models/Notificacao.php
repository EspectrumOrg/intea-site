<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;
    protected $table = 'tb_notificacoes';

    protected $fillable = [
        'solicitante_id',
        'alvo_id',
        'tipo',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function alvo()
    {
        return $this->belongsTo(Usuario::class, 'alvo_id');
    }
}
