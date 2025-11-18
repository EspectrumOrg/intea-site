<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações</title>

    {{-- Bootstrap (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/estilo-chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/style.css') }}">




    <style>
        body {
            background-color: #f9fafb;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 700px;
        }
        .list-group-item {
            border-radius: 10px;
            margin-bottom: 8px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
            font-weight: bold;
        }
        .btn {
            min-width: 90px;
        }
    </style>
</head>
<body>

 <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar  -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>



<div class="container">
    <h2>Solicitações de Seguir</h2>

    {{-- Mensagens de retorno --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

{{-- Lista de notificações --}}
@if($notificacoes->isEmpty())
    <p class="text-center text-muted">Você não tem notificações.</p>
@else
    <ul class="list-group">
        @foreach($notificacoes as $notificacao)
            <li class="list-group-item d-flex justify-content-between align-items-center">

                {{-- LINK PARA A CONTA DO USUÁRIO --}}
                @php
                    $linkConta = route('conta.index', $notificacao->solicitante_id);
                    $nomeUsuario = $notificacao->solicitante->user ?? 'Usuário desconhecido';
                @endphp

                {{-- TIPOS DE NOTIFICAÇÃO --}}
                @if($notificacao->tipo === 'seguir')
                    <div>
                        <a href="{{ $linkConta }}" style="font-weight: bold; text-decoration: none;">
                            {{ $nomeUsuario }}
                        </a>
                        enviou uma solicitação para seguir você.
                    </div>

                    <div>
                        <form action="{{ route('notificacoes.aceitar', $notificacao->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Aceitar</button>
                        </form>

                        <form action="{{ route('notificacoes.recusar', $notificacao->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Recusar</button>
                        </form>
                    </div>

              @elseif($notificacao->tipo === 'seguindo_voce')
    <div>
        <a href="{{ $linkConta }}" style="font-weight: bold; text-decoration: none;">
            {{ $nomeUsuario }}
        </a>
        começou a seguir você.
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success">Novo seguidor</span>

        {{-- BOTÃO OK PARA REMOVER A NOTIFICAÇÃO --}}
        <form action="{{ route('notificacao.destroy', $notificacao->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-primary btn-sm">OK</button>
        </form>
    </div>
@endif

            </li>
        @endforeach
    </ul>
@endif
</div>

</body>
</html>
