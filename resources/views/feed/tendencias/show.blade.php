@extends('feed.post.template.layout')

@section('main')
<!-- Seus estilos -->
<link rel="stylesheet" href="{{ url('assets/css/tendencias/style.show.css') }}">
<!-- Postagem -->
<link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">

<div class="tendencia-container">
    <!-- Cabeçalho da Tendência -->
    <div class="tendencia-header">
        <div class="tendencia-actions">
            <a href="{{ route('tendencias.index') }}" class="btn-voltar">
                <span class="material-symbols-outlined">arrow_back</span>
                Ver todas as tendências
            </a>
        </div>
        <div class="tendencia-info">
            <h1>{{ $tendencia->hashtag }}</h1>
            <p class="tendencia-stats">
                {{ $tendencia->contador_uso }} posts •
                Último uso: {{ $tendencia->ultimo_uso->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <!-- Lista de Postagens da Tendência -->
    <div class="posts-feed">
        @foreach($postagens as $post)
        <div class="post-card">
            <!-- Cabeçalho do Post -->
            <div class="post-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <a href="{{ route('conta.index', ['usuario_id' => $post->usuario_id]) }}">
                            @if($post->usuario->foto)
                            <img src="{{ asset('storage/' . $post->usuario->foto) }}" alt="{{ $post->usuario->nome }}">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                            @endif
                        </a>
                    </div>
                    <div class="user-details">
                        <a href="{{ route('conta.index', ['usuario_id' => $post->usuario_id]) }}">
                            <strong>{{ $post->usuario->apelido }}</strong>
                        </a>
                        <a href="{{ route('conta.index', ['usuario_id' => $post->usuario_id]) }}">
                            <small>{{ $post->usuario->user }}</small>
                        </a>
                        <small class="post-time">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>

                <div class="dropdown"> <!-- OPÇÕES POSTAGEM (COISO QUE FICA NOS PONTINHOS PRETOS LÁ) ========-->
                    <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                        <span class="material-symbols-outlined">more_horiz</span>
                    </button>
                    <ul class="dropdown-content">
                        <!-- Postagem do usuário --------------------->
                        @if(Auth::id() === $post->usuario_id)
                        <li>
                            <button type="button"
                                class="btn-acao editar"
                                onclick="abrirModalEditar('{{$post->id}}')">
                                <span class="material-symbols-outlined">edit</span>Editar
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('post.destroy', $post->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-acao excluir">
                                    <span class="material-symbols-outlined">delete</span>Excluir
                                </button>
                            </form>
                        </li>
                        @else
                        <!-- Postagem de terceiro --------------------->
                        <li>
                            @if( Auth::user()->tipo_usuario === 1 )
                            <!-- Banir Usuário (caso tipo admin)-->
                            <div class="form-excluir">
                                <button type="button" class="btn-acao btn-excluir-usuario" data-bs-toggle="modal" onclick="abrirModalBanimentoUsuarioEspecifico('{{ $post->usuario->id }}')">
                                    <span class="material-symbols-outlined">person_off</span>Banir
                                </button>
                            </div>
                            @else
                            <!-- Denunciar Usuário -->
                            <a class="btn-acao denunciar" href="javascript:void(0)" onclick="abrirModalDenuncia('{{ $post->id }}')">
                                <span class="material-symbols-outlined">flag_2</span>Denunciar
                            </a>
                            @endif
                        </li>
                        <!-- Parte de seguir (do nicolas) -->
                        <li>
                            @php
                            $authUser = Auth::user();
                            $isFollowing = $authUser->seguindo->contains($post->usuario_id);
                            $pedidoFeito = \App\Models\Notificacao::where('solicitante_id', $authUser->id)
                            ->where('alvo_id', $post->usuario_id)
                            ->where('tipo', 'seguir')
                            ->exists();
                            @endphp

                            @if ($authUser->id !== $post->usuario_id)

                            {{-- Se já segue → sempre mostrar Deixar de seguir --}}
                            @if ($isFollowing)
                            <form action="{{ route('seguir.destroy', $post->usuario_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-acao deixar-btn">
                                    <span class="material-symbols-outlined">person_remove</span> Deixar de seguir
                                </button>
                            </form>

                            {{-- Usuário privado (não está seguindo) --}}
                            @elseif ($post->usuario->visibilidade == 0)
                            @if ($pedidoFeito)
                            <form action="{{ route('seguir.cancelar', $post->usuario_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-acao seguir-btn">
                                    <span class="material-symbols-outlined">hourglass_empty</span> Pedido enviado
                                </button>
                            </form>
                            @else
                            <form action="{{ route('seguir.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                <button type="submit" class="btn-acao seguir-btn">
                                    <span class="material-symbols-outlined">person_add</span> Pedir para seguir
                                </button>
                            </form>
                            @endif

                            {{-- Usuário público (não está seguindo) --}}
                            @else
                            <form action="{{ route('seguir.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                <button type="submit" class="btn-acao seguir-btn">
                                    <span class="material-symbols-outlined">person_add</span> Seguir {{ $post->usuario->user }}
                                </button>
                            </form>
                            @endif

                            @endif
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Conteúdo do Post -->
            <div class="post-content">
                @php
                // Processar hashtags no texto
                $textoComHashtags = preg_replace(
                '/#(\w+)/',
                '<span class="hashtag">#$1</span>',
                $post->texto_postagem
                );
                @endphp
                <p>{!! $textoComHashtags !!}</p>
            </div>

            <!-- Imagens do Post -->
            @if($post->imagens && $post->imagens->count() > 0)
            <div class="post-images">
                @foreach($post->imagens as $imagem)
                <img src="{{ asset('storage/' . $imagem->caminho_imagem) }}" alt="Imagem do post" class="post-image">
                @endforeach
            </div>
            @endif

            <!-- Estatísticas do Post -->
            <div class="post-stats">
                <div>
                    <button type="button" onclick="toggleForm('{{ $post->id }}')" class="button btn-comentar">
                        <a href="javascript:void(0)" onclick="abrirModalComentar('{{ $post->id }}')">
                            <span class="material-symbols-outlined">chat_bubble</span>
                            <h1>{{ $post->comentarios_count }}</h1>
                        </a>
                    </button>
                </div>

                <form method="POST" action="{{ route('curtida.toggle') }}">
                    @csrf
                    <input type="hidden" name="tipo" value="postagem">
                    <input type="hidden" name="id" value="{{ $post->id}}">
                    <button type="submit" class="button btn-curtir {{ $post->curtidas_usuario ? 'curtido' : 'normal' }}">
                        <span class="material-symbols-outlined">favorite</span>
                        <h1>{{ $post->curtidas_count }}</h1>
                    </button>
                </form>
            </div>

            <!-- Ações do Post -->
            <div class="post-actions">
                <a href="{{ route('post.show', $post->id) }}" class="btn-action">
                    Ver post completo
                </a>
            </div>
        </div>

        <!-- Modal Edição dessa postagem -->
        @include('feed.post.edit', ['postagem' => $post])

        <!-- Modal Criação de comentário ($post->id) -->
        @include('feed.post.create-comentario-modal', ['postagem' => $post])

        <!-- Modal Criação de comentário ($post->id) -->
        @include('feed.post.create-comentario-modal', ['postagem' => $post])

        <!-- Modal de denúncia (um para cada postagem) -->
        <div id="modal-denuncia-postagem-{{ $post->id }}" class="modal-denuncia hidden">
            <div class="modal-content">
                <h3 class="texto-next-close-button">Coletando informações</h3>
                <span class="close"
                    onclick="fecharModalDenuncia('{{$post->id}}')">
                    <span class="material-symbols-outlined">close</span>
                </span>

                <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                    @csrf
                    <input type="hidden" name="tipo" value="postagem">
                    <input type="hidden" name="id_alvo" value="{{ $post->id }}">

                    @include('layouts.partials.modal-denuncia')
                </form>
            </div>
        </div>

        <!-- modal banir-->
        @include('layouts.partials.modal-banimento', ['usuario' => $post->usuario])

        @endforeach

        @if($postagens->count() == 0)
        <div class="no-posts">
            <div class="no-posts-content">
                <span class="material-symbols-outlined">tag</span>
                <h3>Nenhum post encontrado</h3>
                <p>Esta tendência ainda não tem posts. Seja o primeiro a usar {{ $tendencia->hashtag }}!</p>
                <a href="{{ route('post.index') }}" class="btn-primary">
                    Criar primeiro post
                </a>
            </div>
        </div>
        @endif

        <!-- Paginação -->
        @if($postagens->hasPages())
        <div class="pagination-container">
            {{ $postagens->links() }}
        </div>
        @endif
    </div>
</div>

@endsection