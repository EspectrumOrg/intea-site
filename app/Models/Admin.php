<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = "tb_admin";

    protected $fillable = [
        'usuario_id',
        'created_at',
        'updated_at'
    ];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
