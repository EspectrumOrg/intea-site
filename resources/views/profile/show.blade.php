<!doctype html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - perfil</title>
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

    @php
    use App\Models\Tendencia;

    if (!function_exists('formatarHashtags')) {
    function formatarHashtags($texto) {
    return preg_replace_callback(
    '/#(\w+)/u',
    function ($matches) {
    $tag = e($matches[1]);

    // Busca a hashtag no banco
    $tendencia = Tendencia::where('hashtag', '#'.$tag)->first();

    // Define a URL final (se existir a tendência no banco, vai pra rota certa)
    $url = $tendencia
    ? route('tendencias.show', $tendencia->slug)
    : url('/hashtags/' . $tag);

    // Retorna o link formatado
    return "<a href=\"{$url}\" class=\"hashtag\">#{$tag}</a>";
    },
    e($texto)
    );
    }
    }
    @endphp

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
                    @if(!isset($user) || is_null($user) || $user->status_conta == 2)
                    <div class="alert alert-danger text-center not-found">
                        <div class="text-not">
                            <span class="material-symbols-outlined">
                                error
                            </span>
                            <h4> Perfil não encontrado</h4>
                        </div>

                        <div class="subtext-not">
                            <p>O usuário que você está tentando acessar não existe ou foi banido.</p>
                        </div>

                        <div class="bottom-perfil-not">
                            <a href="/feed" class="btn-voltar-feed">Voltar para o Feed</a>
                        </div>
                    </div>
                    @else
                    <!-- Cabeçalho do perfil -->
                    <div class="profile-header">
                        <div class="foto-perfil">
                            @if (!empty($user->foto))
                            <img src="{{ asset('storage/'.$user->foto) }}" class="card-img-top" alt="foto perfil">
                            @else
                            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top"
                                alt="foto perfil">
                            @endif
                        </div>

                        <div class="profile-info">
                            <h1>{{ $user->apelido }}</h1>
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

                            <div class="profile-counts" style="display: flex; gap: 20px; margin-bottom: 10px;">
                                <div class="profile-counts" style="display: flex; gap: 20px; margin-bottom: 10px;">
                                    <div id="btnSeguindo" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguindo', ['id' => $user->id]) }}">
                                        <strong>{{ $user->seguindo()->count() }}</strong> Seguindo
                                    </div>

                                    <div id="btnSeguidores" style="cursor:pointer"
                                        data-url="{{ route('usuario.listar.seguidores', ['id' => $user->id]) }}">
                                        <strong>{{ $user->seguidores()->count() }}</strong> Seguidores
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
                            @php
                            $authUser = auth()->user();
                            $isFollowing = $authUser->seguindo->contains($user->id);
                            $pedidoFeito = \App\Models\Notificacao::where('solicitante_id', $authUser->id)
                            ->where('alvo_id', $user->id)
                            ->where('tipo', 'seguir')
                            ->exists();
                            @endphp

                            <div class="profile-action-buttons" style="margin-top: 10px; display: flex; gap: 10px;">

                                {{-- Botão Seguir / Pedir / Deixar de seguir --}}
                                @if($isFollowing)
                                <form action="{{ route('seguir.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="deixar-profile-btn">
                                        <span class="material-symbols-outlined">person_remove</span> Deixar de seguir
                                    </button>
                                </form>

                                @elseif($user->visibilidade == 0)
                                @if($pedidoFeito)
                                <form action="{{ route('seguir.cancelar', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="seguir-btn">
                                        <span class="material-symbols-outlined">hourglass_empty</span> Pedido enviado
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Pedir para seguir
                                    </button>
                                </form>
                                @endif

                                @else
                                <form action="{{ route('seguir.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="seguir-btn">
                                        <span class="material-symbols-outlined">person_add</span> Seguir
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('chat.dashboard') }}?usuario2={{ $user->id }}" class="btn-mensagem">
                                    <span class="material-symbols-outlined">message</span> Mensagem
                                </a>

                                <!-- Parte banir/denunciar usuário------------------>
                                @if( Auth::user()->tipo_usuario === 1 )
                                <div class="form-excluir">
                                    <button type="button" data-bs-toggle="modal" class="banir-btn"
                                        onclick="abrirModalBanimentoUsuarioEspecifico('{{ $user->id }}')">
                                        <span class="material-symbols-outlined">person_off</span>Banir {{$user->user}}
                                    </button>
                                </div>
                                @else
                                <a href="javascript:void(0)" class="btn-denuncia"
                                    onclick="abrirModalDenunciaUsuario('{{$user->id}}')">
                                    <span class="material-symbols-outlined">flag_2</span>Denunciar {{$user->user}}
                                </a>
                                @endif

                                <!-- modal banir-->
                                @include('layouts.partials.modal-banimento', ['usuario' => $user])

                                <!-- Modal de denúncia (um para cada usuário) -->
                                <div id="modal-denuncia-usuario-{{ $user->id }}" class="modal-denuncia hidden">
                                    <div class="modal-content">
                                        <h3 class="texto-next-close-button">Coletando informações</h3>
                                        <span class="close" onclick="fecharModalDenunciaUsuario('{{$user->id}}')">
                                            <span class="material-symbols-outlined">close</span>
                                        </span>

                                        <form method="POST" style="width: 100%;" action="{{ route('denuncia.store') }}">
                                            @csrf
                                            <input type="hidden" name="tipo" value="usuario">
                                            <input type="hidden" name="id_alvo" value="{{ $user->id }}">

                                            @include('layouts.partials.modal-denuncia')
                                        </form>
                                    </div>
                                </div>
                                <!--Fim parte banir/denunciar---------------------------------------->
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
                                @if($seguidores->count() > 0)
                                <button class="tab-button" data-tab="followers">
                                    <span class="material-symbols-outlined">group</span>
                                    <span class="tab-text">Seguidores ({{ $seguidores->count() }})</span>
                                </button>
                                @endif

                                <!-- Aba Seguindo -->
                                @if($seguindo->count() > 0)
                                <button class="tab-button" data-tab="following">
                                    <span class="material-symbols-outlined">person_add</span>
                                    <span class="tab-text">Seguindo ({{ $seguindo->count() }})</span>
                                </button>
                                @endif

                                <!-- Aba Configurações (apenas para o próprio usuário) -->
                                @if(auth()->id() == $user->id)
                                <button class="tab-button" data-tab="settings">
                                    <span class="material-symbols-outlined">settings</span>
                                    <span class="tab-text">Configurações</span>
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
                                    <strong>Apelido:</strong>
                                    <span>{{ $user->apelido ?? 'Não informado' }}</span>
                                </div>
                                <div class="info-item">
                                    <strong>Email:</strong>
                                    <span>{{ $user->email }}</span>
                                </div>

                                <div class="info-item">
                                    <strong>CPF:</strong>
                                    <span class="{{ $user->cpf === '•••••••••••' ? 'cpf-privado' : 'cpf-publico' }}">
                                        {{ $user->cpf }}
                                    </span>
                                    @if($user->cpf !== '•••••••••••' && auth()->check() && auth()->id() == $user->id)
                                    <small style="color: #666; margin-left: 5px;">(seu CPF)</small>
                                    @elseif($user->cpf === '•••••••••••')
                                    <small style="color: #666; font-style: italic; margin-left: 5px;">
                                        @auth
                                        Informação privada
                                        @else
                                        Faça login para ver
                                        @endauth
                                    </small>
                                    @endif
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
                            <br>
                            <!-- Botão que abre o modal -->
                            @if($user->tipo_usuario === 3 || $user->tipo_usuario === 2)
                            <button id="btnAbrirModalPerfil" class="abrir-modal-btn">
                                <span class="material-symbols-outlined">add_circle</span> Adicionar Dependente
                            </button>
                            @elseif($user->tipo_usuario === 5)
                            <button id="abrirModalRemover" class="abrir-modal-btn">
                                <span class="material-symbols-outlined">remove_circle</span> Retirar Dependente
                            </button>
                            @endif

                            <!-- Modal (inicialmente oculto) -->
                            <div id="modalPerfil" class="modal-overlay" style="display: none;">
                                <div class="modal-box">
                                    <h2>Editar Informações do Perfil</h2>
                                    <form id="addDependente" method="POST"
                                        action="{{ route('responsavel.adicionar_dependente', ['id' => $user->id]) }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="nome">Nome:</label>
                                            <input type="text" name="nome" id="nome"
                                                placeholder="Digite o nome do dependente" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="cpf">CPF:</label>
                                            <input type="text" name="cpf" id="cpf" placeholder="Digite o CPF" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="ciptea">CIPTEA:</label>
                                            <input type="text" name="ciptea" id="ciptea"
                                                placeholder="Digite o número CIPTEA">
                                        </div>
                                        <div class="modal-actions">
                                            <button type="button" id="fecharModalPerfil"
                                                class="btn-cancelar">Cancelar</button>
                                            <button type="submit" class="btn-salvar">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal para REMOVER Dependente (inicialmente oculto) -->
                            <div id="modalRemoverDependente" class="modal-overlay" style="display: none;">
                                <div class="modal-box">
                                    <h2>Remover Dependente</h2>
                                    <form id="removerDependenteForm" method="POST"
                                        action="{{ route('dependente.remover') }}">
                                        @csrf
                                        @method('DELETE')

                                        <select name="dependente_id" id="dependente_id" required>
                                            <option value="">-- Escolha um dependente --</option>
                                            @if($autista && $autista->count())
                                            @foreach($autista as $autistas)
                                            <option value="{{ $autista->id }}">
                                                {{ $autista->usuario->apelido ?? 'Sem nome' }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>

                                        <p class="alerta">
                                            ⚠️ Tem certeza que deseja remover este dependente? Essa ação não poderá ser
                                            desfeita.
                                        </p>

                                        <div class="modal-actions">
                                            <button type="button" id="fecharModalRemover"
                                                class="btn-cancelar">Cancelar</button>
                                            <button type="submit" class="btn-remover">Remover</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>

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
                                <p class="texto-curto" id="texto-{{ $post->id }}">
                                    {!! formatarHashtags(Str::limit($post->texto_postagem, 150, '')) !!}
                                    @if (strlen($post->texto_postagem) > 150)
                                    <span class="mostrar-mais" onclick="toggleTexto('{{ $post->id }}', this)">...mais</span>
                                    @endif
                                </p>

                                <p class="texto-completo" id="texto-completo-{{ $post->id }}" style="display: none;">
                                    {!! formatarHashtags($post->texto_postagem) !!}
                                    <span class="mostrar-mais" onclick="toggleTexto('{{ $post->id }}', this)">...menos</span>
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


<!-- Aba 3: Curtidas (sempre aparece — OPÇÃO B) -->
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
                    <img src="{{ asset('storage/'.$like->postagem->usuario->foto) }}" alt="{{ $like->postagem->usuario->apelido }}">
                    @else
                    <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                    @endif
                </div>

                <div class="like-content">
                    <strong>{{ $like->postagem->usuario->apelido }}</strong>
                    <p>{{ Str::limit($like->postagem->texto_postagem, 100) }}</p>
                    <small>Curtido em {{ $like->created_at->format('d/m/Y H:i') }}</small>

                    <a href="{{ route('post.read', ['postagem' => $like->postagem->id]) }}" class="ver-post-link">
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

<!-- Aba: Seguindo -->
<div class="tab-content" id="following-tab">
    <h3>Seguindo</h3>

    @if($seguindo->count() > 0)
        <div class="likes-list">
            @foreach($seguindo as $usuario)
            <div class="like-item">

                <div class="like-avatar">
                    @if($usuario->foto)
                <img src="{{ asset('storage/'.$usuario->foto) }}" class="card-img-top" alt="foto perfil">
                    @else
                        <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                    @endif
                </div>

                <div class="like-content">
                <p>{{ $usuario->user }}</p>

                    <a href="{{ route('profile.show', $usuario->id) }}" class="ver-post-link">
                        Ver perfil
                    </a>
                </div>

            </div>
            @endforeach
        </div>
    @else
        <div class="no-content-message">
            <p>Você não está seguindo ninguém ainda.</p>
        </div>
    @endif
</div>
<div class="tab-content" id="followers-tab">
    <h3>Seguidores</h3>

    @if($seguidores->count() > 0)
        <div class="likes-list">
            @foreach($seguidores as $usuario)
            <div class="like-item">

                <div class="like-avatar">
                    @if($usuario->foto)
                <img src="{{ asset('storage/'.$usuario->foto) }}" class="card-img-top" alt="foto perfil">
                    @else
                        <img src="{{ url('assets/images/logos/contas/user.png') }}" alt="Usuário">
                    @endif
                </div>

                <div class="like-content">
                      <p>{{ $usuario->user }}</p>

                    <a href="{{ route('profile.show', $usuario->id) }}" class="ver-post-link">
                        Ver perfil
                    </a>
                </div>

            </div>
            @endforeach
        </div>
    @else
        <div class="no-content-message">
            <p>Você ainda não tem seguidores.</p>
        </div>
    @endif
</div>
                      

                    <!-- Aba 4: Configurações (apenas para o próprio usuário) -->
                    @if(auth()->id() == $user->id)
                    <div class="tab-content" id="settings-tab">
                        <!-- Inclui os formulários de configurações -->
                        @include('profile.partials.update-profile-information-form')
                        @include('profile.partials.update-privacy-form')
                    </div>
                    @endif

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
        <!-- modal de avisos -->
        @include("layouts.partials.avisos")


        <!-- Modal Criação de postagem -->
        @include('feed.post.create-modal')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Controle das abas
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const tabsWrapper = document.querySelector('.profile-tabs-wrapper');
            const prevBtn = document.querySelector('.tab-scroll-prev');
            const nextBtn = document.querySelector('.tab-scroll-next');

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

            document.addEventListener('DOMContentLoaded', function() {
                const likesTabButtons = document.querySelectorAll('.likes-tab-button');
                const likesTabContents = document.querySelectorAll('.likes-tab-content');

                likesTabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-likes-tab');

                        // Remove classe active de todos os botões e conteúdos
                        likesTabButtons.forEach(btn => btn.classList.remove('active'));
                        likesTabContents.forEach(content => content.classList.remove('active'));

                        // Adiciona classe active ao botão clicado e conteúdo correspondente
                        this.classList.add('active');
                        const targetContent = document.getElementById(`${tabId}-likes-content`);
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