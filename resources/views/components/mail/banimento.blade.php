<h2>Olá, {{ $banimento->usuario->apelido }}</h2>

@php
    switch ($banimento->infracao) {
        case 'conteudo_explicito':
            $infracao_cometida = 'conteúdo explícito';
            break;
        case 'desinformacao':
            $infracao_cometida = 'desinformação';
            break;
        case 'discurso_de_odio':
            $infracao_cometida = 'discurso de ódio';
            break;
        case 'farsa':
            $infracao_cometida = 'farsa';
            break;
        case 'golpe':
            $infracao_cometida = 'golpe';
            break;
        case 'spam':
            $infracao_cometida = 'spam';
            break;
        default:
            $infracao_cometida = 'má fé';
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