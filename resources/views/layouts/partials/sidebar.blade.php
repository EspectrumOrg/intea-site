<div class="content">
    <div class="logo">
        <h5>Logo</h5>
    </div>

    <div class="info">
        <a href="#"><img src="{{ asset('storage/'. Auth::user()->foto) }}"></a>
        <div class="text">
            <h5>{{ Auth::user()->user}}</h5>
            <h4>{{ Auth::user()->email}}</h4>
        </div>
    </div>

    <div class="links">
        <a href="{{ route('post.index') }}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}" id="home">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('post.index') ? 'home-preenchido.png' : 'home.png')) }}" alt="Home">
            <h1>Home</h1>
        </a>

        <a href="{{ route('post.index') }}" class="nav-link" id="message">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/email.png') }}" alt="Mensagens">
            <h1>Mensagens</h1>
        </a>

        <a href="{{ route('profile.edit') }}"
            class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('profile.edit') ? 'user-preenchido.png' : 'user.png')) }}" alt="Perfil">
            <h1>Perfil</h1>
        </a>

        <a href="{{ route('post.index') }}" class="nav-link" id="config">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/gear.png') }}">
            <h1>Configurações</h1>
        </a>

        @if (Auth::user()->tipo_usuario === 4)
        <a href="{{ route('pagina_saude') }}" class="nav-link">
            <h1>Profissionais</h1>
        </a>
        @else
        <a href="" class="nav-link">
            <h1>Especialistas</h1>
        </a>
        @endif

        @can("visualizar-admin")
        <a href="{{ route('usuario.index') }}" class="nav-link">
            <h1>Admin</h1>
        </a>
        @endcan


        <ul class="ul">
            <li class="nav-item dropdown-item">
                @if (!empty(Auth::user()->foto))
                <a href="#"><img src="{{ asset('storage/'.Auth::user()->foto) }}"></a>
                @else
                <a href="#"><img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil"></a>
                @endif

                <ul class="dropdown">
                    <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link" href="#">Sair</a>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Modal de criação de postagem -->
    <div class="post-button">
        <button type="button" id="postagem-modal" onclick="abrirModalPostar()">Postar</button>
    </div>
</div>