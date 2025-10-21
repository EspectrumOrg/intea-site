<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log; // importar no topo
use Illuminate\Support\Facades\Session;

class ResetPasswordNotification extends Notification
{
    public $token;

    /**
     * Cria uma nova instância da notificação.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Define os canais de notificação (obrigatório).
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Define a mensagem enviada por e-mail.
     */
   public function toMail($notifiable)
{
    // Log para ver no storage/logs/laravel.log
    Log::info('Disparando e-mail de reset para: ' . $notifiable->email);

    $url = url(route('password.reset', [
        'token' => $this->token,
        'email' => $notifiable->getEmailForPasswordReset(),
    ], false));

    return (new MailMessage)
        ->greeting('Olá!')
        ->subject('Redefinição de Senha')
        ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
        ->action('Redefinir Senha', $url)
        ->line('Este link de redefinição de senha expirará em 60 minutos.')
        ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.')
        ->salutation('Atenciosamente, equipe Espectrum');
}
}
