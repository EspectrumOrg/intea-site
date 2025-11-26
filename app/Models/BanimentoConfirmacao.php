<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanimentoConfirmacao extends Model
{
    use HasFactory;

    protected $table = 'tb_banimento_confirmacao';

    protected $fillable = [
        'id_usuario',
        'id_usuario_banido',
        'id_admin',
        'infracao',
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function UsuarioBanido()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_banido');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
}
