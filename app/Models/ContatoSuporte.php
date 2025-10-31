<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContatoSuporte extends Model
{
    use HasFactory;

    protected $table = 'tb_contato_suporte';

    protected $fillable = [
        'email',
        'name',
        'assunto',
        'mensagem',
    ];
}
