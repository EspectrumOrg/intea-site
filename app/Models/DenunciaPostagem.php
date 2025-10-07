<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaPostagem extends Model
{
    use HasFactory;

    protected $table = "tb_denuncia_postagem";

    protected $fillable = [
        'id_postagem',
        'id_usuario',
        'motivo_denuncia',
        'texto_denuncia',
        'status_denuncia',
    ];

    public function postagem() //postagem denunciada
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    public function usuario() //usuário que fez a denúncia
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
