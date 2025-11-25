@extends('feed.post.template.layout')

@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Descobrir Interesses</h1>
        <p>Encontre comunidades que combinam com você</p>

        <!-- Barra de pesquisa e botão criar -->
        <div class="interesses-actions">
            <form action="{{ route('interesses.pesquisar') }}" method="GET" class="pesquisa-interesses-form">
                <div class="search-container">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar interesses..." class="search-input" required>
                    <button type="submit" class="search-btn">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                </div>
            </form>

            <a href="{{ route('interesses.create') }}" class="btn-criar-interesse">
                <span class="material-symbols-outlined">add</span>
                Criar Interesse
            </a>
        </div>
    </div>

    <!-- Contador de Interesses -->
    <div class="interesses-count">
        <span class="count-badge">
            <span class="material-symbols-outlined">tag</span>
            {{ $interesses->total() }} interesses disponíveis
        </span>
    </div>

    <!-- Interesses que você segue -->
    @if(isset($interessesUsuario) && count($interessesUsuario) > 0)
    <section class="interesses-section">
        <h2 class="section-title">
            <span class="material-symbols-outlined">bookmark</span>
            Seus Interesses ({{ count($interessesUsuario) }})
        </h2>
        <div class="interesses-grid">
            @foreach($interesses as $interesse)
            @if(in_array($interesse->id, $interessesUsuario))
            <div class="interesse-card">
                <div class="interesse-header" style="background-color: {{ $interesse->cor }}20;">
                    <div class="interesse-icon" style="color: {{ $interesse->cor }};">
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

        @if($interesses->count() > 0)
        <div class="interesses-lista">
            @foreach($interesses as $index => $interesse)
            <div class="interesse-item">
                <div class="interesse-numero">{{ $index + 1 }}</div>
                <div class="interesse-info">
                    <div class="interesse-badge-mini" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                        <span class="material-symbols-outlined">{{ $interesse->icone }}</span>
                    </div>
                    <div class="interesse-detalhes">
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
                            @if($interesse->destaque)
                            <span class="badge-destaque">Destacado</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="interesse-actions">
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
                        Ver
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginação Funcional -->
        @if($interesses->hasPages() && $interesses->lastPage() > 1)
        <div style="margin: 3rem 0; text-align: center; padding: 2rem 0;">
            <div style="display: inline-flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center; align-items: center;">
                @for ($i = 1; $i <= $interesses->lastPage(); $i++)
                    @if ($i == $interesses->currentPage())
                    <span style="
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                width: 42px;
                                height: 42px;
                                background: #3b82f6;
                                color: white;
                                border-radius: 8px;
                                font-weight: bold;
                                font-size: 0.9rem;
                                box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
                            ">
                        {{ $i }}
                    </span>
                    @else
                    <a href="{{ $interesses->url($i) }}" style="
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                width: 42px;
                                height: 42px;
                                background: white;
                                color: #6b7280;
                                border: 2px solid #e5e7eb;
                                border-radius: 8px;
                                text-decoration: none;
                                font-weight: 600;
                                font-size: 0.9rem;
                                transition: all 0.3s ease;
                            ">
                        {{ $i }}
                    </a>
                    @endif
                    @endfor
            </div>
            <p style="margin: 1rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
                Página {{ $interesses->currentPage() }} de {{ $interesses->lastPage() }}
            </p>
        </div>
        @endif
        @else
        <div class="no-results">
            <span class="material-symbols-outlined">search_off</span>
            <h3>Nenhum interesse encontrado</h3>
            <p>Tente ajustar sua pesquisa ou criar um novo interesse</p>
            <a href="{{ route('interesses.create') }}" class="btn-criar-interesse">
                <span class="material-symbols-outlined">add</span>
                Criar Interesse
            </a>
        </div>
        @endif
    </section>
</div>

<!-- CSS para a nova lista enumerada -->
<style>
    .interesses-count {
        margin: 2rem 0;
        text-align: center;
    }

    .count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #f8fafc;
        border: 2px solid #e5e7eb;
        border-radius: 25px;
        font-weight: 600;
        color: #374151;
    }

    .interesses-lista {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .interesse-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .interesse-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .interesse-numero {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .interesse-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .interesse-badge-mini {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .interesse-detalhes {
        flex: 1;
    }

    .interesse-detalhes h3 {
        font-size: 1.1rem;
        margin: 0 0 0.25rem 0;
        color: #1f2937;
        font-weight: 600;
    }

    .interesse-desc {
        color: #6b7280;
        margin: 0 0 0.5rem 0;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .interesse-meta {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .badge-destaque {
        background: #f59e0b;
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .interesse-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .btn-seguir-interesse {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .btn-seguir-interesse {
        background: #3b82f6;
        color: white;
    }

    .btn-seguir-interesse:hover {
        background: #2563eb;
    }

    .btn-seguir-interesse.seguindo {
        background: #10b981;
    }

    .btn-saiba-mais {
        padding: 0.5rem 1rem;
        background: #f8fafc;
        color: #64748b;
        border-radius: 6px;
        text-decoration: none;
        text-align: center;
        font-weight: 500;
        font-size: 0.8rem;
        transition: background 0.3s ease;
        white-space: nowrap;
    }

    .btn-saiba-mais:hover {
        background: #e5e7eb;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .interesse-item {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .interesse-info {
            flex-direction: column;
            gap: 0.75rem;
        }

        .interesse-actions {
            justify-content: center;
        }

        .interesse-meta {
            justify-content: center;
        }
    }
</style>

<!-- Incluir o JavaScript externo -->
<script src="{{ asset('assets/js/interesses/seguir-interesse.js') }}"></script>
@endsection