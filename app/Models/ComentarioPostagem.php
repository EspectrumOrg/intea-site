<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioPostagem extends Model
{
    use HasFactory;

    protected $table = 'tb_comentario_postagem';

    protected $fillable = [
        'id_postagem',
        'id_usuario',
        'comentario',
    ];

    public function usuario () 
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function postagem ()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function image ()
    {
        return $this->hasOne(ImagemComentarioPostagem::class, 'id_comentario');
    }

    public function curtidas_comentario ()
    {
        return $this->hasMany(CurtidaComentario::class);
    }
}
