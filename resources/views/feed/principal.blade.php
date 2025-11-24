@extends('feed.post.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">

<div class="container-post">
    <!-- Header do Feed Principal -->
    <div class="feed-header principal">
        <div class="feed-info">
            <span class="material-symbols-outlined">public</span>
            <span>Feed Principal - Todas as postagens</span>
        </div>
        
        <!-- Navegação Rápida para Interesses -->
        @if($interessesUsuario->count() > 0)
        <div class="interesses-rapidos">
            <span>Seus feeds:</span>
            <div class="interesses-lista">
                <a href="{{ route('feed.personalizado') }}" class="interesse-rapido">
                    <span class="material-symbols-outlined">blend</span>
                    Personalizado
                </a>
                @foreach($interessesUsuario as $interesse)
                <a href="{{ route('feed.interesse', $interesse->slug) }}" 
                   class="interesse-rapido" 
                   style="color: {{ $interesse->cor }}">
                    <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                    {{ $interesse->nome }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Criar Postagem -->
    <div class="create-post">
        @include("feed.post.create")
    </div>

    <!-- Lista de Postagens -->
    <div class="content-post">
        @if($postagens->isEmpty())
        <div class="empty-feed">
            <span class="material-symbols-outlined">feed</span>
            <h3>Nenhuma postagem encontrada</h3>
            <p>Seja o primeiro a postar!</p>
        </div>
        @else
            @foreach($postagens as $postagem)
            <div class="corpo-post">
                <!-- Badge de Interesses da Postagem -->
                @if($postagem->interesses->count() > 0)
                <div class="post-interesses">
                    @foreach($postagem->interesses->take(2) as $interesse)
                    <a href="{{ route('feed.interesse', $interesse->slug) }}" 
                       class="interesse-badge-mini" 
                       style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                        <span class="material-symbols-outlined" style="font-size: 14px;">{{ $interesse->icone }}</span>
                        {{ $interesse->nome }}
                    </a>
                    @endforeach
                    @if($postagem->interesses->count() > 2)
                    <span class="mais-interesses">+{{ $postagem->interesses->count() - 2 }}</span>
                    @endif
                </div>
                @endif

                <!-- Código de postagem -->
                <a href="{{ route('post.read', ['postagem' => $postagem->id]) }}" class="post-overlay"></a>
                
                <div class="foto-perfil">
                    <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                        <img
                            src="{{ $postagem->usuario->foto ? url('storage/' . $postagem->usuario->foto) : asset('assets/images/logos/contas/user.png') }}"
                            alt="foto de perfil"
                            style="border-radius: 50%; object-fit:cover;"
                            width="40"
                            height="40"
                            loading="lazy">
                    </a>
                </div>

                <div class="corpo-content">
                    <div class="topo">
                        <div class="info-perfil">
                            <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                                <h1>{{ Str::limit($postagem->usuario->apelido ?? 'Desconhecido', 25, '...') }}</h1>
                            </a>
                            <h2>{{ $postagem->usuario->user }} . {{ $postagem->created_at->shortAbsoluteDiffForHumans() }}</h2>
                        </div>
                        <!-- Dropdown opções -->
                    </div>

                    <div class="conteudo-post">
                        <div class="coment-perfil">
                            <p>{{ $postagem->texto_postagem }}</p>
                        </div>
                        <!-- Imagens e ações -->
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Paginação -->
            <div class="paginacao">
                {{ $postagens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection