@extends('feed.post.template.layout')


@section('main')
<link rel="stylesheet" href="{{ url('assets/css/tendencias/style.index.css') }}">
<div class="tendencias-container">
    <!-- Cabeçalho da Página -->
    <div class="tendencias-header">
        <div class="tendencias-info">
            <h1> Todas as Tendências</h1>
            <p class="tendencias-subtitle">Descubra o que está sendo comentado na comunidade</p>
        </div>
    </div>

    <!-- Barra de Pesquisa -->
    <div class="search-container">
        <form method="GET" action="{{ route('tendencias.index') }}" class="search-form" id="searchForm">
            <div class="search-input-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Pesquisar tendências..."
                    value="{{ request('search') }}"
                    class="search-input"
                    autocomplete="off"
                >
                @if(request('search'))
                    <button type="button" class="clear-search" id="clearSearch">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                @endif
                <button type="submit" class="search-btn">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </div>
        </form>

        <!-- Resultados em tempo real -->
        <div class="search-results" id="searchResults"></div>
    </div>

    <!-- Informações de Resultados -->
    <div class="results-info">
        @if(request('search'))
            <div class="search-results-header">
                <h3>Resultados para: "{{ request('search') }}"</h3>
                <a href="{{ route('tendencias.index') }}" class="clear-results">
                    <span class="material-symbols-outlined">close</span>
                    Limpar pesquisa
                </a>
            </div>
        @endif
        
        <div class="results-count">
            <span>{{ $tendencias->total() }} tendência(s) encontrada(s)</span>
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
            {{ $tendencias->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Mensagem quando não há tendências -->
    @if($tendencias->count() == 0)
        <div class="no-tendencias">
            <div class="no-tendencias-content">
                <span class="material-symbols-outlined">
                    @if(request('search'))
                        search_off
                    @else
                        trending_up
                    @endif
                </span>
                <h3>
                    @if(request('search'))
                        Nenhuma tendência encontrada para "{{ request('search') }}"
                    @else
                        Nenhuma tendência encontrada
                    @endif
                </h3>
                <p>
                    @if(request('search'))
                        Tente usar termos diferentes ou verifique a ortografia.
                    @else
                        Ainda não há hashtags sendo usadas na comunidade. Seja o primeiro a criar uma tendência!
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('tendencias.index') }}" class="btn-primary">
                        Ver todas as tendências
                    </a>
                @else
                    <a href="{{ route('post.index') }}" class="btn-primary">
                        Criar primeiro post
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- JavaScript para busca em tempo real -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const clearSearch = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    
    let searchTimeout;

    // Limpar pesquisa
    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            searchForm.submit();
        });
    }

    // Busca em tempo real
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/tendencias/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data.tendencias);
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                });
        }, 300);
    });

    // Fechar resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Exibir resultados da busca
    function displaySearchResults(tendencias) {
        if (tendencias.length === 0) {
            searchResults.innerHTML = `
                <div class="no-results">
                    <span class="material-symbols-outlined">search_off</span>
                    <p>Nenhuma tendência encontrada</p>
                </div>
            `;
        } else {
            searchResults.innerHTML = tendencias.map(tendencia => `
                <a href="/tendencias/${tendencia.slug}" class="search-result-item">
                    <span class="result-hashtag">${tendencia.hashtag}</span>
                    <span class="result-count">${tendencia.contador_uso} posts</span>
                </a>
            `).join('');
        }
        searchResults.style.display = 'block';
    }

    // Submeter form ao pressionar Enter
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
});
</script>
@endsection