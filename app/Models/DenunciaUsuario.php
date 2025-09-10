<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaUsuario extends Model
{
    use HasFactory;

    protected $table = "tb_denuncia_usuario";

    protected $fillable = [
        'id_usuario_denunciado',
        'id_usuario_denunciante',
        'motivo_denuncia',
        'texto_denuncia',
    ];

    public function usuarioDenunciado()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function usuarioDenunciante()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}