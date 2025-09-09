<div class="content">
    @can("visualizar-admin")
    <div class="links">
        <div class="logo">
            <img src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        </div>

        <a href="" class="nav-link mb-2">Estatísticas</a>
        <a href="{{ route('usuario.index')}}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}" id="usuario">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('post.index') ? 'home-preenchido.png' : 'home.png')) }}" alt="usuarios">
            <h1>Usuários</h1>
        </a>
        <a href="{{ route('denuncia.index')}}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}" id="denuncia">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('post.index') ? 'home-preenchido.png' : 'home.png')) }}" alt="denuncias">
            <h1>Denúncias</h1>
        </a>

        <a href="{{ route('post.index') }}" class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}" id="feed">
            <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (request()->routeIs('post.index') ? 'home-preenchido.png' : 'home.png')) }}" alt="feed">
            <h1>Feed</h1>
        </a>
    </div>
    @endcan
</div>