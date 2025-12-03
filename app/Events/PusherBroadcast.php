<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PusherBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $remetente_id;
    public $foto;
    public $hora;
    public $imagem; 

public function __construct(
        ?string $message = null, // Mude para nullable
        int $remetente_id,
        ?string $foto = null,
        ?string $hora = null,
        ?string $imagem = null
    ) {
        $this->message = $message;
        $this->remetente_id = $remetente_id;
        $this->foto = $foto;
        $this->hora = $hora;
        $this->imagem = $imagem;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('public');
    }

    public function broadcastAs(): string
    {
        return 'chat';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'remetente_id' => $this->remetente_id,
            'foto' => $this->foto,
            'imagem' => $this->imagem, 
            'hora' => $this->hora, 
        ];
    }
}
