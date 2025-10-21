<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensagemPrivada extends Model
{
    use HasFactory;

    protected $table = 'tb_mensagensprivadas';
    protected $fillable = [
        'conversa_id',
        'remetente_id',
        'texto',
    ];
    public function conversa()
    {
        return $this->belongsTo(ChatPrivado::class, 'conversa_id');
    }

    /**
     * Relacionamento com o remetente (usuário que enviou a mensagem).
     */
    public function remetente()
    {
        return $this->belongsTo(Usuario::class, 'remetente_id');
    }
}
