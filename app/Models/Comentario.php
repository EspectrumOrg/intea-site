<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'tb_comentario';

    protected $fillable = [
        'id_postagem',
        'id_comentario_pai',
        'id_usuario',
        'comentario',
    ];

    public function usuario () 
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function comentarioPai ()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario_pai', 'id');
    }

    public function postagem ()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function image ()
    {
        return $this->hasOne(ImagemComentario::class, 'id_comentario');
    }

    public function curtidas_comentario ()
    {
        return $this->hasMany(CurtidaComentario::class);
    }

    public function respostas ()
    {
        return $this->hasMany(Comentario::class, 'id_comentario_pai', 'id');
    }
}
