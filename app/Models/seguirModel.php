<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class seguirModel extends Model
{
    use HasFactory;
    protected $table = 'tb_seguir';  // Nome da tabela

    protected $fillable = [
        'segue_id',
        'seguindo_id',
    ];

    // Se quiser pode definir os relacionamentos também (opcional)

    public function seguidor()
    {
        return $this->belongsTo(User::class, 'segue_id');
    }

    public function seguido()
    {
        return $this->belongsTo(User::class, 'seguindo_id');
    }
}

