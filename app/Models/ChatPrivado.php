<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPrivado extends Model
{
    use HasFactory;
   protected $table = 'tb_chatprivado';

    // Campos que podem ser preenchidos via create()
    protected $fillable = [
        'usuario1_id',
        'usuario2_id',
    ];

    /**
     * Relacionamento com mensagens privadas.
     * Cada conversa possui várias mensagens.
     */
    public function mensagens()
    {
        return $this->hasMany(MensagemPrivada::class, 'conversa_id');
    }

    /**
     * Relacionamento com o usuário 1.
     */
    public function usuario1()
    {
        return $this->belongsTo(Usuario::class, 'usuario1_id');
    }

    /**
     * Relacionamento com o usuário 2.
     */
    public function usuario2()
    {
        return $this->belongsTo(Usuario::class, 'usuario2_id');
    }
}
