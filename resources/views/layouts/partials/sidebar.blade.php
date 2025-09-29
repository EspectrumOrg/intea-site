<div class="content">
    <div class="links">
        <div class="logo">
            <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        </div>

        <a href="{{ route('post.index') }}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}" id="home">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('post.index') ? 'home-preenchido.png' : 'home.png')) }}" alt="Home">
            <h1>Home</h1>
        </a>

        <a href="{{ route('conta.index', [Auth::user()->id]) }}" class="nav-link" id="message">
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

        <!--@if (Auth::user()->tipo_usuario === 4)
        <a href="{{ route('pagina_saude') }}" class="nav-link">
            <h1>Profissionais</h1>
        </a>
        @else
        <a href="" class="nav-link">
            <h1>Especialistas</h1>
        </a>
        @endif-->

        @can("visualizar-admin")
        <a href="{{ route('usuario.index') }}" class="nav-link">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/admin.png') }}" />
            <h1>Admin</h1>
        </a>
        @endcan

        <!-- Modal de criação de postagem -->
        <div class="post-button">
            <button type="button" id="postagem-modal" onclick="abrirModalPostar()">Postar</button>
        </div>
    </div>

    <div class="info dropdown-container" id="userDropdown">
        <a href="#"><img src="{{ asset('storage/'. Auth::user()->foto) }}"></a>
        <div class="text">
            <h5>{{ Auth::user()->user }}</h5>
            <h4>{{ Auth::user()->email }}</h4>
        </div>

        <ul class="dropdown-checar-perfil hidden">
            <li id="li-checar-perfil-siedebar-01"><a href="{{ route('profile.edit') }}">Checar perfil</a></li>
            <li id="li-checar-perfil-siedebar-02">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        href="#">Sair {{ Auth::user()->user}}</a>
                </form>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dropdownContainer = document.getElementById("userDropdown");
            const dropdownMenu = dropdownContainer.querySelector(".dropdown-checar-perfil");

            // Abre/fecha ao clicar na área .info
            dropdownContainer.addEventListener("click", (e) => {
                e.stopPropagation(); // evita fechar ao clicar dentro
                dropdownMenu.classList.toggle("hidden");
            });

            // Fecha ao clicar fora
            document.addEventListener("click", () => {
                dropdownMenu.classList.add("hidden");
            });
        });
    </script>


</div>