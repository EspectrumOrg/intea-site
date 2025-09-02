<div class="content">
    @can("visualizar-admin")
    <div class="titulo">
        <h5 class="mb-4">Administração</h5>
    </div>

    <div class="links">
        <a href="" class="nav-link mb-2">Estatísticas</a>
        <a href="{{ route('usuario.index')}}" class="nav-link mb-2">Usuários</a>
        <a href="{{ route('denuncia.index')}}" class="nav-link mb-2">Denúncias</a>
        <a href="{{ route('post.index') }}" class="nav-link mb-2">Feed</a>
    </div>
    @endcan
</div>