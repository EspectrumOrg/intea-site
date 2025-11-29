<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Banimento;

class BanimentoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Banimento $banimento;

    public function __construct(Banimento $banimento)
    {
        $this->banimento = $banimento;
    }

    public function build()
    {
        return $this->subject('Sua conta foi banida')
            ->view('components.mail.banimento')
            ->with(['banimento' => $this->banimento]);
    }
}
