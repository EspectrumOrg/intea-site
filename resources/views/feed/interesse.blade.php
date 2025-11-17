@extends('feed.post.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">

<div class="container-post">
    <!-- Header do Feed do Interesse -->
    <div class="feed-header interesse" style="border-left: 4px solid {{ $interesse->cor }};">
        <div class="interesse-header-info">
            <div class="interesse-avatar" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
            </div>
            <div class="interesse-info">
                <h1 class="interesse-titulo">{{ $interesse->nome }}</h1>
                <p class="interesse-descricao">{{ $interesse->descricao }}</p>
                <div class="interesse-stats-header">
                    <span class="stat">
                        <span class="material-symbols-outlined">people</span>
                        {{ $interesse->contador_membros }} seguidores
                    </span>
                    <span class="stat">
                        <span class="material-symbols-outlined">chat</span>
                        {{ $interesse->contador_postagens }} postagens
                    </span>
                </div>
            </div>
        </div>
        
        <div class="interesse-actions">
            <button class="btn-seguir {{ auth()->user()->segueInteresse($interesse->id) ? 'seguindo' : '' }}" 
                    data-interesse-id="{{ $interesse->id }}">
                {{ auth()->user()->segueInteresse($interesse->id) ? 'Seguindo' : 'Seguir' }}
            </button>
            <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn-info">
                <span class="material-symbols-outlined">info</span>
            </a>
        </div>
    </div>

    <!-- Navegação entre Feeds -->
    <div class="feed-navigation">
        <a href="{{ route('feed.principal') }}" class="nav-feed">
            <span class="material-symbols-outlined">public</span>
            Principal
        </a>
        @if($interessesUsuario->count() > 0)
        <a href="{{ route('feed.personalizado') }}" class="nav-feed">
            <span class="material-symbols-outlined">blend</span>
            Personalizado
        </a>
        @endif
        @foreach($interessesUsuario as $interesseUser)
        @if($interesseUser->id != $interesse->id)
        <a href="{{ route('feed.interesse', $interesseUser->slug) }}" 
           class="nav-feed" 
           style="color: {{ $interesseUser->cor }}">
            <span class="material-symbols-outlined">{{ $interesseUser->icone }}</span>
            {{ $interesseUser->nome }}
        </a>
        @endif
        @endforeach
    </div>

    <!-- Criar Postagem -->
    <div class="create-post">
        @include("feed.post.create")
    </div>

    <!-- Lista de Postagens do Interesse -->
    <div class="content-post">
        @if($postagens->isEmpty())
        <div class="empty-feed">
            <span class="material-symbols-outlined">tag</span>
            <h3>Nenhuma postagem em {{ $interesse->nome }}</h3>
            <p>Seja o primeiro a postar neste interesse!</p>
        </div>
        @else
            @foreach($postagens as $postagem)
            <div class="corpo-post">
                <!-- Seu código existente de postagem -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botão Seguir/Deixar de seguir
    const btnSeguir = document.querySelector('.btn-seguir');
    if (btnSeguir) {
        btnSeguir.addEventListener('click', function() {
            const interesseId = this.dataset.interesseId;
            const estaSeguindo = this.classList.contains('seguindo');
            
            const url = estaSeguindo 
                ? `/api/interesses/${interesseId}/deixar-seguir`
                : `/api/interesses/${interesseId}/seguir`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    if (estaSeguindo) {
                        this.classList.remove('seguindo');
                        this.textContent = 'Seguir';
                    } else {
                        this.classList.add('seguindo');
                        this.textContent = 'Seguindo';
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar ação');
            });
        });
    }
});
</script>
@endsection