<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPostagem extends Model
{
    use HasFactory;

    protected $table = "tb_video_postagem";

    protected $fillable = [
        'id_postagem',
        'caminho_video',
    ];

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }
}
