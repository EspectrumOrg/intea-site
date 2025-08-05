<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuidadorModel extends Model
{
    use HasFactory;
    protected $table ='tb_responsavel';

    public $fillable = ['id','usuario_id', 'cipteiaAutista','created_at','updated_at'];
    //public $timestamps=false;
      public function usuarioModel()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
