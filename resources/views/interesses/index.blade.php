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
            <div class="pesquisa-interesses-container" style="position: relative; width: 100%; max-width: 500px;">
                <form action="{{ route('interesses.pesquisar') }}" method="GET" class="pesquisa-interesses-form">
                    <div class="search-container" style="position: relative;">
                        <input type="text" 
                               name="q" 
                               id="buscarInteresses" 
                               value="{{ request('q') }}" 
                               placeholder="Pesquisar interesses..." 
                               class="search-input" 
                               autocomplete="off"
                               onfocus="mostrarSugestoes()"
                               onblur="setTimeout(() => esconderSugestoes(), 200)">
                        <button type="submit" class="search-btn">
                            <span class="material-symbols-outlined">search</span>
                        </button>
                    </div>
                </form>
                
                <!-- Sugestões de busca -->
                <div id="sugestoesBusca" 
                     style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; max-height: 300px; overflow-y: auto; margin-top: 4px;">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>

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
                        @if($interesse->icone_custom)
                            <!-- SOLUÇÃO SIMPLES - Sem onerror complexo -->
                            <img src="{{ $interesse->icone }}" 
                                 alt="{{ $interesse->nome }}" 
                                 style="width: 50px; height: 50px; object-fit: contain;"
                                 class="icone-custom"
                                 data-cor="{{ $interesse->cor }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="material-symbols-outlined icone-fallback" style="display: none; font-size: 50px;">tag</span>
                        @else
                            <span class="material-symbols-outlined" style="font-size: 50px;">{{ $interesse->icone ?? 'tag' }}</span>
                        @endif
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
                        @if($interesse->icone_custom)
                            <!-- SOLUÇÃO SIMPLES - Sem onerror complexo -->
                            <img src="{{ $interesse->icone }}" 
                                 alt="{{ $interesse->nome }}" 
                                 style="width: 40px; height: 40px; object-fit: contain;"
                                 class="icone-custom-mini"
                                 data-cor="{{ $interesse->cor }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="material-symbols-outlined icone-fallback-mini" style="display: none; font-size: 32px;">tag</span>
                        @else
                            <span class="material-symbols-outlined">{{ $interesse->icone ?? 'tag' }}</span>
                        @endif
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
        width: 96%;
        margin-left: 2%;
        margin-right: 2%;   
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
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        position: relative;
    }

    .interesse-badge-mini img {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .interesse-badge-mini .material-symbols-outlined {
        font-size: 32px;
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
        font-size: 1.0rem;
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
        font-size: 1.0rem;
        transition: background 0.3s ease;
        white-space: nowrap;
    }

    .btn-saiba-mais:hover {
        background: #e5e7eb;
    }

    /* Para cards grandes (seção "Seus Interesses") */
    .interesse-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .interesse-icon img {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }
    
    .interesse-icon .material-symbols-outlined {
        font-size: 50px !important;
    }
    
    .icone-fallback {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .icone-fallback-mini {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Estilos para a busca em tempo real */
    .resultado-busca-interesse {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .resultado-busca-interesse:hover {
        background-color: #f9fafb;
    }

    .resultado-busca-interesse:last-child {
        border-bottom: none;
    }

    .interesse-icon-mini {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .interesse-icon-mini img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .info {
        flex: 1;
        min-width: 0;
    }

    .info h4 {
        margin: 0;
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .info p {
        margin: 0.25rem 0 0 0;
        font-size: 0.8rem;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .seguidores {
        font-size: 0.75rem;
        color: #9ca3af;
        white-space: nowrap;
        margin-left: 0.5rem;
    }

    .sugestoes-header {
        padding: 0.75rem 1rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
    }

    .nenhum-resultado-sugestao {
        padding: 1.5rem;
        text-align: center;
        color: #6b7280;
        font-size: 0.9rem;
    }

    /* Loading para busca */
    .loading-sugestoes {
        padding: 1.5rem;
        text-align: center;
        color: #6b7280;
    }

    .loading-sugestoes .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f4f6;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-bottom: 0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

        .pesquisa-interesses-container {
            max-width: 100%;
        }
        
        .interesse-icon {
            width: 50px;
            height: 50px;
        }
        
        .interesse-icon img {
            width: 40px;
            height: 40px;
        }
        
        .interesse-icon .material-symbols-outlined {
            font-size: 40px !important;
        }
        
        .interesse-badge-mini {
            width: 50px;
            height: 50px;
        }
        
        .interesse-badge-mini img {
            width: 32px;
            height: 32px;
        }
        
        .interesse-badge-mini .material-symbols-outlined {
            font-size: 24px;
        }
    }

    /* Estilo para input focado */
    #buscarInteresses:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script>
// ============================================
// BUSCA EM TEMPO REAL DE INTERESSES
// ============================================

let timeoutBuscaInteresses;
let ultimaQueryInteresses = '';

// Mostrar sugestões quando o input recebe foco
function mostrarSugestoes() {
    const input = document.getElementById('buscarInteresses');
    const sugestoes = document.getElementById('sugestoesBusca');
    
    if (!input || !sugestoes) return;
    
    if (input.value.trim().length > 0) {
        sugestoes.style.display = 'block';
        buscarInteressesEmTempoReal(input.value.trim());
    } else {
        // Mostrar TODOS os interesses quando vazio
        mostrarTodosInteresses();
    }
}

// Esconder sugestões
function esconderSugestoes() {
    const sugestoes = document.getElementById('sugestoesBusca');
    if (sugestoes) {
        sugestoes.style.display = 'none';
    }
}

// Buscar TODOS os interesses (quando input vazio)
function mostrarTodosInteresses() {
    const sugestoes = document.getElementById('sugestoesBusca');
    if (!sugestoes) return;
    
    sugestoes.innerHTML = `
        <div class="sugestoes-header">Todos os Interesses</div>
        <div class="loading-sugestoes">
            <div class="spinner"></div>
            <div>Carregando...</div>
        </div>
    `;
    sugestoes.style.display = 'block';
    
    // Usar os interesses já carregados na página
    const interessesPagina = @json($interesses->items());
    
    if (interessesPagina.length > 0) {
        const interessesFormatados = interessesPagina.map(interesse => ({
            id: interesse.id,
            nome: interesse.nome,
            slug: interesse.slug,
            descricao: interesse.descricao,
            icone: interesse.icone,
            icone_custom: interesse.icone_custom,
            cor: interesse.cor,
            seguidores_count: interesse.seguidores_count,
            contador_membros: interesse.contador_membros,
            tipo: 'interesse'
        }));
        
        exibirResultadosBuscaInteresses(interessesFormatados, true);
    } else {
        // Se não tiver na página, buscar via API
        fetch('/api/interesses/todos?limit=20')
            .then(response => response.json())
            .then(data => {
                if (data.sucesso && data.interesses.length > 0) {
                    exibirResultadosBuscaInteresses(data.interesses, true);
                } else {
                    sugestoes.innerHTML = `
                        <div class="sugestoes-header">Todos os Interesses</div>
                        <div class="nenhum-resultado-sugestao">
                            Nenhum interesse encontrado
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro ao buscar interesses:', error);
                sugestoes.innerHTML = `
                    <div class="sugestoes-header">Todos os Interesses</div>
                    <div class="nenhum-resultado-sugestao">
                        Erro ao carregar interesses
                    </div>
                `;
            });
    }
}

// Buscar interesses em tempo real
function buscarInteressesEmTempoReal(query) {
    const sugestoes = document.getElementById('sugestoesBusca');
    if (!sugestoes) return;
    
    // Mostrar loading
    sugestoes.innerHTML = `
        <div class="loading-sugestoes">
            <div class="spinner"></div>
            <div>Buscando interesses...</div>
        </div>
    `;
    
    clearTimeout(timeoutBuscaInteresses);
    
    timeoutBuscaInteresses = setTimeout(() => {
        // Usar a rota de busca global
        fetch(`/buscar?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(resultados => {
                // Filtrar apenas interesses
                const interessesFiltrados = resultados.filter(item => 
                    item.tipo === 'interesse' || (item.nome && item.descricao)
                );
                
                // Verificar se a query ainda é a mesma
                const input = document.getElementById('buscarInteresses');
                if (input && input.value.trim() === query) {
                    exibirResultadosBuscaInteresses(interessesFiltrados, false);
                }
            })
            .catch(error => {
                console.error('Erro na busca de interesses:', error);
                if (sugestoes) {
                    sugestoes.innerHTML = `
                        <div class="nenhum-resultado-sugestao">
                            Erro ao buscar interesses
                        </div>
                    `;
                }
            });
    }, 300); // Debounce de 300ms
}

// Exibir resultados da busca
function exibirResultadosBuscaInteresses(interesses, ehTodos = false) {
    const sugestoes = document.getElementById('sugestoesBusca');
    if (!sugestoes) return;
    
    if (!interesses || interesses.length === 0) {
        sugestoes.innerHTML = `
            <div class="sugestoes-header">${ehTodos ? 'Todos os Interesses' : 'Resultados da busca'}</div>
            <div class="nenhum-resultado-sugestao">
                Nenhum interesse encontrado
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="sugestoes-header">
            ${ehTodos ? 'Todos os Interesses' : `${interesses.length} resultado(s) encontrado(s)`}
        </div>
    `;
    
    interesses.forEach(interesse => {
        // Determinar cor do ícone
        const cor = interesse.cor || '#3b82f6';
        
        // Determinar ícone
        let iconeHTML = '';
        if (interesse.icone_custom) {
            iconeHTML = `
                <div class="interesse-icon-mini" style="background-color: ${cor}20;">
                    <img src="${interesse.icone}" 
                         alt="${interesse.nome}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="material-symbols-outlined" style="display: none; color: ${cor}; font-size: 24px;">tag</span>
                </div>
            `;
        } else {
            iconeHTML = `
                <div class="interesse-icon-mini" style="background-color: ${cor}20; color: ${cor};">
                    <span class="material-symbols-outlined">${interesse.icone || 'tag'}</span>
                </div>
            `;
        }
        
        // Determinar contador de seguidores
        const seguidores = interesse.seguidores_count || interesse.contador_membros || 0;
        
        html += `
            <div class="resultado-busca-interesse" onclick="selecionarInteresseBusca('${interesse.slug || interesse.id}', '${interesse.nome.replace(/'/g, "\\'")}')">
                ${iconeHTML}
                <div class="info">
                    <h4>${interesse.nome}</h4>
                    <p>${interesse.descricao || 'Sem descrição'}</p>
                </div>
                <div class="seguidores">
                    ${seguidores} seguidores
                </div>
            </div>
        `;
    });
    
    // Adicionar link para ver todos resultados
    const input = document.getElementById('buscarInteresses');
    if (input && input.value.trim() && !ehTodos) {
        html += `
            <div class="resultado-busca-interesse" 
                 onclick="pesquisarInteresses('${input.value.replace(/'/g, "\\'")}')"
                 style="justify-content: center; background-color: #f8fafc; font-weight: 500; color: #3b82f6;">
                <span class="material-symbols-outlined" style="font-size: 1rem; margin-right: 0.5rem;">search</span>
                Ver todos os resultados para "${input.value}"
            </div>
        `;
    }
    
    sugestoes.innerHTML = html;
}

// Selecionar interesse da busca
function selecionarInteresseBusca(slug, nome) {
    // Preencher input
    const input = document.getElementById('buscarInteresses');
    if (input) {
        input.value = nome;
    }
    
    // Redirecionar para o interesse
    window.location.href = `/interesses/${slug}`;
}

// Pesquisar interesses (submeter form)
function pesquisarInteresses(query) {
    const input = document.getElementById('buscarInteresses');
    if (input) {
        input.value = query;
    }
    
    // Submeter form
    const form = document.querySelector('.pesquisa-interesses-form');
    if (form) {
        form.submit();
    } else {
        // Se não encontrar form, redirecionar
        window.location.href = `/interesses/pesquisar?q=${encodeURIComponent(query)}`;
    }
}

// Event listener para input
document.addEventListener('DOMContentLoaded', function() {
    const inputBusca = document.getElementById('buscarInteresses');
    
    if (inputBusca) {
        // Buscar ao digitar
        inputBusca.addEventListener('input', function(e) {
            clearTimeout(timeoutBuscaInteresses);
            const query = e.target.value.trim();
            ultimaQueryInteresses = query;
            
            const sugestoes = document.getElementById('sugestoesBusca');
            if (!sugestoes) return;
            
            if (query.length === 0) {
                mostrarTodosInteresses();
                return;
            }
            
            if (query.length < 2) {
                sugestoes.style.display = 'none';
                return;
            }
            
            sugestoes.style.display = 'block';
            
            timeoutBuscaInteresses = setTimeout(() => {
                buscarInteressesEmTempoReal(query);
            }, 300);
        });
        
        // Focar no input se tiver query
        if (inputBusca.value.trim().length > 0) {
            setTimeout(() => {
                inputBusca.focus();
                inputBusca.setSelectionRange(inputBusca.value.length, inputBusca.value.length);
            }, 100);
        }
    }
    
    // Fechar sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        const sugestoes = document.getElementById('sugestoesBusca');
        const input = document.getElementById('buscarInteresses');
        
        if (sugestoes && sugestoes.style.display === 'block') {
            if (!sugestoes.contains(e.target) && (!input || !input.contains(e.target))) {
                esconderSugestoes();
            }
        }
    });
});

// Função para seguidores
function seguirInteresse(interesseId) {
    fetch(`/interesses/${interesseId}/seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            notificacoes: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            location.reload();
        }
    })
    .catch(error => console.error('Erro:', error));
}

function deixarSeguirInteresse(interesseId) {
    fetch(`/interesses/${interesseId}/deixar-seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            location.reload();
        }
    })
    .catch(error => console.error('Erro:', error));
}

// Adicionar event listeners para os botões
document.addEventListener('DOMContentLoaded', function() {
    // Botões de seguir
    document.querySelectorAll('.btn-seguir-interesse:not(.seguindo)').forEach(btn => {
        btn.addEventListener('click', function() {
            const interesseId = this.dataset.interesseId;
            seguirInteresse(interesseId);
        });
    });
    
    // Botões de deixar de seguir (na lista principal)
    document.querySelectorAll('.btn-seguir-interesse.seguindo').forEach(btn => {
        btn.addEventListener('click', function() {
            const interesseId = this.dataset.interesseId;
            deixarSeguirInteresse(interesseId);
        });
    });
    
    // Botões "Deixar de Seguir" (na seção de interesses do usuário)
    document.querySelectorAll('.btn-deixar-seguir').forEach(btn => {
        btn.addEventListener('click', function() {
            const interesseId = this.dataset.interesseId;
            deixarSeguirInteresse(interesseId);
        });
    });
});
</script>

<!-- Incluir o JavaScript externo -->
<script src="{{ asset('assets/js/interesses/seguir-interesse.js') }}"></script>
@endsection