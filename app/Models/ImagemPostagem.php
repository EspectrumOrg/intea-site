<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagemPostagem extends Model
{
    use HasFactory;

    protected $table = "tb_imagem_postagem";

    protected $fillable = [
        'caminho_imagem'
    ];

    public function postagem () {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }
}
