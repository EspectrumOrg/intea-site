<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autista extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
    ];

    public function Usuario() {
        return $this->belongsTo(Usuario::class);
    }
}
