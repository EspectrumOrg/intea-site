<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postagem extends Model
{
    use HasFactory;

    protected $table = "tb_postagem";

    protected $fillable = [
        'titulo_postagem',
        'texto_postagem',
    ];

    public function usuario () 
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comentarios () 
    {
        return $this->hasMany(ComentarioPostagem::class);
    }

    public function imagens ()
    {
        return $this->hasMany(ImagemPostagem::class, 'id_postagem');
    }

    public function curtidas ()
    {
        return $this->hasMany(CurtidaPostagem::class);
    }
}
