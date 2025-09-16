<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagemComentarioPostagem extends Model
{
    use HasFactory;

    protected $table = "tb_imagem_comentario_postagem";

    protected $fillable = [
        'id_comentario',
        'caminho_postagem',
    ];

    public function comentarioPostagem()
    {
        return $this->belongsTo(ComentarioPostagem::class, 'id_comentario');
    }
}
