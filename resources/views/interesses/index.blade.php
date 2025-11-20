@extends('feed.post.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">

<div class="container-post">
    <div class="interesses-page-header">
        <h1>Descobrir Interesses</h1>
        <p>Encontre comunidades que combinam com você</p>
    </div>

    <!-- Interesses que você segue -->
    @if(isset($interessesUsuario) && count($interessesUsuario) > 0)
    <section class="interesses-section">
        <h2 class="section-title">
            <span class="material-symbols-outlined">bookmark</span>
            Seus Interesses
        </h2>
        <div class="interesses-grid">
            @foreach($interesses as $interesse)
                @if(in_array($interesse->id, $interessesUsuario))
                <div class="interesse-card-large">
                    <div class="interesse-header" style="background-color: {{ $interesse->cor }}20;">
                        <div class="interesse-icon-large" style="color: {{ $interesse->cor }};">
                            <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                        </div>
                        <h3>{{ $interesse->nome }}</h3>
                    </div>
                    <div class="interesse-body">
                        <p>{{ $interesse->descricao }}</p>
                        <div class="interesse-stats">
                            <span class="stat">
                                <span class="material-symbols-outlined">people</span>
                                {{ $interesse->seguidores_count }} seguidores
                            </span>
                            <span class="stat">
                                <span class="material-symbols-outlined">chat</span>
                                {{ $interesse->contador_postagens }} postagens
                            </span>
                        </div>
                    </div>
                    <div class="interesse-actions">
                        <a href="{{ route('post.interesse', $interesse->slug) }}" class="btn-visitar-interesse">
                            Ver Feed
                        </a>
                        <button class="btn-deixar-seguir" data-interesse-id="{{ $interesse->id }}">
                            Deixar de Seguir
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    <!-- Todos os Interesses -->
    <section class="interesses-section">
        <h2 class="section-title">
            <span class="material-symbols-outlined">explore</span>
            Todos os Interesses
        </h2>
        
        <!-- Filtros -->
        <div class="interesses-filters">
            <button class="filter-btn active" data-filter="all">Todos</button>
            <button class="filter-btn" data-filter="destaque">Destacados</button>
            <button class="filter-btn" data-filter="popular">Populares</button>
        </div>

        <div class="interesses-grid-full">
            @foreach($interesses as $interesse)
            <div class="interesse-card-full" data-category="{{ $interesse->destaque ? 'destaque' : 'normal' }}" data-popular="{{ $interesse->seguidores_count > 10 ? 'popular' : 'normal' }}">
                <div class="interesse-badge" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                    <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                </div>
                
                <div class="interesse-content-full">
                    <h3>{{ $interesse->nome }}</h3>
                    <p class="interesse-desc">{{ $interesse->descricao }}</p>
                    
                    <div class="interesse-meta">
                        <span class="meta-item">
                            <span class="material-symbols-outlined">people</span>
                            {{ $interesse->seguidores_count }} seguidores
                        </span>
                        <span class="meta-item">
                            <span class="material-symbols-outlined">chat</span>
                            {{ $interesse->contador_postagens }} postagens
                        </span>
                    </div>
                    
                    @if($interesse->destaque)
                    <span class="badge-destaque">Destacado</span>
                    @endif
                </div>
                
                <div class="interesse-actions-full">
                    @if(in_array($interesse->id, $interessesUsuario))
                        <button class="btn-seguir-interesse seguindo" data-interesse-id="{{ $interesse->id }}">
                            <span class="material-symbols-outlined">check</span>
                            Seguindo
                        </button>
                    @else
                        <button class="btn-seguir-interesse" data-interesse-id="{{ $interesse->id }}">
                            <span class="material-symbols-outlined">add</span>
                            Seguir
                        </button>
                    @endif
                    
                    <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn-saiba-mais">
                        Saiba Mais
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($interesses->hasPages())
        <div class="interesses-pagination">
            {{ $interesses->links() }}
        </div>
        @endif
    </section>
</div>

<!-- Incluir o JavaScript externo -->
<script src="{{ asset('assets/js/interesses/seguir-interesse.js') }}"></script>
@endsection