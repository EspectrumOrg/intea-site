<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use HasFactory;

    protected $table = "tb_denuncia";

    protected $fillable = [
        'id_usuario_denunciante',
        'id_usuario_denunciado',
        'id_postagem',
        'id_comentario',
        'motivo_denuncia',
        'texto_denuncia',
        'status_denuncia',
    ];

    public function usuarioDenunciante() // Usuário que fez a denúncia
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_denunciante');
    }
    public function usuarioDenunciado() // Usuário denunciado
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_denunciante');
    }

    public function postagem() // Postagem denunciada
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function comentario() // Comentário denunciada
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }
}
