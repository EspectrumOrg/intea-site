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
                <p class="username"> {{ $user->user }}</p>
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

                <!-- NOVOS BOTÕES: Seguir e Mensagem (aparecem apenas se não for o usuário logado) -->
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
                                
                                <!-- Aba Configurações (apenas para o próprio usuário) -->
                                @if(auth()->id() == $user->id)
                                <button class="tab-button" data-tab="settings">
                                    <span class="material-symbols-outlined">settings</span>
                                    <span class="tab-text">Configurações</span>
                                </button>
                                @endif
                                <!-- Aba Configurações do autista para responsavel-->
                                @if($user->tipo_usuario === 5)
                                <button class="tab-button" data-tab="autista">
                                    <span class="material-symbols-outlined">settings</span>
                                    <span class="tab-text">Configurações do autista</span>
                                </button>
                                @endif
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

                    <!-- Aba 2: Postagens (só mostra se tiver conteúdo) -->
                    @if($userPosts->count() > 0)
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

                    <!-- Aba 4: Configurações (apenas para o próprio usuário) -->
                    @if(auth()->id() == $user->id)
                    <div class="tab-content" id="settings-tab">
                        <!-- Inclui os formulários de configurações -->
                        @include('profile.partials.update-profile-information-form')
                        @include('profile.partials.update-password-form')
                        @include('profile.partials.delete-user-form')
                    </div>
                    @endif
                    <!-- Aba 5: Configurações do autista para responsavel -->
                    @if($user-> tipo_usuario == 5)
                    <div class="tab-content" id="autista-tab">
                        <!-- Inclui os formulários de configurações -->
                        @include('profile.dados-autista-responsavel', ['autista' => $autista])
                    </div>
                    @endif

                    <!-- Mensagem quando não há conteúdo nas abas opcionais -->
                    @if($userPosts->count() == 0 && $likedPosts->count() == 0 && auth()->id() != $user->id)
                    <div class="no-content-message">
                        <p>Este usuário ainda não tem atividades para mostrar.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const tabsWrapper = document.querySelector('.profile-tabs-wrapper');
            const prevBtn = document.querySelector('.tab-scroll-prev');
            const nextBtn = document.querySelector('.tab-scroll-next');
            const tabsContainer = document.querySelector('.profile-tabs');

            // Controle das abas
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove classe active de todos os botões e conteúdos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Adiciona classe active ao botão clicado e conteúdo correspondente
                    this.classList.add('active');
                    const targetContent = document.getElementById(`${tabId}-tab`);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });

            // Controle do scroll horizontal
            function updateScrollButtons() {
                if (!tabsWrapper || !prevBtn || !nextBtn) return;

                const scrollLeft = tabsWrapper.scrollLeft;
                const scrollWidth = tabsWrapper.scrollWidth;
                const clientWidth = tabsWrapper.clientWidth;

                // Mostra/oculta botões baseado no scroll
                prevBtn.style.display = scrollLeft > 0 ? 'flex' : 'none';
                nextBtn.style.display = scrollLeft < (scrollWidth - clientWidth - 10) ? 'flex' : 'none';

                // Ativa/desativa botões
                prevBtn.disabled = scrollLeft <= 0;
                nextBtn.disabled = scrollLeft >= (scrollWidth - clientWidth - 10);
            }

            // Eventos dos botões de scroll
            if (prevBtn && nextBtn && tabsWrapper) {
                prevBtn.addEventListener('click', () => {
                    tabsWrapper.scrollBy({ left: -200, behavior: 'smooth' });
                });

                nextBtn.addEventListener('click', () => {
                    tabsWrapper.scrollBy({ left: 200, behavior: 'smooth' });
                });

                // Atualiza botões quando scrollar
                tabsWrapper.addEventListener('scroll', updateScrollButtons);

                // Atualiza botões no carregamento e redimensionamento
                window.addEventListener('resize', updateScrollButtons);
                updateScrollButtons();
            }

            // Scroll suave para a aba ativa se estiver fora da view
            function scrollToActiveTab() {
                const activeTab = document.querySelector('.tab-button.active');
                if (activeTab && tabsWrapper) {
                    const tabRect = activeTab.getBoundingClientRect();
                    const wrapperRect = tabsWrapper.getBoundingClientRect();

                    if (tabRect.left < wrapperRect.left || tabRect.right > wrapperRect.right) {
                        activeTab.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest', 
                            inline: 'center' 
                        });
                    }
                }
            }

            // Recalcula scroll quando mudar de aba
            tabButtons.forEach(button => {
                button.addEventListener('click', scrollToActiveTab);
            });
        });
    </script>
</body>
</html>