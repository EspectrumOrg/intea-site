@extends('feed.post.template.layout')

@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Pesquisar Interesses</h1>
        <p>Resultados para: "{{ $query }}"</p>

        <!-- Barra de pesquisa -->
        <form action="{{ route('interesses.pesquisar') }}" method="GET" class="pesquisa-interesses-form">
            <div class="search-container">
                <input type="text" name="q" value="{{ $query }}" placeholder="Pesquisar interesses..."
                    class="search-input" required>
                <button type="submit" class="search-btn">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </div>
        </form>
    </div>

    @if($interesses->count() > 0)
    <section class="interesses-section">
        <h2 class="section-title">
            <span class="material-symbols-outlined">search</span>
            {{ $interesses->total() }} resultado(s) encontrado(s)
        </h2>

        <div class="interesses-grid-full">
            @foreach($interesses as $interesse)
            <div class="interesse-card-full">
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
                        Ver Interesse
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($interesses->hasPages())
        <div class="interesses-pagination">
            {{ $interesses->appends(['q' => $query])->links() }}
        </div>
        @endif
    </section>
    @else
    <div class="no-results">
        <span class="material-symbols-outlined">search_off</span>
        <h3>Nenhum interesse encontrado</h3>
        <p>Não encontramos resultados para "{{ $query }}"</p>
        <a href="{{ route('interesses.create') }}" class="btn-criar-interesse">
            <span class="material-symbols-outlined">add</span>
            Criar este interesse
        </a>
    </div>
    @endif
</div>
@endsection