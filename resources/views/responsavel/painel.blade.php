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

                    {{-- Se houver múltiplos autistas, mostrar seleção --}}
                    @if(isset($autistas) && $autistas->count() > 1)
                    <div class="autistas-list" style="margin-bottom: 20px; display:flex; gap:10px; overflow:auto;">
                        @foreach($autistas as $autista)
                        <a href="?autista={{ $autista->id }}"
                            class="btn btn-sm {{ isset($selectedAutista) && $selectedAutista && $selectedAutista->id == $autista->id ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $autista->usuario->apelido}}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    @if(isset($selectedAutista) && $selectedAutista)
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            <img src="{{ !empty($selectedAutista->usuario->foto) ? asset('storage/'.$selectedAutista->usuario->foto) : url('assets/images/logos/contas/user.png') }}"
                                alt="Foto do autista">
                        </div>

                        <div class="profile-info">
                            <h1>{{ $selectedAutista->usuario->nome }}</h1>
                            <p class="username">{{ $selectedAutista->usuario->user }}</p>
                            <p class="bio">{{ $selectedAutista->usuario->descricao ?? 'Sem descrição' }}</p>

                            <div class="profile-counts" style="display: flex; gap: 20px; margin-bottom: 10px;">
                                <div id="btnSeguindo" style="cursor:pointer"
                                    data-url="{{ route('usuario.listar.seguindo', ['id' => $selectedAutista->usuario->id]) }}">
                                    <strong>{{ $selectedAutista->usuario->seguindo()->count() }}</strong> Seguindo
                                </div>
                                <div id="btnSeguidores" style="cursor:pointer"
                                    data-url="{{ route('usuario.listar.seguidores', ['id' => $selectedAutista->usuario->id]) }}">
                                    <strong>{{ $selectedAutista->usuario->seguidores()->count() }}</strong> Seguidores
                                </div>
                            </div>

                            @if(auth()->id() != $selectedAutista->usuario->id)
                            <div class="profile-action-buttons" style="margin-top: 10px; display: flex; gap: 10px;">
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $selectedAutista->usuario->id }}">
                                    <button type="submit" class="seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Seguir
                                    </button>
                                </form>

                                <a href="{{ route('chat.dashboard') }}?usuario2={{ $selectedAutista->usuario->id }}"
                                    class="btn-mensagem">
                                    <span class="material-symbols-outlined">message</span> Mensagem
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

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

                                @if($comentariosAutista->count() > 0)
                                <button class="tab-button" data-tab="comments">
                                    <span class="material-symbols-outlined">comment</span>
                                    <span class="tab-text">Comentários ({{ $comentariosAutista->count() }})</span>
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
                    @if(isset($selectedAutista) && $selectedAutista)
                    <div class="tab-content active" id="profile-tab">
                        <section class="perfil-section">
                            <header class="header">
                                <h2>Informações do Perfil</h2>
                            </header>

                            <div class="profile-info-grid">
                                <div class="info-item"><strong>Email:</strong>
                                    <span>{{ $selectedAutista->usuario->email }}</span>
                                </div>
                                <div class="info-item"><strong>Apelido:</strong>
                                    <span>{{ $selectedAutista->usuario->apelido ?? 'Não informado' }}</span>
                                </div>
                                <div class="info-item"><strong>CPF:</strong>
                                    <span>{{ $selectedAutista->usuario->cpf }}</span>
                                </div>
                                <div class="info-item"><strong>Data Nascimento:</strong>
                                    <span>{{ \Carbon\Carbon::parse($selectedAutista->usuario->data_nascimento)->format('d/m/Y') }}</span>
                                </div>

                                @if($dadosespecificos)
                                <div class="info-item"><strong>CIPTEA Autista:</strong>
                                    <span>{{ $dadosespecificos->cipteia_autista ?? 'Não informado' }}</span>
                                </div>
                                <div class="info-item"><strong>Status CIPTEA:</strong>
                                    <span>{{ $dadosespecificos->status_cipteia_autista ?? 'Não informado' }}</span>
                                </div>
                                @endif
                            </div>
                        </section>
                    </div>
                    @endif

                    <!-- Aba 2: Postagens (só mostra se tiver conteúdo) ------------------------------------------->
                    @if($userPosts->count() > 0)
                    <div class="tab-content" id="posts-tab">
                        <h3>Minhas Postagens</h3>
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
                                        <!-- Botão dos pontinhos -->
                                        <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>

                                        <ul class="dropdown-content">
                                            @php
                                            $authUser = Auth::user();

                                            // Verifica se o usuário é dono do post
                                            $isOwner = intval($authUser->id) === intval($post->usuario_id);

                                            // Responsável (tipo_usuario = 5) pode gerenciar posts de seus autistas
                                            if (!$isOwner && $authUser->tipo_usuario == 5 && isset($autistas)) {
                                            $isOwner = $autistas->contains(function($autista) use ($post) {
                                            return intval($autista->usuario->id) === intval($post->usuario_id);
                                            });
                                            }

                                            // Checa se já segue o usuário
                                            $isFollowing =
                                            $authUser->seguindo->pluck('id')->map('intval')->contains(intval($post->usuario_id));

                                            // Checa se já enviou pedido de seguir
                                            $pedidoFeito = \App\Models\Notificacao::where('solicitante_id',
                                            $authUser->id)
                                            ->where('alvo_id', $post->usuario_id)
                                            ->where('tipo', 'seguir')
                                            ->exists();
                                            @endphp

                                            {{-- Editar / Excluir para dono/responsável --}}
                                            @if($isOwner)
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

                                            {{-- Posts de terceiros --}}
                                            @else
                                            {{-- Banir usuário (Admin) --}}
                                            @if($authUser->tipo_usuario === 1)
                                            <li>
                                                <button type="button" class="btn-acao btn-excluir-usuario"
                                                    onclick="abrirModalBanimentoUsuarioEspecifico('{{ $post->usuario->id }}')">
                                                    <span class="material-symbols-outlined">person_off</span>Banir
                                                </button>
                                            </li>
                                            {{-- Denunciar usuário --}}
                                            @else
                                            <li>
                                                <a class="btn-acao denunciar" href="javascript:void(0)"
                                                    onclick="abrirModalDenuncia('{{ $post->id }}')">
                                                    <span class="material-symbols-outlined">flag_2</span>Denunciar
                                                </a>
                                            </li>
                                            @endif

                                            {{-- Seguir / Deixar de seguir / Pedido enviado --}}
                                            @if ($authUser->id !== $post->usuario_id)
                                            <li>
                                                @if ($isFollowing)
                                                <form action="{{ route('seguir.destroy', $post->usuario_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-acao deixar-btn">
                                                        <span
                                                            class="material-symbols-outlined">person_remove</span>Deixar
                                                        de seguir
                                                    </button>
                                                </form>
                                                @elseif ($post->usuario->visibilidade == 0)
                                                @if ($pedidoFeito)
                                                <form action="{{ route('seguir.cancelar', $post->usuario_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span
                                                            class="material-symbols-outlined">hourglass_empty</span>Pedido
                                                        enviado
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('seguir.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span class="material-symbols-outlined">person_add</span>Pedir
                                                        para seguir
                                                    </button>
                                                </form>
                                                @endif
                                                @else
                                                <form action="{{ route('seguir.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $post->usuario_id }}">
                                                    <button type="submit" class="btn-acao seguir-btn">
                                                        <span class="material-symbols-outlined">person_add</span>Seguir
                                                        {{ $post->usuario->user }}
                                                    </button>
                                                </form>
                                                @endif
                                            </li>
                                            @endif
                                            @endif
                                        </ul>
                                    </div>

                                </div>
                                <p class="texto-curto" id="texto-{{ $post->id }}">
                                    {{ Str::limit($post->texto_postagem, 150, '') }}
                                    @if (strlen($post->texto_postagem) > 150)
                                    <span class="mostrar-mais"
                                        onclick="toggleTexto('{{ $post->id }}', this)">...mais</span>
                                    @endif
                                </p>

                                <p class="texto-completo" id="texto-completo-{{ $post->id }}" style="display: none;">
                                    {{ $post->texto_postagem }}
                                    <span class="mostrar-mais"
                                        onclick="toggleTexto('{{ $post->id }}', this)">...menos</span>
                                </p>
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
                            <!-- Modal Edição dessa postagem -->
                            @include('feed.post.edit', ['postagem' => $post])

                            <!-- Modal Criação de comentário ($postagem->id) -->
                            @include('feed.post.create-comentario-modal', ['postagem' => $post])
                            <!-- Modal Criação de comentário ($post->id) -->
                            @include('feed.post.create-comentario-modal', ['postagem' => $post])

                            <!-- Modal Criação de comentário ($post->id) -->
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

                    <!-- Aba 3: Curtidas -->
                    <div class="tab-content" id="likes-tab">
                        <h3>Conteúdo Curtido</h3>

                        <!-- Abas internas para Postagens e Comentários -->
                        <div class="likes-tabs">
                            <button class="likes-tab-button active" data-likes-tab="posts">
                                Postagens ({{ $likedPosts->count() }})
                            </button>
                        </div>

                        <!-- Conteúdo de Postagens Curtidas -->
                        <div class="likes-tab-content active" id="posts-likes-content">
                            @if($likedPosts->count() > 0)
                            <div class="likes-list">
                                @foreach($likedPosts as $like)
                                @if($like->postagem)
                                <div class="like-item">
                                    <div class="like-avatar">
                                        @if($like->postagem->usuario->foto)
                                        <img src="{{ asset('storage/'.$like->postagem->usuario->foto) }}"
                                            alt="{{ $like->postagem->usuario->apelido }}">
                                        @else
                                        <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                                        @endif
                                    </div>

                                    <div class="like-content">
                                        <strong>{{ $like->postagem->usuario->apelido }}</strong>
                                        <p>{{ Str::limit($like->postagem->texto_postagem, 100) }}</p>
                                        <small>Curtido em {{ $like->created_at->format('d/m/Y H:i') }}</small>

                                        <a href="{{ route('post.read', ['postagem' => $like->postagem->id]) }}"
                                            class="ver-post-link">
                                            Ver postagem completa
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <div class="no-content-message">
                                <p>Nenhuma postagem curtida ainda.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Aba 4: Comentários -->
                    <div class="tab-content" id="comments-tab">

                        <h3>Comentários enviados</h3>

                        <div class="comments-list">
                            @if($comentariosAutista->count() > 0)

                            @foreach($comentariosAutista as $comentario)

                            <div class="like-item">

                                <!-- Avatar do dono da postagem -->
                                <div class="like-avatar">
                                    @if($comentario->postagem->usuario->foto)
                                    <img src="{{ asset('storage/'.$comentario->postagem->usuario->foto) }}"
                                        alt="{{ $comentario->postagem->usuario->apelido }}">
                                    @else
                                    <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                                    @endif
                                </div>

                                <div class="like-content">


                                        <div class="dropdown">
                                            <button class="menu-opcoes" onclick="toggleDropdown(event, this)">
                                                <span class="material-symbols-outlined">more_horiz</span>
                                            </button>

                                            <ul class="dropdown-content">
                                                @php
                                                $authUser = Auth::user();
                                                $isOwner = intval($authUser->id) === intval($comentario->id_usuario);

                                                if (!$isOwner && $authUser->tipo_usuario == 5 && isset($autistas)) {
                                                $isOwner = $autistas->contains(function($autista) use ($comentario) {
                                                return intval($autista->usuario->id) ===
                                                intval($comentario->id_usuario);
                                                });
                                                }
                                                @endphp

                                                @if($isOwner)
                                                <li>
                                                    <button type="button" class="btn-acao editar"
                                                        onclick="event.stopPropagation(); abrirModalEditarComentario('{{ $comentario->id }}')">
                                                        <span class="material-symbols-outlined">edit</span>Editar
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('comentario.destroy', $comentario->id) }}"
                                                        method="POST" style="display:inline"
                                                        onsubmit="return confirm('Deseja excluir este comentário?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-acao excluir">
                                                            <span class="material-symbols-outlined">delete</span>Excluir
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>


                                        <strong>
                                            Comentou na postagem de {{ $comentario->postagem->usuario->apelido }}
                                        </strong>

                                        <p>{{ Str::limit($comentario->comentario, 120) }}</p>

                                        @if ($comentario->image)
                                        <img src="{{ asset('storage/'.$comentario->image->caminho) }}"
                                            alt="Imagem do comentário" class="comment-image">
                                        @endif

                                        <small>Enviado em {{ $comentario->created_at->format('d/m/Y H:i') }}</small>

                                        <a href="{{ route('post.read', ['postagem' => $comentario->postagem->id]) }}"
                                            class="ver-post-link">
                                            Ver postagem
                                        </a>
                                    </div>
                                </div>

                                <!-- Modal Edição dessa postagem -->
                                @include('responsavel.editComentario', ['comentario' => $comentario])

                                @endforeach

                                @else

                                <div class="no-content-message">
                                    <p>Nenhum comentário enviado ainda.</p>
                                </div>

                                @endif
                            </div>

                        </div>



                        <div class="tab-content" id="autista-tab">
                            @if(isset($selectedAutista) && $selectedAutista)
                            @include('responsavel.dados-autista-responsavel', ['autista' => $selectedAutista])
                            @endif
                        </div>


                        @endif

                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Controle das abas
                        const tabButtons = document.querySelectorAll('.tab-button');
                        const tabContents = document.querySelectorAll('.tab-content');
                        const tabsWrapper = document.querySelector('.profile-tabs-wrapper');
                        const prevBtn = document.querySelector('.tab-scroll-prev');
                        const nextBtn = document.querySelector('.tab-scroll-next');

                        tabButtons.forEach(button => {
                            button.addEventListener('click', function () {
                                const tabId = this.getAttribute('data-tab');
                                // Remove classe active de todos os botões e conteúdos
                                tabButtons.forEach(btn => btn.classList.remove('active'));
                                tabContents.forEach(content => content.classList.remove(
                                    'active'));
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
                            nextBtn.style.display = scrollLeft < (scrollWidth - clientWidth - 10) ? 'flex' :
                                'none';

                            // Ativa/desativa botões
                            prevBtn.disabled = scrollLeft <= 0;
                            nextBtn.disabled = scrollLeft >= (scrollWidth - clientWidth - 10);
                        }

                        // Eventos dos botões de scroll
                        if (prevBtn && nextBtn && tabsWrapper) {
                            prevBtn.addEventListener('click', () => {
                                tabsWrapper.scrollBy({
                                    left: -200,
                                    behavior: 'smooth'
                                });
                            });
                            nextBtn.addEventListener('click', () => {
                                tabsWrapper.scrollBy({
                                    left: 200,
                                    behavior: 'smooth'
                                });
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

                        document.addEventListener('DOMContentLoaded', function () {
                            const likesTabButtons = document.querySelectorAll('.likes-tab-button');
                            const likesTabContents = document.querySelectorAll('.likes-tab-content');

                            likesTabButtons.forEach(button => {
                                button.addEventListener('click', function () {
                                    const tabId = this.getAttribute('data-likes-tab');

                                    // Remove classe active de todos os botões e conteúdos
                                    likesTabButtons.forEach(btn => btn.classList.remove(
                                        'active'));
                                    likesTabContents.forEach(content => content
                                        .classList
                                        .remove('active'));

                                    // Adiciona classe active ao botão clicado e conteúdo correspondente
                                    this.classList.add('active');
                                    const targetContent = document.getElementById(
                                        `${tabId}-likes-content`);
                                    if (targetContent) {
                                        targetContent.classList.add('active');
                                    }
                                });
                            });
                        });

                        // Recalcula scroll quando mudar de aba
                        tabButtons.forEach(button => {
                            button.addEventListener('click', scrollToActiveTab);
                        });
                    });

                </script>

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
