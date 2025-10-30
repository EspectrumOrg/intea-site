<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification; // coloque esse use lá no topo, junto dos outros


class Usuario extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

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
    'foto',
    'descricao',
    'visibilidade',
    'tipo_usuario',
    'status_conta',
    'tema_preferencia',
    'tema_interface',
    'created_at',
    'updated_at'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'senha',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  // Aqui está o método necessário para o Auth::attempt funcionar com o campo "senha"
  public function getAuthPassword()
  {
    return $this->senha;
  }


  // Relacionamentos
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

  public function postagens()
  {
    return $this->hasMany(Postagem::class, 'usuario_id');
  }

    public function comentarios()
  {
    return $this->hasMany(Comentario::class, 'id_usuario');
  }

  public function getPostagensCountAttribute()
  {
    return $this->postagens()->count();
  }

  public function denuncias()
  {
    return $this->hasMany(Denuncia::class, 'id_usuario_denunciante');
  }

  public function genero()
  {
    return $this->belongsTo(Genero::class, 'genero'); // a chave estrangeira é 'genero'
  }
  public function seguindo()
{
    return $this->belongsToMany(
        Usuario::class,     
        'tb_seguir',         
        'segue_id',          
        'seguindo_id'       
    )->withTimestamps();
}


public function grupos()
{
    return $this->belongsToMany(
        GruposModel::class,
        'tb_gruposdacomunidade_usuarios',
        'idusuario',
        'idGruposComunidade'
    );
}
  public function seguidores()
  {
      return $this->belongsToMany(
          self::class,
          'tb_seguir',
          'seguindo_id',    // Foreign key do usuário atual na tabela follows (quem é seguido)
          'segue_id'        // Foreign key do usuário que está seguindo
      )->withTimestamps();
  }
  public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token));
}
}
