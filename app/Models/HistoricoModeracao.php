<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoModeracao extends Model
{
    protected $table = 'historico_moderacao';

    protected $fillable = [
        'interesse_id',
        'usuario_id',
        'postagem_id',
        'acao',
        'motivo',
        'detalhes'
    ];

    protected $casts = [
        'detalhes' => 'array'
    ];

    public function interesse()
    {
        return $this->belongsTo(Interesse::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class);
    }

    public function moderador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}