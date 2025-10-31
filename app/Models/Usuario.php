<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Banimento[] $banimentos
 * @method \Illuminate\Database\Eloquent\Relations\HasMany banimentos()
 */

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
    'onboarding_concluido',
    'onboarding_concluido_em',
    'created_at',
    'updated_at'
  ];

  protected $hidden = [
    'senha',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'onboarding_concluido' => 'boolean',
    'onboarding_concluido_em' => 'datetime'
  ];

  public function getAuthPassword()
  {
    return $this->senha;
  }

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


  public function banimentos(): HasMany
  {
    return $this->hasMany(Banimento::class, 'id_usuario');
  }

  public function genero()
  {
    return $this->belongsTo(Genero::class, 'genero');
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
          'seguindo_id',
          'segue_id'
      )->withTimestamps();
  }

  public function interesses()
  {
      return $this->belongsToMany(Interesse::class, 'interesse_usuario')
                  ->withPivot('notificacoes', 'seguindo_desde')
                  ->withTimestamps();
  }

  public function interessesComoModerador()
  {
      return $this->belongsToMany(Interesse::class, 'interesse_moderadores')
                  ->withPivot('cargo')
                  ->withTimestamps();
  }

  public function alertasModeracao()
  {
      return $this->hasMany(AlertaModeracao::class);
  }

  public function expulsoesInteresses()
  {
      return $this->hasMany(InteresseExpulsao::class);
  }

  public function postagensModeradas()
  {
      return $this->hasMany(Postagem::class, 'removida_por');
  }

  public function postagensNoInteresse($interesseId)
  {
      return $this->postagens()
                  ->whereHas('interesses', function($query) use ($interesseId) {
                      $query->where('interesses.id', $interesseId);
                  })
                  ->get();
  }

  public function seguirInteresse($interesseId, $notificacoes = true): void
  {
      $this->interesses()->attach($interesseId, [
          'notificacoes' => $notificacoes,
          'seguindo_desde' => now()
      ]);

      $interesse = Interesse::find($interesseId);
      $interesse->atualizarContadores();
  }

  public function deixarSeguirInteresse($interesseId): void
  {
      $this->interesses()->detach($interesseId);

      $interesse = Interesse::find($interesseId);
      $interesse->atualizarContadores();
  }

  public function segueInteresse($interesseId): bool
  {
      return $this->interesses()->where('interesses.id', $interesseId)->exists();
  }

  public function estaExpulsoDe($interesseId): bool
  {
      return $this->expulsoesInteresses()
                  ->where('interesse_id', $interesseId)
                  ->where(function($query) {
                      $query->where('permanente', true)
                            ->orWhere('expulso_ate', '>', now());
                  })
                  ->exists();
  }

  public function ehModeradorDe($interesseId): bool
  {
      return $this->interessesComoModerador()
                  ->where('interesses.id', $interesseId)
                  ->exists();
  }

  public function podeModerar($interesseId): bool
  {
      return $this->ehModeradorDe($interesseId) || $this->tipo_usuario === 'admin';
  }

  public function obterFeedInteresses($limite = 20)
  {
      $interessesIds = $this->interesses()->pluck('interesses.id');
      
      return Postagem::with(['usuario', 'imagens', 'interesses'])
                  ->whereHas('interesses', function($query) use ($interessesIds) {
                      $query->whereIn('interesses.id', $interessesIds);
                  })
                  ->where(function($query) {
                      $query->where('visibilidade_interesse', 'publico')
                            ->orWhere(function($q) {
                                $q->where('visibilidade_interesse', 'seguidores');
                            });
                  })
                  ->where('bloqueada_auto', false)
                  ->where('removida_manual', false)
                  ->orderBy('created_at', 'desc')
                  ->limit($limite)
                  ->get();
  }

  public function obterInteressesSugeridos($limite = 6)
  {
      return Interesse::ativos()
                  ->whereNotIn('id', $this->interesses()->pluck('interesses.id'))
                  ->populares($limite)
                  ->get();
  }

  public function obterEstatisticasModeracao()
  {
      return [
          'alertas_ativos' => $this->alertasModeracao()->where('ativo', true)->count(),
          'expulsoes' => $this->expulsoesInteresses()->count(),
          'postagens_moderadas' => $this->postagensModeradas()->count(),
      ];
  }

  public function onboardingConcluido(): bool
  {
      return $this->onboarding_concluido ?? false;
  }

  public function completarOnboarding()
  {
      $this->update([
          'onboarding_concluido' => true,
          'onboarding_concluido_em' => now()
      ]);
  }

  public function temInteresses(): bool
  {
      return $this->interesses()->exists();
  }

  public function obterIdsInteresses(): array
  {
      return $this->interesses()->pluck('interesses.id')->toArray();
  }

  public function sendPasswordResetNotification($token)
  {
      $this->notify(new ResetPasswordNotification($token));
  }
}
