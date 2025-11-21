<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Banimento;
use App\Models\BanimentoReconsideracao;

class BanimentoReconsideracaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public BanimentoReconsideracao $banimentoReconsiderao;

    public function __construct(BanimentoReconsideracao $banimentoReconsiderao)
    {
        $this->banimentoReconsiderao = $banimentoReconsiderao;
    }

    public function build()
    {
        return $this->subject('Sua conta foi desbanida')
                    ->view('components.mail.banimento-reconsideracao')
                    ->with(['banimentoReconsiderao' => $this->banimentoReconsiderao]);
    }
}
