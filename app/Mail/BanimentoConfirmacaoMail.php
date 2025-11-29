<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BanimentoConfirmacao;

class BanimentoConfirmacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public BanimentoConfirmacao $banimentoConfirmacao;

    public function __construct(BanimentoConfirmacao $banimentoConfirmacao)
    {
        $this->banimentoConfirmacao = $banimentoConfirmacao;
    }

    public function build()
    {
        return $this->subject('Conta de usuario banida')
            ->view('components.mail.banimento-confirmacao')
            ->with(['banimentoConfirmacao' => $this->banimentoConfirmacao]);
    }
}
