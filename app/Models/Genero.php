<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = "tb_genero";

    protected $fillable = [
        'titulo'
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'genero');
    }
}
