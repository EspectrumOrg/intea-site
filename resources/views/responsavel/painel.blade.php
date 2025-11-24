<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Painel Responsável</title>
    <!-- icones-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/profile/modalSeguir.css') }}">
    <!-- Postagem -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">
</head>

<body>
    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- conteúdo principal -->
            <div class="container-main">
                <div class="profile-container">
                    <!-- VERIFICAÇÃO DE SEGURANÇA -->
                    @if(!isset($user) || is_null($user))
                    <div class="alert alert-danger text-center">
                        <h4>❌ Erro: Perfil não encontrado</h4>
                        <p>O usuário que você está tentando acessar não existe.</p>
                        <a href="/feed" class="btn btn-primary">Voltar para o Feed</a>
                    </div>
                    @else
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            @if (!empty($autista->usuario->foto))
                            <img src="{{ asset('storage/'.$autista->usuario->foto) }}" alt="Foto do autista">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Foto do autista">
                            @endif
                        </div>

                        <div class="profile-info">
                            <h1>{{ $autista->usuario->nome }}</h1>
                            <p class="username">{{ $autista->usuario->user }}</p>
                            <p class="bio">{{ $autista->usuario->descricao ?? 'Sem descrição' }}</p>

                            <div class="profile-counts" style="display: flex; gap: 20px; margin-bottom: 10px;">
                                <div class="profile-counts" style="display: flex; gap: 20px; margin-bottom: 10px;">
                                    <div id="btnSeguindo" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguindo', ['id' => $user->id]) }}">
                                        <strong>{{ $autista->usuario->seguindo()->count() }}</strong> Seguindo
                                    </div>

                                    <div id="btnSeguidores" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguidores', ['id' => $user->id]) }}">
                                        <strong>{{ $autista->usuario->seguidores()->count() }}</strong> Seguidores
                                    </div>
                                </div>

                                <!-- Modal simples -->
                                <div id="modalUsuarios"
                                    style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
                                             background:white; padding:20px; border:1px solid #ccc; max-height:400px; overflow-y:auto;">
                                    <button id="fecharModal">Fechar</button>
                                    <ul id="listaUsuarios"></ul>
                                </div>
                            </div>

                            @if(auth()->id() != $user->id)
                            <div class="profile-action-buttons" style="margin-top: 10px; display: flex; gap: 10px;">
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Seguir
                                    </button>
                                </form>

                                <a href="{{ route('chat.dashboard') }}?usuario2={{ $user->id }}" class="btn-mensagem">
                                    <span class="material-symbols-outlined">message</span> Mensagem
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Navegação por abas DINÂMICA -->
                    <div class="profile-tabs-container">
                        <button class="tab-scroll-btn tab-scroll-prev" aria-label="Abas anteriores">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>

                        <div class="profile-tabs-wrapper">
                            <div class="profile-tabs">
                                <!-- Aba Perfil (SEMPRE existe) -->
                                <button class="tab-button active" data-tab="profile">
                                    <span class="material-symbols-outlined">person</span>
                                    <span class="tab-text">Perfil</span>
                                </button>

                                <!-- Aba Postagens (só mostra se tiver postagens) -->
                                @if($userPosts->count() > 0)
                                <button class="tab-button" data-tab="posts">
                                    <span class="material-symbols-outlined">article</span>
                                    <span class="tab-text">Postagens ({{ $userPosts->count() }})</span>
                                </button>
                                @endif

                                <!-- Aba Curtidas (só mostra se tiver curtidas) -->
                                @if($likedPosts->count() > 0)
                                <button class="tab-button" data-tab="likes">
                                    <span class="material-symbols-outlined">favorite</span>
                                    <span class="tab-text">Curtidas ({{ $likedPosts->count() }})</span>
                                </button>
                                @endif

                                <!-- Aba Configurações do autista -->
                                <button class="tab-button" data-tab="autista">
                                    <span class="material-symbols-outlined">settings</span>
                                    <span class="tab-text">Dados do autista</span>
                                </button>
                            </div>
                        </div>

                        <button class="tab-scroll-btn tab-scroll-next" aria-label="Próximas abas">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>

                    <!-- Conteúdo das abas -->
                    <!-- Aba 1: Perfil (Informações) - SEMPRE existe -->
                    <div class="tab-content active" id="profile-tab">
                        <section class="perfil-section">
                            <header class="header">
                                <h2>Informações do Perfil</h2>
                            </header>

                            <div class="profile-info-grid">
                                <div class="info-item">
                                    <strong>Nome:</strong>
                                    <span>{{ $autista->usuario->nome }}</span>
                                </div>

                                <div class="info-item">
                                    <strong>Email:</strong>
                                    <span>{{ $autista->usuario->email }}</span>
                                </div>

                                <div class="info-item">
                                    <strong>Apelido:</strong>
                                    <span>{{ $autista->usuario->apelido ?? 'Não informado' }}</span>
                                </div>

                                <div class="info-item">
                                    <strong>CPF:</strong>
                                    <span
                                        class="{{ $autista->usuario->cpf === '•••••••••••' ? 'cpf-privado' : 'cpf-publico' }}">
                                        {{ $autista->usuario->cpf }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <strong>Data Nascimento:</strong>
                                    <span>{{ \Carbon\Carbon::parse($autista->usuario->data_nascimento)->format('d/m/Y') }}</span>
                                </div>

                                @if($dadosespecificos)
                                <div class="info-item">
                                    <strong>CIPTEA Autista:</strong>
                                    <span>{{ $dadosespecificos->cipteia_autista ?? 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>Status CIPTEA:</strong>
                                    <span>{{ $dadosespecificos->status_cipteia_autista ?? 'Não informado' }}</span>
                                </div>
                                @endif
                            </div>
                        </section>
                    </div>

                    <!-- Aba 2: Postagens (só mostra se tiver conteúdo) -->
                    @if($userPosts->count() > 0)
                    <div class="tab-content" id="posts-tab">
                        <h3>Postagens</h3>
                        <div class="posts-grid">
                            @foreach($userPosts as $post)
                            <div class="post-card">
                                <div class="post-header">
                                    <div class="dados-post">
                                        <a href="{{ route('post.read', ['postagem' => $post->id]) }}">
                                            <h1>{{ Str::limit($post->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                                        </a>
                                        <small>{{ $post->created_at->format('d/m/Y H:i') }}</small>
                                    </div>

                                    <div class="dropdown">
                                        <!-- OPÇÕES POSTAGEM (COISO QUE FICA NOS PONTINHOS PRETOS LÁ) ========-->
                                        <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>
                                        <ul class="dropdown-content">
                                            <!-- Postagem do usuário --------------------->
                                            @if(Auth::id() === $post->usuario_id)
                                            <li>
                                                <button type="button" class="btn-acao editar"
                                                    onclick="abrirModalEditar('{{ $post->id }}')">
                                                    <span class="material-symbols-outlined">edit</span>Editar
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('post.destroy', $post->id) }}" method="POST"
                                                    style="display:inline">
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
                                                    <button type="button" class="btn-acao btn-excluir-usuario"
                                                        data-bs-toggle="modal"
                                                        onclick="abrirModalBanimentoUsuarioEspecifico('{{ $post->usuario->id }}')">
                                                        <span class="material-symbols-outlined">person_off</span>Banir
                                                    </button>
                                                </div>
                                                @else
                                                <!-- Denunciar Usuário -->
                                                <a class="btn-acao denunciar" href="javascript:void(0)"
                                                    onclick="abrirModalDenuncia('{{ $post->id }}')">
                                                    <span class="material-symbols-outlined">flag_2</span>Denunciar
                                                </a>
                                                @endif
                                            </li>
                                            <!-- Parte de seguir (do nicolas) -->
                                            <li>
                                                @php
                                                $authUser = Auth::user();
                                                $isFollowing = $authUser->seguindo->contains($post->usuario_id);
                                                $pedidoFeito = \App\Models\Notificacao::where('solicitante_id',
                                                $authUser->id)
                                                ->where('alvo_id', $post->usuario_id)
                                                ->where('tipo', 'seguir')
                                                ->exists();
                                                @endphp

                                                @if ($authUser->id !== $post->usuario_id)

                                                {{-- Se já segue → sempre mostrar Deixar de seguir --}}
                                                @if ($isFollowing)
                                                <form action="{{ route('seguir.destroy', $post->usuario_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-acao deixar-btn">
                                                        <span class="material-symbols-outlined">person_remove</span>
                                                        Deixar de seguir
                                                    </button>
                                                </form>

                                                {{-- Usuário privado (não está seguindo) --}}
                                                @elseif ($post->usuario->visibilidade == 0)
                                                @if ($pedidoFeito)
                                                <form action="{{ route('seguir.cancelar', $post->usuario_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span class="material-symbols-outlined">hourglass_empty</span>
                                                        Pedido enviado
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('seguir.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span class="material-symbols-outlined">person_add</span> Pedir
                                                        para seguir
                                                    </button>
                                                </form>
                                                @endif

                                                {{-- Usuário público (não está seguindo) --}}
                                                @else
                                                <form action="{{ route('seguir.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span class="material-symbols-outlined">person_add</span> Seguir
                                                        {{ $post->usuario->user }}
                                                    </button>
                                                </form>
                                                @endif

                                                @endif
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <p class="post-content">{{ $post->texto_postagem }}</p>
                                @if($post->imagens && $post->imagens->count() > 0)
                                @foreach($post->imagens as $imagem)
                                <img src="{{ asset('storage/'.$imagem->caminho_imagem) }}" alt="Imagem do post"
                                    class="post-image">
                                @endforeach
                                @endif
                                <div class="post-stats">
                                    <div>
                                        <button type="button" onclick="toggleForm('{{ $post->id }}')"
                                            class="button btn-comentar">
                                            <a href="javascript:void(0)"
                                                onclick="abrirModalComentar('{{ $post->id }}')">
                                                <span class="material-symbols-outlined">chat_bubble</span>
                                                <h1>{{ $post->comentarios_count }}</h1>
                                            </a>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('curtida.toggle') }}">
                                        @csrf
                                        <input type="hidden" name="tipo" value="postagem">
                                        <input type="hidden" name="id" value="{{ $post->id}}">
                                        <button type="submit"
                                            class="button btn-curtir {{ $post->curtidas_usuario ? 'curtido' : 'normal' }}">
                                            <span class="material-symbols-outlined">favorite</span>
                                            <h1>{{ $post->curtidas_count }}</h1>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Edição dessa postagem -->
                            @include('feed.post.edit', ['postagem' => $post])

                            <!-- Modal Criação de comentário ($postagem->id) -->
                            @include('feed.post.create-comentario-modal', ['postagem' => $post])

                            <!-- Modal Criação de comentário ($post->id) -->
                            @include('feed.post.create-comentario-modal', ['postagem' => $post])

                            <!-- Modal de denúncia (um para cada postagem) -->
                            <div id="modal-denuncia-postagem-{{ $post->id }}" class="modal-denuncia hidden">
                                <div class="modal-content">
                                    <h3 class="texto-next-close-button">Coletando informações</h3>
                                    <span class="close" onclick="fecharModalDenuncia('{{$post->id}}')">
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

                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Aba 3: Curtidas (só mostra se tiver conteúdo) -->
                    @if($likedPosts->count() > 0)
                    <div class="tab-content" id="likes-tab">
                        <h3>Postagens Curtidas</h3>
                        <div class="likes-list">
                            @foreach($likedPosts as $like)
                            <div class="like-item">
                                <div class="like-avatar">
                                    @if($like->usuario->foto)
                                    <img src="{{ asset('storage/'.$like->usuario->foto) }}"
                                        alt="{{ $like->usuario->nome }}">
                                    @else
                                    <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                                    @endif
                                </div>

                                <div class="like-content">
                                    <strong>{{ $like->usuario->nome }}</strong>
                                    <p>{{ $like->texto_postagem }}</p>
                                    <small>Curtido em {{ $like->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Configurações do autista para responsavel -->
                    <div class="tab-content" id="autista-tab">
                        <!-- Inclui os formulários de configurações -->
                        @include('responsavel.dados-autista-responsavel', ['autista' => $autista])
                    </div>

                    <!-- Mensagem quando não há conteúdo nas abas opcionais -->
                    @if($userPosts->count() == 0 && $likedPosts->count() == 0 && auth()->id() != $user->id)
                    <div class="no-content-message">
                        <p>Este usuário ainda não tem atividades para mostrar.</p>
                    </div>
                    @endif
                    @endif
                    <!-- Fim da verificação de segurança -->
                </div>
            </div>

            <!-- conteúdo popular -->
            <div class="content-popular">
                @include('profile.partials.buscar')
                @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])
            </div>
        </div>
    </div>
    <!-- JS -->
    <script src="{{ asset('assets/js/perfil/modalSeguir.js') }}"></script>
    <!-- modal de denúncia usuário -->
    <script src="{{ url('assets/js/perfil/modal-denuncia-usuario.js') }}"></script>
    <!-- modal de denúncia usuário -->
    <script src="{{ url('assets/js/posts/modal-denuncia.js') }}"></script>
    <!-- dropdown-->
    <script src="{{ url('assets/js/posts/dropdown-option.js') }}"></script>
</body>

</html>
