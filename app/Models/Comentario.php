<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'tb_comentario';

    protected $fillable = [
        'id_postagem',
        'id_comentario_pai',
        'id_usuario',
        'comentario',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function comentarioPai()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario_pai', 'id');
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function image()
    {
        return $this->hasOne(ImagemComentario::class, 'id_comentario', 'id');
    }

    public function curtidas_comentario()
    {
        return $this->hasMany(Curtida::class, 'id_comentario', 'id');
    }


    public function respostas()
    {
        return $this->hasMany(Comentario::class, 'id_comentario_pai', 'id');
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'id_comentario');
    }

    public function scopeApenasDeUsuariosAtivos($query)
    {
        return $query->whereHas('usuario', function ($q) {
            $q->where('status_conta', 1);
        });
    }

    public function getCurtidasCountAttribute()
    {
        return $this->curtidas_comentario()
            ->apenasDeUsuariosAtivos()
            ->count();
    }

    public function getComentariosCountAttribute()
    {
        return $this->respostas()
            ->apenasDeUsuariosAtivos()
            ->count();
    }

    public function getCurtidasUsuarioAttribute()
    {
        return $this->curtidas_comentario()->where('id_usuario', auth()->id())->exists();
    }
}
