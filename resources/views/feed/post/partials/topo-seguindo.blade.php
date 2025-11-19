    <!-- layout geral -->

<!-- Topo seguindo antigo
    <link rel="stylesheet" href="{{ url('assets/css/layout/topo-seguindo.css') }}">

    <div class="topo-home-opcoes">
        <a href="{{ route('post.index') }}"
            class="aba-postagem {{ request()->routeIs('post.index') ? 'selecionado' : '' }}"
            id="home">
            <h1>Para você</h1>
        </a>

        <a href="{{ route('post.seguindo') }}"
            class="aba-postagem {{ request()->routeIs('post.seguindo') ? 'selecionado' : '' }}"
            id="seguindo">
            <h1>Seguindo</h1>
        </a>
    </div>

-->

    <div class="seguindo-container">
    <div class="feed-navigation">
        <a href="{{ route('post.index') }}" class="nav-feed {{ request()->routeIs('post.index') ? 'active' : '' }}">
            <span class="material-symbols-outlined">public</span>
            Principal
        </a>
        <a href="{{ route('post.seguindo') }}" class="nav-feed {{ request()->routeIs('post.seguindo') ? 'active' : '' }}">
            <span class="material-symbols-outlined">group</span>
            Seguindo
        </a>
        @if(isset($interessesUsuario) && $interessesUsuario->count() > 0)
        <a href="{{ route('post.personalizado') }}" class="nav-feed {{ request()->routeIs('post.personalizado') ? 'active' : '' }}">
            Personalizado
        </a>
        @endif
        @if(isset($interessesUsuario))
            @foreach($interessesUsuario as $interesse)
            <a href="{{ route('post.interesse', $interesse->slug) }}" 
               class="nav-feed {{ request()->routeIs('post.interesse') && request()->route('slug') == $interesse->slug ? 'active' : '' }}" 
               style="color: {{ $interesse->cor }}">
                <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                {{ $interesse->nome }}
            </a>
            @endforeach
        @endif
    </div>
</div>