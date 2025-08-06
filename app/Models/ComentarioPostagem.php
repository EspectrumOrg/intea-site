<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioPostagem extends Model
{
    use HasFactory;

    protected $table = 'tb_comentario_postagem';

    protected $fillable = [
        'comentario'
    ];

    public function usuario () 
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function postagem ()
    {
        return $this->belongsTo(Postagem::class, 'postagem_id');
    }

    public function curtidas_comentario ()
    {
        return $this->hasMany(CurtidaComentario::class);
    }
}
