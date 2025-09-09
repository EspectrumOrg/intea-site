<?php

namespace App\Models;

use App\Http\Controllers\PostagemController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurtidaPostagem extends Model
{
    use HasFactory;

    protected $table = 'tb_curtida_postagem';

    protected $fillable = [
        'id_postagem',
        'id_usuario',
    ];

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function usuario()
    {
        return $this->belongsTo(USuario::class, 'id_usuario');
    }
}
