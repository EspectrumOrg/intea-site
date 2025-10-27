<!-- icons and style -->
<link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />


<div class="content">

    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('landpage') }}">
            <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        </a>
    </div>

    <!-- User info -->
    <div class="info dropdown-container" id="userDropdown">
        <a href="#">
            <img
                src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : asset('assets/images/logos/contas/user.png') }}"
                alt="foto de perfil"
                style="border-radius: 50%;"
                width="40"
                height="40"
                loading="lazy">
        </a>
        <div class="text">
            <h5>{{ Auth::user()->user }}</h5>
            <h4>{{ Auth::user()->email }}</h4>
        </div>

        <ul class="dropdown-checar-perfil hidden">
            <li id="li-checar-perfil-siedebar-01"><a href="{{ route('profile.edit') }}">Checar perfil</a></li>
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

    <!-- Links sidebar-->
    <div class="links">
        <a href="{{ route('post.index') }}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}"
            id="home">
            <span class="material-symbols-outlined">home</span>
            <h1>Home</h1>
        </a>

        <a href="{{ route('chat.dashboard') }}" class="nav-link {{ request()->routeIs('chat.dashboard') ? 'active' : '' }}" id="message">
            <span class="material-symbols-outlined">mail</span>
            <h1>Mensagens</h1>
        </a>

        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"
            id="profile">
            <span class="material-symbols-outlined">person</span>
            <h1>Perfil</h1>
        </a>

        <a href="{{ route('grupo.index') }}" class="nav-link {{ request()->routeIs('grupo.index') ? 'active' : '' }}"
            id="config">
            <span class="material-symbols-outlined">group</span>
            <h1>Grupos</h1>
        </a>

        <a href="{{ route('configuracao.config') }}"
            class="nav-link {{ request()->routeIs('configuracao.config') ? 'active' : '' }}">
            <span class="material-symbols-outlined">settings</span>
            <h1>Configurações</h1>
        </a>



        @can("visualizar-admin")
        <a href="{{ route('dashboard.index') }}"
            class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <span class="material-symbols-outlined">manage_accounts</span>
            <h1>Administração</h1>
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