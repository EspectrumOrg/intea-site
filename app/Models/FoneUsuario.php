<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoneUsuario extends Model
{
    use HasFactory;

    protected $table = 'tb_fone_usuario';
    
    protected $fillable = [
        'usuario_id',
        'numero_telefone',
        'tipo_telefone',
        'is_principal'
    ];

    protected $casts = [
        'is_principal' => 'boolean'
    ];

    // Relação com usuário
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Escopo para telefones principais
    public function scopePrincipal($query)
    {
        return $query->where('is_principal', true);
    }
}