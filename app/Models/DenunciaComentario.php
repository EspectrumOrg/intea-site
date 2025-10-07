<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaComentario extends Model
{
    use HasFactory;

    protected $table = "tb_denuncia_comentario";

    protected $fillable = [
        'id_comentario',
        'id_usuario',
        'motivo_denuncia',
        'texto_denuncia',
        'status_denuncia',
    ];

    public function comentario() //comentário denunciado
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }

    public function usuario() //usuário que fez a denúncia
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
