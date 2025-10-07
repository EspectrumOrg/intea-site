<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postagem extends Model
{
    use HasFactory;

    protected $table = "tb_postagem";

    protected $fillable = [
        'usuario_id',
        'texto_postagem',
    ];

    protected $appends = [
        'curtidas_count',
        'comentario_count',
        'curtidas_usuario',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_postagem');
    }

    public function imagens()
    {
        return $this->hasMany(ImagemPostagem::class, 'id_postagem');
    }

    public function curtidas()
    {
        return $this->hasMany(CurtidaPostagem::class, 'id_postagem');
    }

    public function denuncias()
    {
        return $this->hasMany(DenunciaPostagem::class, 'id_postagem');
    }

    public function getCurtidasCountAttribute()
    {
        return $this->curtidas()->count();
    }

    public function getComentariosCountAttribute()
    {
        return $this->comentarios()->count();
    }

    public function getCurtidasUsuarioAttribute()
    {
        return $this->curtidas()->where('id_usuario', auth()->id())->exists();
    }
}
