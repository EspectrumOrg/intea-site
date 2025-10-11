<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários Seguindo</title>

</head>
<body>

<h2>Usuários que você segue</h2>
@if($usuariosSeguindo->isEmpty())
    <p>Você ainda não segue ninguém.</p>
@else
    <ul>
        @foreach ($usuariosSeguindo as $usuario)
            <li>
                <img src="{{ $usuario->foto ? asset('storage/' . $usuario->foto) : asset('storage/default.jpg') }}" width="100px" alt="{{ $usuario->nome }}">
                <div>
                    <strong>{{ $usuario->nome }}</strong> ({{ $usuario->user }}) — {{ $usuario->email }}
                </div>
                <a href="{{ route('chat.usuario', $usuario->id) }}">
                    <button>Abrir Chat</button>
                </a>
            </li>
        @endforeach
    </ul>
@endif




<h2>Minhas Conversas</h2>
@if($conversas->isEmpty())
    <p>Você ainda não possui conversas.</p>
@else
    <ul>
        @foreach ($conversas as $conversa)
            @php
                $outroUsuarioId = $conversa->usuario1_id == $usuarioLogado ? $conversa->usuario2_id : $conversa->usuario1_id;
                $outroUsuario = \App\Models\Usuario::find($outroUsuarioId);
            @endphp
            <li>
                <img src="{{ $outroUsuario->foto ? asset('storage/' . $outroUsuario->foto) : asset('storage/default.jpg') }}" width="100px" alt="{{ $outroUsuario->nome }}">
                <div>
                    <strong>{{ $outroUsuario->nome }}</strong> ({{ $outroUsuario->user }})
                    <br>
                    Última atualização: {{ $conversa->updated_at->format('d/m/Y H:i') }}
                </div>
                <a href="{{ route('chat.usuario', $outroUsuario->id) }}">
                    <br>
                    <br>
                    <button>Abrir Chat</button>

                    <br>
                    <br>
                </a>
            </li>
        @endforeach
    </ul>
@endif
</body>
</html>