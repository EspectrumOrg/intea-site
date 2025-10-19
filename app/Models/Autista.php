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
        'responsavel_id',
        'created_at',
        'updated_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
    public function responsavel()
{
    return $this->belongsTo(Responsavel::class, 'responsavel_id');
}
}
