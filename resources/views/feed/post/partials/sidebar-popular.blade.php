<!-- resources/views/feed/post/partials/sidebar-popular.blade.php -->
<link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">

<div class="sidebar-tendencias">
    <div class="sidebar-header">
        <h3>🔥 O que está bombando</h3>
    </div>

    <div class="tendencias-list">
        @isset($tendenciasPopulares)
            @foreach($tendenciasPopulares as $tendencia)
                <a href="{{ route('tendencias.show', $tendencia->slug) }}" class="tendencia-item">
                    <div class="tendencia-content">
                        <span class="tendencia-nome">{{ $tendencia->hashtag }}</span>
                        <span class="tendencia-contador">{{ $tendencia->contador_uso }} posts</span>
                    </div>
                </a>
            @endforeach
            
            @if($tendenciasPopulares->count() == 0)
                <div class="no-tendencias">
                    <p>Nenhuma tendência no momento</p>
                </div>
            @endif
        @else
            <div class="no-tendencias">
                <p>Carregando tendências...</p>
            </div>
        @endisset
    </div>
</div>