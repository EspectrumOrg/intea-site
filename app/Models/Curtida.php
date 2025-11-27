<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curtida extends Model
{
    use HasFactory;

    protected $table = 'tb_curtida';

    protected $fillable = [
        'id_postagem',
        'id_comentario',
        'id_usuario',
    ];

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function scopeApenasDeUsuariosAtivos($query)
    {
        return $query->whereHas('usuario', function ($q) {
            $q->where('status_conta', 1);
        });
    }
}
