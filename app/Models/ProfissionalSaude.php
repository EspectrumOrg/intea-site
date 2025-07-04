<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfissionalSaude extends Model
{
    use HasFactory;

    protected $table = "tb_profissionalsaude";

    protected $fillable = [
        'usuario_id',
    ];

    public function Usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
