<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banimento extends Model
{
    use HasFactory;

    protected $table = 'tb_banimento';

    protected $fillable = [
        'id_usuario',
        'id_admin',
        'infracao',
        'motivo',
        'id_postagem',
        'id_comentario',
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }
}