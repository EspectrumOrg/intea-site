<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\RespostaSuporte;

class RespostaSuporteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resposta;

    public function __construct(RespostaSuporte $resposta)
    {
        $this->resposta = $resposta;
    }

    public function build()
    {
        return $this->subject($this->resposta->assunto)
            ->view('components.mail.resposta_suporte');
    }
}