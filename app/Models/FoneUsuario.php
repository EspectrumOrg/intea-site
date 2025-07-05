<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoneUsuario extends Model
{
    use HasFactory;

    protected $table='tb_fone_usuario';
    
    public $fillable=[
    'id',
    'usuario_id',
    'numero_telefone'
    ];

    public function Usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
