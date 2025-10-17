<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagemComentario extends Model
{
    use HasFactory;

    protected $table = "tb_imagem_comentario";

    protected $fillable = [
        'id_comentario',
        'caminho_imagem',
    ];

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }
}
