<h2>Olá, {{ $banimento->usuario->apelido }}</h2>

@php
switch ($banimento->infracao) {
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
$infracao_cometida = $banimento->infracao; // mantém valor original caso venha algo inesperado
break;
}

@endphp

<p>
    Sua conta <strong>{{ $banimento->usuario->user }}</strong> foi banida do <strong>Intea</strong> por
    <strong>{{ $infracao_cometida }}</strong>.
</p>

<p><strong>Motivo:</strong> {{ $banimento->motivo }}</p>

@if($banimento->id_postagem && $banimento->postagem)
<hr>
<h4>Conteúdo da postagem:</h4>
<p><strong>Texto:</strong> {{ $banimento->postagem->texto_postagem }}</p>
<p><strong>Data:</strong> {{ $banimento->postagem->created_at->format('d/m/Y H:i') }}</p>
@endif

@if($banimento->id_comentario && $banimento->comentario)
<hr>
<h4>Conteúdo do comentário:</h4>
<p><strong>Comentário:</strong> {{ $banimento->comentario->comentario }}</p>
<p><strong>Data:</strong> {{ $banimento->comentario->created_at->format('d/m/Y H:i') }}</p>

@if ($banimento->comentario->comentarioPai)
<h5>Resposta ao comentário:</h5>
<p><strong>Comentário relacionado:</strong> {{ $banimento->comentario->comentarioPai->comentario }}</p>
<p><strong>De:</strong> {{ $banimento->comentario->comentarioPai->usuario->user }}</p>
<p><strong>Data:</strong> {{ $banimento->comentario->comentarioPai->created_at->format('d/m/Y H:i') }}</p>
@endif
@endif

<hr>
<p>Nos contate caso você ache que ouve um engano na sua expulsão.</p>

<p>Atenciosamente,<br><strong>Equipe Espectrum</strong></p>