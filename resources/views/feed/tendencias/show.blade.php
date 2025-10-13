@extends('feed.post.template.layout')

@section('main')
<link rel="stylesheet" href="{{ url('assets/css/tendencias/style.show.css') }}">
<div class="tendencia-container">
    <!-- Cabeçalho da Tendência -->
    <div class="tendencia-header">
        <div class="tendencia-info">
            <h1>{{ $tendencia->hashtag }}</h1>
            <p class="tendencia-stats">
                {{ $tendencia->contador_uso }} posts • 
                Último uso: {{ $tendencia->ultimo_uso->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="tendencia-actions">
            <a href="{{ route('tendencias.index') }}" class="btn-voltar">
                <span class="material-symbols-outlined">arrow_back</span>
                Ver todas as tendências
            </a>
        </div>
    </div>

    <!-- Lista de Postagens da Tendência -->
    <div class="posts-feed">
        @foreach($postagens as $post)
            <div class="post-card">
                <!-- Cabeçalho do Post -->
                <div class="post-header">
                    <div class="user-info">
                        <div class="user-avatar">
                            @if($post->usuario->foto)
                                <img src="{{ asset('storage/'.$post->usuario->foto) }}" 
                                     alt="{{ $post->usuario->nome }}">
                            @else
                                <img src="{{ url('assets/images/logos/contas/user.png') }}" 
                                     alt="Usuário">
                            @endif
                        </div>
                        <div class="user-details">
                            <strong>{{ $post->usuario->nome }}</strong>
                            <small>{{ $post->usuario->user }}</small>
                            <small class="post-time">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo do Post -->
                <div class="post-content">
                    @php
                        // Processar hashtags no texto
                        $textoComHashtags = preg_replace(
                            '/#(\w+)/', 
                            '<span class="hashtag">#$1</span>', 
                            $post->texto_postagem
                        );
                    @endphp
                    {!! $textoComHashtags !!}
                </div>

                <!-- Imagens do Post -->
                @if($post->imagens && $post->imagens->count() > 0)
                    <div class="post-images">
                        @foreach($post->imagens as $imagem)
                            <img src="{{ asset('storage/'.$imagem->caminho_imagem) }}" 
                                 alt="Imagem do post" 
                                 class="post-image">
                        @endforeach
                    </div>
                @endif

                <!-- Estatísticas do Post -->
                <div class="post-stats">
                    <span class="curtidas">❤️ {{ $post->curtidas_count ?? 0 }} curtidas</span>
                    <span class="comentarios">💬 {{ $post->comentarios_count ?? 0 }} comentários</span>
                </div>

                <!-- Ações do Post -->
                <div class="post-actions">
                    <a href="{{ route('post.show', $post->id) }}" class="btn-action">
                        Ver post completo
                    </a>
                </div>
            </div>
        @endforeach

        @if($postagens->count() == 0)
            <div class="no-posts">
                <div class="no-posts-content">
                    <span class="material-symbols-outlined">tag</span>
                    <h3>Nenhum post encontrado</h3>
                    <p>Esta tendência ainda não tem posts. Seja o primeiro a usar {{ $tendencia->hashtag }}!</p>
                    <a href="{{ route('post.index') }}" class="btn-primary">
                        Criar primeiro post
                    </a>
                </div>
            </div>
        @endif

        <!-- Paginação -->
        @if($postagens->hasPages())
            <div class="pagination-container">
                {{ $postagens->links() }}
            </div>
        @endif
    </div>
</div>

@endsection