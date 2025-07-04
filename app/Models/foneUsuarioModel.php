<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class foneUsuarioModel extends Model
{
    use HasFactory;

    protected $table='tb_foneusuario';
    public $fillable=[
    'id','idusuario','numerousuario'
    ];
}
