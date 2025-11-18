<div class="content">
    @can("visualizar-admin")

    <div class="admin-topo">
        <img src="{{ Auth::user()->foto ? url('storage/' . Auth::user()->foto) : url('assets/images/logos/contas/user.png') }}"
            class="foto-administrador">
        <div class="text">
            <h2>@if(Auth::user()->id === 1) Administrador Chefe @else Administrador Secundario @endif</h2>
            <h1>{{ Auth::user()->apelido}} - {{ Auth::user()->user}}</h1>
        </div>
    </div>

    <div class="links">
        <a  href="{{ route('dashboard.index')}}" 
            class="nav-link azul {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" 
            id="dashboard">
            <span class="material-symbols-outlined">monitoring</span>
            <h1>Dashboard</h1>
        </a>

        <a  href="{{ route('usuario.index')}}" 
            class="nav-link verde {{ request()->routeIs('usuario.index') ? 'active' : '' }}" 
            id="usuario">
            <span class="material-symbols-outlined">groups</span>
            <h1>Usuários</h1>
        </a>

        <a href="{{ route('contato.index')}}" 
            class="nav-link amarelo {{ request()->routeIs('contato.index') ? 'active' : '' }}" 
            id="suporte">
            <span class="material-symbols-outlined">contact_support</span>
            <h1>Suporte</h1>
        </a>

        <a href="{{ route('denuncia.index')}}" 
            class="nav-link vermelho {{ request()->routeIs('denuncia.index') ? 'active' : '' }}" 
            id="denuncia">
            <span class="material-symbols-outlined">flag_2</span>
            <h1>Denúncias</h1>
        </a>

        <a href="{{ route('post.index') }}" 
        class="nav-link vermelho {{ request()->routeIs('post.index') ? 'active' : '' }}" 
        id="feed">
            <span class="material-symbols-outlined">arrow_back</span>
            <h1>Voltar</h1>
        </a>
    </div>
    @endcan
</div>