@extends('feed.post.template.layout')


@section('main')
<link rel="stylesheet" href="{{ url('assets/css/tendencias/style.index.css') }}">
<div class="tendencias-container">
    <!-- Cabeçalho da Página -->
    <div class="tendencias-header">
        <div class="tendencias-info">
            <h1>🔥 Todas as Tendências</h1>
            <p class="tendencias-subtitle">Descubra o que está sendo comentado na comunidade</p>
        </div>
    </div>

    <!-- Lista de Tendências -->
    <div class="tendencias-grid">
        @foreach($tendencias as $tendencia)
            <div class="tendencia-card">
                <a href="{{ route('tendencias.show', $tendencia->slug) }}" class="tendencia-link">
                    <div class="tendencia-main">
                        <span class="tendencia-hashtag">{{ $tendencia->hashtag }}</span>
                        <span class="tendencia-count">{{ $tendencia->contador_uso }} posts</span>
                    </div>
                    <div class="tendencia-meta">
                        <small>Último uso: {{ $tendencia->ultimo_uso->format('d/m/Y H:i') }}</small>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Paginação -->
    @if($tendencias->hasPages())
        <div class="pagination-container">
            {{ $tendencias->links() }}
        </div>
    @endif

    <!-- Mensagem quando não há tendências -->
    @if($tendencias->count() == 0)
        <div class="no-tendencias">
            <div class="no-tendencias-content">
                <span class="material-symbols-outlined">trending_up</span>
                <h3>Nenhuma tendência encontrada</h3>
                <p>Ainda não há hashtags sendo usadas na comunidade. Seja o primeiro a criar uma tendência!</p>
                <a href="{{ route('post.index') }}" class="btn-primary">
                    Criar primeiro post
                </a>
            </div>
        </div>
    @endif
</div>

@endsection