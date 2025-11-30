<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autista extends Model
{
    use HasFactory;

    protected $table = "tb_autista";

    protected $fillable = [
        'usuario_id',
        'cipteia_autista',
        'rg_autista',
        'status_cipteia_autista',
        'created_at',
        'updated_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function responsaveis()
    {
        return $this->belongsToMany(
            Responsavel::class,
            'tb_autista_responsavel',
            'autista_id',
            'responsavel_id'
        );
    }
}
