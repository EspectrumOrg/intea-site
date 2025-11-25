<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Painel Responsável</title>
    <!-- Ícones -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">
</head>

<body>
    <div class="layout">
        <div class="container-content">

            <!-- Sidebar -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- Conteúdo principal -->
            <div class="container-main">
                <div class="profile-container">

                    <!-- Verificação de segurança -->
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
                                    <div id="btnSeguindo" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguindo', ['id' => $user->id]) }}">
                                        <strong>{{ $autista->usuario->seguindo()->count() }}</strong> Seguindo
                                    </div>
                                    <div id="btnSeguidores" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguidores', ['id' => $user->id]) }}">
                                        <strong>{{ $autista->usuario->seguidores()->count() }}</strong> Seguidores
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

                        <!-- Navegação por abas -->
                        <div class="profile-tabs-container">
                            <button class="tab-scroll-btn tab-scroll-prev" aria-label="Abas anteriores">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>

                            <div class="profile-tabs-wrapper">
                                <div class="profile-tabs">
                                    <button class="tab-button active" data-tab="profile">
                                        <span class="material-symbols-outlined">person</span>
                                        <span class="tab-text">Perfil</span>
                                    </button>

                                    @if($userPosts->count() > 0)
                                        <button class="tab-button" data-tab="posts">
                                            <span class="material-symbols-outlined">article</span>
                                            <span class="tab-text">Postagens ({{ $userPosts->count() }})</span>
                                        </button>
                                    @endif

                                    @if($likedPosts->count() > 0)
                                        <button class="tab-button" data-tab="likes">
                                            <span class="material-symbols-outlined">favorite</span>
                                            <span class="tab-text">Curtidas ({{ $likedPosts->count() }})</span>
                                        </button>
                                    @endif

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
                                        <div class="info-item"><strong>CIPTEA Autista:</strong> <span>{{ $dadosespecificos->cipteia_autista ?? 'Não informado' }}</span></div>
                                        <div class="info-item"><strong>Status CIPTEA:</strong> <span>{{ $dadosespecificos->status_cipteia_autista ?? 'Não informado' }}</span></div>
                                    @endif
                                </div>
                            </section>
                        </div>

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
                                            </div>
                                            <p class="post-content">{{ $post->texto_postagem }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($likedPosts->count() > 0)
                            <div class="tab-content" id="likes-tab">
                                <h3>Postagens Curtidas</h3>
                                <div class="likes-list">
                                    @foreach($likedPosts as $like)
                                        <div class="like-item">
                                            <div class="like-avatar">
                                                @if($like->usuario->foto)
                                                    <img src="{{ asset('storage/'.$like->usuario->foto) }}" alt="{{ $like->usuario->nome }}">
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

                        <div class="tab-content" id="autista-tab">
                            @include('responsavel.dados-autista-responsavel', ['autista' => $autista])
                        </div>

                    @endif
                </div>
            </div>

            <!-- Conteúdo popular -->
            <div class="content-popular">
                @include('profile.partials.buscar')
                @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/js/perfil/modalSeguir.js') }}"></script>
    <script src="{{ url('assets/js/posts/dropdown-option.js') }}"></script>
</body>
</html>
