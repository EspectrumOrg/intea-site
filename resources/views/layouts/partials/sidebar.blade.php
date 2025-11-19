<!-- icons and style -->
<link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

<div
    class="content {{ Auth::check() && Auth::user()->tema_preferencia == 'monocromatico' ? 'sidebar-monochrome' : '' }}">

    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('landpage') }}">
            <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        </a>
    </div>

    <!-- User info -->
    <div class="info dropdown-container" id="userDropdown">
        <a href="#">
            <img src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                alt="foto de perfil" style="border-radius: 50%;" width="40" height="40" loading="lazy">
        </a>
        <div class="text">
            <h5>{{ Auth::user()->apelido }}</h5>
            <h4>{{ Auth::user()->user }}</h4>
        </div>

        <ul class="dropdown-checar-perfil hidden">
            <li id="li-checar-perfil-siedebar-01"><a href="{{ route('profile.show') }}">Checar perfil</a></li>
            <li id="li-checar-perfil-siedebar-02">
                <!-- Authentication -->
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <a onclick="event.preventDefault(); this.closest('form').submit();" href="#">Sair
                        {{ Auth::user()->user}}</a>
                </form>
            </li>
        </ul>
    </div>
    @php  
        $totalNotificacoes = \App\Models\Notificacao::where('alvo_id', auth()->id())
        ->whereIn('tipo', ['seguindo_voce', 'seguir', 'solicitacao_aceita', 'seguir'])
        ->count();
    @endphp

    <!-- Links sidebar-->
    <div class="links">
        <a href="{{ route('post.index') }}"
            class="nav-link {{ request()->routeIs('post.index') || request()->routeIs('post.seguindo') ? 'active' : '' }}"
            id="home">
            <span id="simbolo-home" class="material-symbols-outlined">home</span>
            <h1>Home</h1>
        </a>

        <!-- Movi para post/partials/topo-seguindo para ficar melhor distribuido e parecido com o twitter
        <a href="{{ route('post.seguindo') }}" class="nav-link {{ request()->routeIs('post.seguindo') ? 'active' : '' }}"
            id="seguindo">
            <span id="simbolo-home" class="material-symbols-outlined" >Home</span>
            <h1>Seguindo</h1>
        </a>
        -->

        <a href="{{ route('chat.dashboard') }}"
            class="nav-link {{ request()->routeIs('chat.dashboard') ? 'active' : '' }}" id="message">
            <span id="simbolo-mensagens" class="material-symbols-outlined">mail</span>
            <h1>Mensagens</h1>
        </a>

        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"
            id="profile">
            <span id="simbolo-perfil" class="material-symbols-outlined">person</span>
            <h1>Perfil</h1>
        </a>

        <a href="{{ route('notificacao.index') }}"
            class="nav-link {{ request()->routeIs('notificacao.index') ? 'active' : '' }}" id="config">

            <span class="material-symbols-outlined"> notifications </span>

            <h1 style="display: flex; align-items: center; gap: 6px;">
                Notificações

                @if($totalNotificacoes > 0)
                        <span style="
                        background: #14b814;
                        color: white;
                        padding: 2px 8px;
                        border-radius: 12px;
                        font-size: 12px;
                        font-weight: bold;
                    ">
                            {{ $totalNotificacoes }}
                        </span>
                @endif
            </h1>
        </a>

        <a href="{{ route('configuracao.config') }}"
            class="nav-link {{ request()->routeIs('configuracao.config') ? 'active' : '' }}">
            <span id="simbolo-config" class="material-symbols-outlined">settings</span>
            <h1>Configurações</h1>
        </a>



        @can("visualizar-admin")
            <a href="{{ route('dashboard.index') }}"
                class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <span class="material-symbols-outlined">manage_accounts</span>
                <h1>Administração</h1>
            </a>
        @endcan
        @can("visualizar-responsavel")
            <a href="{{ route('responsavel.painel') }}"
                class="nav-link {{ request()->routeIs('responsavel.painel') ? 'active' : '' }}">
                <span id="simbolo-responsavel" class="material-symbols-outlined">supervisor_account</span>
                <h1>Responsáveis</h1>
            </a>
        @endcan


        <!-- Modal de criação de postagem --------------------------------------------------------------------------->
        <div class="post-button">
            <button type="button" id="postagem-modal" onclick="abrirModalPostar()">Postar</button>
        </div>
    </div>
</div>

<!-- JS -->
<script src="{{ url('assets/js/avisos/sidebar-user.js') }}"></script>