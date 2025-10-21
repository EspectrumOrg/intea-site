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

public function __construct(string $message, int $remetente_id, ?string $foto = null)
{
    $this->message = $message;
    $this->remetente_id = $remetente_id;
    $this->foto = $foto; 
}

    public function broadcastOn(): Channel
    {
        return new Channel('public');
    }

    public function broadcastAs(): string
    {
        return 'chat';
    }
}
