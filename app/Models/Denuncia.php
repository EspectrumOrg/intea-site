<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use HasFactory;

    protected $table = "tb_denuncia_postagem";

    protected $fillable = [
        'id_postagem',
        'id_usuario',
        'motivo_denuncia',
        'texto_denuncia',
    ];

    public function postagem()
    {
        return $this->hasOne(Postagem::class, 'id_postagem');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_usuario');
    }
}
