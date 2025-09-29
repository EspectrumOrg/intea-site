<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GruposModel extends Model
{
    use HasFactory;
    protected $table='tb_gruposdacomunidade';
    public $fillable=['id','idLider','nomeGrupo','descGrupo','imagemGrupo'];



public function usuarios()
{
    return $this->belongsToMany(
        Usuario::class,
        'tb_gruposdacomunidade_usuarios',
        'idGruposComunidade',
        'idusuario'
    );
}

}
