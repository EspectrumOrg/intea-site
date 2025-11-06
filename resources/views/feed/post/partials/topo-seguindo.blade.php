    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/topo-seguindo.css') }}">

    <div class="topo-home-opcoes">
        <a href="{{ route('post.index') }}"
            class="aba-postagem {{ request()->routeIs('post.index') ? 'selecionado' : '' }}"
            id="home">
            <h1>Home</h1>
        </a>

        <a href="{{ route('post.seguindo') }}"
            class="aba-postagem {{ request()->routeIs('post.seguindo') ? 'selecionado' : '' }}"
            id="seguindo">
            <h1>Seguindo</h1>
        </a>
    </div>