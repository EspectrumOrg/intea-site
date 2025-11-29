<h2>Olá, {{ $banimentoConfirmacao->usuario->apelido }}</h2>

@php
switch ($banimentoConfirmacao->infracao) {
case 'odio':
$infracao_cometida = 'Ódio ou Discriminação';
break;

case 'abuso_e_assedio':
$infracao_cometida = 'Abuso ou Assédio';
break;

case 'discurso_de_odio':
$infracao_cometida = 'Ameaças ou Violência';
break;

case 'seguranca_infantil':
$infracao_cometida = 'Segurança Infantil';
break;

case 'privacidade':
$infracao_cometida = 'Privacidade';
break;

case 'comportamentos_ilegais_e_regulamentados':
$infracao_cometida = 'Atividades Ilegais';
break;

case 'spam':
$infracao_cometida = 'Spam';
break;

case 'suicidio_ou_automutilacao':
$infracao_cometida = 'Risco à Integridade Pessoal';
break;

case 'personificacao':
$infracao_cometida = 'Falsa Identidade';
break;

case 'entidades_violentas_e_odiosas':
$infracao_cometida = 'Grupos Extremistas';
break;

default:
$infracao_cometida = $banimentoConfirmacao->infracao; // mantém valor original caso venha algo inesperado
break;
}

@endphp

<p>
    A conta de <strong>{{ $banimentoConfirmacao->usuarioBanido->user }}</strong> foi banida do <strong>Intea</strong> por
    <strong>{{ $infracao_cometida }}</strong>.
</p>

<hr>
<p>Pedimos desculpas por qualquer inconveniente que possa ter ocorrido.</p>

<p>Atenciosamente,<br><strong>Equipe Espectrum</strong></p>