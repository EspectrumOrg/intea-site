<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
  protected $table = 'tb_usuario';
  public $fillable = [
    'nome',
    'user',
    'apelido',
    'email',
    'senha',
    'cpf',
    'genero',
    'data_nascimento',
    'cep',
    'logradouro',
    'endereco',
    'rua',
    'bairro',
    'numero',
    'cidade',
    'estado',
    'complemento',
    'tipo_usuario',
    'status_conta',
    'created_at',
    'updated_at'
  ];



  public function admin()
  {
    return $this->hasOne(Admin::class, 'usuario_id');
  }

  public function autista()
  {
    return $this->hasOne(Autista::class, 'usuario_id');
  }

  public function comunidade()
  {
    return $this->hasOne(Comunidade::class, 'usuario_id');
  }

  public function profissionalsaude()
  {
    return $this->hasOne(ProfissionalSaude::class, 'usuario_id');
  }

  public function responsavel()
  {
    return $this->hasOne(Responsavel::class, 'usuario_id');
  }



  public function telefones()
  {
    return $this->hasMany(FoneUsuario::class, 'usuario_id');
  }
  use HasFactory;
}