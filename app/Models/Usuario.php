<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'cpf',
        'genero',
        'data_nascimento',
        'cep',
        'logradouro',
        'endereco',
        'rua',
        'bairro',
        'numero',
        'cidade',
        'estado',
        'complemento',
        'tipo_usuario'
    ];

    public function admin() {
        return $this->hasOne(Admin::class);
    }

    public function autista() {
        return $this->hasOne(Autista::class);
    }

    public function comunidade() {
        return $this->hasOne(Comunidade::class);
    }

    public function profissionalSaude() {
        return $this->hasOne(ProfissionalSaude::class);
    }

    public function responsavel() {
        return $this->hasOne(Responsavel::class);
    }
}
