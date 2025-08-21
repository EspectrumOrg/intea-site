<?php

namespace App\Models;

use App\Http\Controllers\PostagemController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurtidaPostagem extends Model
{
    use HasFactory;

    protected $table = 'tb_curtida';

    protected $fillable = [
        'id_postagem',
        'usuario',
    ];

    public function postagem () 
    {
        return $this->belongsTo(PostagemController::class);
    }
}
