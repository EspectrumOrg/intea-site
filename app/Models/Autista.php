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
        'id_cuidador',
        'created_at',
        'updated_at'
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
