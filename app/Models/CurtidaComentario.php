<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurtidaComentario extends Model
{
    use HasFactory;

    protected $table = 'tb_curtida_comentario';

    protected $fillable = [
        'id_comentario',
        'id_usuario'
    ];

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
