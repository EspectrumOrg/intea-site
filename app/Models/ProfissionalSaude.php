<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfissionalSaude extends Model
{
    use HasFactory;

    protected $table = "tb_profissional_saude";

    protected $fillable = [
        'usuario_id',
        'tipo_registro',
        'registro_profissional',
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
