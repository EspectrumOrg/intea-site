<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespostaSuporte extends Model
{
    use HasFactory;

    public $table = 'tb_resposta_suporte';

    public $fillable = [
        'usuario_id',
        'destinatario',
        'assunto',
        'mensagem',
        'data_contato',
        'resposta',
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}