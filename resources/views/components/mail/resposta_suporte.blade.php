<h2>Olá, {{ $resposta->destinatario }}</h2>

<p>Em resposta a seu contato em {{ \Carbon\Carbon::parse($resposta->data_contato)->format('d/m/Y H:i') }}.</p>

<hr>

<h3><strong>Assunto: </strong>{{ $resposta->assunto }}</h3>
<p><strong>Mensagem: </strong>{{ $resposta->mensagem }}</p>

<hr>
<p><strong>Resposta: </strong>{{ $resposta->resposta }}</p>
<hr>

<p>Se ainda restar alguma dúvida, basta responder este e-mail para continuarmos ajudando.</p>

<p>Nos contate caso você tenha outra reclamação/sugestão.</p>

<p>Mensagem para <strong>Equipe Espectrum</strong></p>