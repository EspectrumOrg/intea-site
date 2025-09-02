<div class="content">
    <div class="titulo">
        <h5 class="mb-4">Informações Usuário</h5>
    </div>

    <div class="links">
        @can("visualizar-admin")
        <a href="{{ route('usuario.index') }}" class="nav-link mb-2">Admin</a>
        @endcan

        @if (Auth::user()->tipo_usuario === 4)
        <a href="{{ route('pagina_saude') }}" class="nav-link mb-2">Profissional</a>
        @else
        <a href="" class="nav-link mb-2">Especialistas</a>
        @endif

        <a href="{{ route('post.index') }}" class="nav-link mb-2">Feed</a>
        <a href="" class="nav-link mb-2">Conversas</a>
        <a href="" class="nav-link mb-2">Seguindo</a>
        <a href="" class="nav-link mb-2">Configurações</a>
    </div>
</div>