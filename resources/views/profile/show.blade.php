<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Perfil</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    
    <!-- Seus estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
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
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            @if (!empty($user->foto))
                            <img src="{{ asset('storage/'.$user->foto) }}" class="card-img-top" alt="foto perfil">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
                            @endif
                        </div>
                        <div class="profile-info">
                            <h1>{{ $user->nome }}</h1>
                            <p class="username">@ {{ $user->user }}</p>
                            <p class="bio">{{ $user->descricao ?? 'Sem descrição' }}</p>
                            <p class="tipo-usuario">
                                @switch($user->tipo_usuario)
                                    @case(1) Administrador @break
                                    @case(2) Autista @break
                                    @case(3) Comunidade @break
                                    @case(4) Profissional de Saúde @break
                                    @case(5) Responsável @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    <!-- Navegação por abas -->
                    <div class="profile-tabs">
                        <button class="tab-button active" data-tab="profile">
                            <span class="material-symbols-outlined">person</span>
                            Perfil
                        </button>
                        <button class="tab-button" data-tab="posts">
                            <span class="material-symbols-outlined">article</span>
                            Postagens ({{ $userPosts->count() }})
                        </button>
                        <button class="tab-button" data-tab="likes">
                            <span class="material-symbols-outlined">favorite</span>
                            Curtidas ({{ $likedPosts->count() }})
                        </button>
                        
                        <!-- Nova aba: Configurações (apenas para o próprio usuário) -->
                        @if(auth()->id() == $user->id)
                        <button class="tab-button" data-tab="settings">
                            <span class="material-symbols-outlined">settings</span>
                            Configurações
                        </button>
                        @endif
                    </div>

                    <!-- Conteúdo das abas -->
                    
                    <!-- Aba 1: Perfil (Informações) -->
                    <div class="tab-content active" id="profile-tab">
                        <section class="perfil-section">
                            <header class="header">
                                <h2>Informações do Perfil</h2>
                            </header>

                            <div class="profile-info-grid">
                                <div class="info-item">
                                    <strong>Nome:</strong>
                                    <span>{{ $user->nome }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>Email:</strong>
                                    <span>{{ $user->email }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>Apelido:</strong>
                                    <span>{{ $user->apelido ?? 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>CPF:</strong>
                                    <span>{{ $user->cpf }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>Data Nascimento:</strong>
                                    <span>{{ \Carbon\Carbon::parse($user->data_nascimento)->format('d/m/Y') }}</span>
                                </div>
                                
                                @if($dadosespecificos)
                                    @if($user->tipo_usuario == 2)
                                    <div class="info-item">
                                        <strong>CIPTEA Autista:</strong>
                                        <span>{{ $dadosespecificos->cipteia_autista ?? 'Não informado' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Status CIPTEA:</strong>
                                        <span>{{ $dadosespecificos->status_cipteia_autista ?? 'Não informado' }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($user->tipo_usuario == 4)
                                    <div class="info-item">
                                        <strong>Tipo de Registro:</strong>
                                        <span>{{ $dadosespecificos->tipo_registro ?? 'Não informado' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Registro Profissional:</strong>
                                        <span>{{ $dadosespecificos->registro_profissional ?? 'Não informado' }}</span>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </section>
                    </div>

                    <!-- Aba 2: Postagens -->
                    <div class="tab-content" id="posts-tab">
                        <h3>Minhas Postagens</h3>
                        <div class="posts-grid">
                            @foreach($userPosts as $post)
                                <div class="post-card">
                                    <div class="post-header">
                                        <small>{{ $post->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="post-content">{{ $post->texto_postagem }}</p>
                                    
                                    @if($post->imagens && $post->imagens->count() > 0)
                                        @foreach($post->imagens as $imagem)
                                            <img src="{{ asset('storage/'.$imagem->caminho_imagem) }}" alt="Imagem do post" class="post-image">
                                        @endforeach
                                    @endif
                                    
                                    <div class="post-stats">
                                        <span>❤️ {{ $post->curtidas_count }} curtidas</span>
                                        <span>💬 {{ $post->comentarios_count }} comentários</span>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($userPosts->count() == 0)
                                <p>Nenhuma postagem encontrada.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Aba 3: Curtidas -->
                    <div class="tab-content" id="likes-tab">
                        <h3>Postagens Curtidas</h3>
                        <div class="likes-list">
                            @foreach($likedPosts as $like)
                                <div class="like-item">
                                    <div class="like-avatar">
                                        @if($like->postagem->usuario->foto)
                                            <img src="{{ asset('storage/'.$like->postagem->usuario->foto) }}" alt="{{ $like->postagem->usuario->nome }}">
                                        @else
                                            <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                                        @endif
                                    </div>
                                    <div class="like-content">
                                        <strong>{{ $like->postagem->usuario->nome }}</strong>
                                        <p>{{ $like->postagem->texto_postagem }}</p>
                                        <small>Curtido em {{ $like->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($likedPosts->count() == 0)
                                <p>Nenhuma curtida encontrada.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Aba 4: Configurações (apenas para o próprio usuário) -->
                    @if(auth()->id() == $user->id)
                    <div class="tab-content" id="settings-tab">
                        <!-- Inclui os formulários de configurações -->
                        @include('profile.partials.update-profile-information-form')
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                    @endif
                </div>
            </div>

            <!-- conteúdo popular -->
            <div class="content-popular">
                @include('feed.post.partials.sidebar-popular', ['posts' => $postsPopulares])
            </div>
        </div>
    </div>

    <style>
        .profile-tabs {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 2rem;
            background: white;
            border-radius: 8px 8px 0 0;
            padding: 0 1rem;
        }
        
        .tab-button {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab-button:hover {
            color: #4f46e5;
            background-color: #f8fafc;
        }
        
        .tab-button.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background-color: #f8fafc;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .post-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        
        .likes-list {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .like-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .post-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-top: 1rem;
        }
        
        .like-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    this.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>