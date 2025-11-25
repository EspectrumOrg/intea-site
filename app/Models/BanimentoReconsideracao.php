<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanimentoReconsideracao extends Model
{
    use HasFactory;

    protected $table = 'tb_banimento_reconsideracao';

    protected $fillable = [
        'id_usuario',
        'id_admin',
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
}
