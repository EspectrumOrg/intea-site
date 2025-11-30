<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsavel extends Model
{
    use HasFactory;

    protected $table = 'tb_responsavel';

    protected $fillable = [
        'usuario_id',
        'created_at',
        'updated_at'
    ];

    public function usuarioModel()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function autistas()
    {
        return $this->belongsToMany(
            Autista::class,
            'tb_autista_responsavel',
            'responsavel_id',
            'autista_id'
        );
    }
}
