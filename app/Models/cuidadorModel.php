<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuidadorModel extends Model
{
    use HasFactory;
    protected $table ='tb_cuidador';

    public $fillable = ['id','idusuario', 'cipteiaAutista','created_at','updated_at'];
    //public $timestamps=false;
      public function usuarioModel()
    {
        return $this->belongsTo(usuarioModel::class, 'idusuario');
    }
}
