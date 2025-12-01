<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações</title>

    {{-- Bootstrap (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/estilo-chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/style.css') }}">
    <!-- estilo do notificacao -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/notificacao/notificacao.css') }}">
    <!-- Postagem -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">
</head>

<body class="{{ auth()->user()->tema_preferencia === 'monocromatico' ? 'monochrome' : '' }}">

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
                        $fotoUsuario = $notificacao->solicitante->foto ? asset('storage/'.$notificacao->solicitante->foto) : url('assets/images/logos/contas/user.png');


                        @endphp

                        {{-- TIPOS DE NOTIFICAÇÃO --}}
                        @if($notificacao->tipo === 'seguir')
                        <div class="foto-usuario-seguir">
                            <img src="{{ $fotoUsuario }}" alt="{{ $nomeUsuario }}">
                            <div class="dados">
                                <a href="{{ $linkConta }}">
                                    {{ $nomeUsuario }}
                                </a>
                                <p>enviou uma solicitação para seguir você.</p>
                            </div>
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
                        <div class="foto-usuario-seguir">
                            <img src="{{ $fotoUsuario }}" alt="{{ $nomeUsuario }}">
                            <div class="dados">
                                <a href="{{ $linkConta }}">
                                    {{ $nomeUsuario }}
                                </a>
                                começou a seguir você.
                            </div>
                        </div>

                        <div class="novo-seguidor">
                            {{-- BOTÃO OK PARA REMOVER A NOTIFICAÇÃO --}}
                            <form action="{{ route('notificacao.destroy', $notificacao->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ok">OK</button>
                            </form>
                        </div>
                        @endif

                    </li>
                    @endforeach
                </ul>
                @endif

                <!-- Modal Criação de postagem -->
                @include('feed.post.create-modal')
            </div>

            <!-- conteúdo você pode gostar  -->
            <div class="content-popular">
                @include('feed.post.partials.buscar')
                @include('feed.post.partials.sidebar-popular')
            </div>

</body>

</html>