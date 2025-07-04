<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuarioModel extends Model
{
protected $table = 'tb_usuario'; 
      public $fillable = [
        'nomeUsuario',
        'emailUsuario',
        'senhaUsuario',
        'cpfUsuario',
        'generoUsuario',
        'dataNascUsuario',
        'cepUsuario',
        'logradouroUsuario',
        'enderecoUsuario',
        'ruaUsuario',
        'bairroUsuario',
        'numeroUsuario',
        'cidadeUsuario',
        'estadoUsuario',
        'complementoUsuario',
        'created_at',
        'updated_at'
    ];
  public function cuidador()
    {
        return $this->hasOne(cuidadorModel::class, 'idusuario');
    }


    public function telefones()
    {
        return $this->hasMany(foneUsuarioModel::class, 'idusuario');
    }
    use HasFactory;
}
