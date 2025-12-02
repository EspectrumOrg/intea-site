@extends('feed.post.template.layout')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <!-- Header do Interesse - COR DINÂMICA -->
    <div class="interesse-header-main" style="border-left-color: {{ $interesse->cor }}; background: linear-gradient(135deg, {{ $interesse->cor }}15 0%, {{ $interesse->cor }}08 100%);">
        <div class="interesse-header-content">
            <div class="interesse-avatar" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                @if($interesse->icone_custom)
                    <img src="{{ $interesse->icone }}" alt="{{ $interesse->nome }}" style="width: 80px; height: 80px; border-radius: 20px;">
                @else
                    <span class="material-symbols-outlined" style="font-size: 3rem;">{{ $interesse->icone ?? 'tag' }}</span>
                @endif
            </div>
            <div class="interesse-info-main">
                <h1 class="interesse-titulo" style="color: {{ $interesse->cor }};">{{ $interesse->nome }}</h1>
                <p class="interesse-descricao">{{ $interesse->descricao }}</p>
                <div class="interesse-stats-main">
                    <div class="stat-item" style="border-color: {{ $interesse->cor }}20; background: {{ $interesse->cor }}08;">
                        <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">people</span>
                        <span class="stat-number">{{ $interesse->seguidores_count ?? $interesse->contador_membros }}</span>
                        <span class="stat-label">membros</span>
                    </div>
                    <div class="stat-item" style="border-color: {{ $interesse->cor }}20; background: {{ $interesse->cor }}08;">
                        <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">chat</span>
                        <span class="stat-number">{{ $interesse->postagens_count ?? $interesse->contador_postagens }}</span>
                        <span class="stat-label">postagens</span>
                    </div>
                    <div class="stat-item" style="border-color: {{ $interesse->cor }}20; background: {{ $interesse->cor }}08;">
                        <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">shield</span>
                        <span class="stat-number">{{ $interesse->moderadores()->count() }}</span>
                        <span class="stat-label">moderadores</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="interesse-actions-main">
            @auth
                @if($usuarioSegue)
                    <button class="btn-deixar-seguir" data-interesse-id="{{ $interesse->id }}">
                        <span class="material-symbols-outlined">close</span>
                        Deixar de Seguir
                    </button>
                @else
                    <button class="btn-seguir-interesse" data-interesse-id="{{ $interesse->id }}" style="background: {{ $interesse->cor }}; border-color: {{ $interesse->cor }};">
                        <span class="material-symbols-outlined">add</span>
                        Seguir
                    </button>
                @endif
                
                <button class="btn-compartilhar" id="btnCompartilhar">
                    <span class="material-symbols-outlined">share</span>
                    Compartilhar
                </button>
                
                <!-- BOTÕES DE GERENCIAMENTO PARA DONOS -->
                @if($usuarioEhDono || auth()->user()->isAdministrador())
                    <div class="dropdown-interesse">
                        <button class="btn-gerenciar">
                            <span class="material-symbols-outlined">settings</span>
                            Gerenciar
                        </button>
                        <div class="dropdown-menu-interesse">
                            <a href="{{ route('interesses.edit', $interesse->slug) }}" class="dropdown-item">
                                <span class="material-symbols-outlined">edit</span>
                                Editar Interesse
                            </a>
                            <a href="{{ route('interesses.moderadores', $interesse->slug) }}" class="dropdown-item">
                                <span class="material-symbols-outlined">group</span>
                                Gerenciar Moderadores
                            </a>
                            <button class="dropdown-item" onclick="abrirModalTransferir()">
                                <span class="material-symbols-outlined" style="color: #f59e0b;">swap_horiz</span>
                                <span style="color: #f59e0b;">Transferir Propriedade</span>
                            </button>
                            <button class="dropdown-item text-danger" onclick="confirmarDelecaoInteresse('{{ $interesse->slug }}')">
                                <span class="material-symbols-outlined">delete</span>
                                Deletar Interesse
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-seguir-interesse" style="background: {{ $interesse->cor }}; border-color: {{ $interesse->cor }};">
                    <span class="material-symbols-outlined">login</span>
                    Entrar para seguir
                </a>
            @endauth
        </div>
    </div>

    <!-- ABA DE NAVEGAÇÃO COM GERENCIAMENTO -->
    <div class="interesse-navigation">
        <a href="{{ route('post.interesse', $interesse->slug) }}" class="nav-item" style="border-color: {{ $interesse->cor }}; color: {{ $interesse->cor }};">
            <span class="material-symbols-outlined">feed</span>
            Feed
        </a>
        
        <!-- ABAS ESPECIAIS PARA DONOS -->
        @if($usuarioEhDono || auth()->user()->isAdministrador())
            <a href="{{ route('interesses.edit', $interesse->slug) }}" class="nav-item" style="border-color: {{ $interesse->cor }}; color: {{ $interesse->cor }};">
                <span class="material-symbols-outlined">edit</span>
                Editar
            </a>
            <a href="{{ route('interesses.moderadores', $interesse->slug) }}" class="nav-item" style="border-color: {{ $interesse->cor }}; color: {{ $interesse->cor }};">
                <span class="material-symbols-outlined">group</span>
                Moderadores
            </a>
        @endif
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="interesse-content-single-column">
        
        <!-- SEÇÃO DO DONO/CRIADOR EM DESTAQUE -->
        @if($dono)
        <section class="interesses-section">
            <h2 class="section-title">
                <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">person</span>
                Criador do Interesse
                <span class="section-badge" style="background: {{ $interesse->cor }};">Dono</span>
            </h2>
            <div class="section-content">
                <div class="dono-card" style="border-left-color: {{ $interesse->cor }};">
                    <div class="dono-avatar">
                        @if($dono->foto)
                            <img src="{{ asset('storage/' . $dono->foto) }}" 
                                alt="{{ $dono->apelido ?: $dono->user }}"
                                onerror="this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';">
                            <div class="avatar-fallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f3f4f6; border-radius: 50%; align-items: center; justify-content: center; color: #6b7280;">
                                <span class="material-symbols-outlined">account_circle</span>
                            </div>
                        @else
                            <span class="material-symbols-outlined" style="font-size: 3rem; color: #6b7280;">account_circle</span>
                        @endif
                        <div class="dono-badge" style="background: {{ $interesse->cor }};">
                            <span class="material-symbols-outlined">star</span>
                        </div>
                    </div>
                    <div class="dono-details">
                        <div class="dono-header">
                            <h3>{{ $dono->apelido ?: $dono->user }}</h3>
                            <span class="dono-role" style="background: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                                <span class="material-symbols-outlined">verified</span>
                             Dono
                            </span>
                        </div>
                        <p class="dono-bio">{{ $dono->descricao ?: 'Criador desta comunidade incrível!' }}</p>
                        <div class="dono-stats">
                            <div class="dono-stat">
                                <span class="material-symbols-outlined">groups</span>
                                <span>{{ $dono->seguidores()->count() }} seguidores</span>
                            </div>
                            <div class="dono-stat">
                                <span class="material-symbols-outlined">chat</span>
                                <span>{{ $dono->postagens()->count() }} postagens</span>
                            </div>
                        </div>
                        @if(auth()->id() !== $dono->id)
                            <div class="dono-actions">
                                <button class="btn-seguir-dono" data-user-id="{{ $dono->id }}">
                                    <span class="material-symbols-outlined">person_add</span>
                                    Seguir
                                </button>
                                <a href="{{ route('profile.show', $dono->id) }}" class="btn-visitar-perfil" style="background: {{ $interesse->cor }};">
                                    <span class="material-symbols-outlined">visibility</span>
                                    Visitar Perfil
                                </a>
                            </div>
                        @else
                            <div class="dono-actions">
                                <span class="dono-you" style="color: {{ $interesse->cor }};">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Este é você!
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- SOBRE O INTERESSE -->
        @if($interesse->sobre)
        <section class="interesses-section">
            <h2 class="section-title">
                <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">info</span>
                Sobre
            </h2>
            <div class="section-content">
                <p>{{ $interesse->sobre }}</p>
            </div>
        </section>
        @endif

        <!-- USUÁRIOS POPULARES COM IDENTIFICAÇÃO DO DONO -->
        @if($usuariosPopulares && $usuariosPopulares->count() > 0)
        <section class="interesses-section">
            <h2 class="section-title">
                <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">star</span>
                Membros Populares
            </h2>
            <div class="usuarios-grid">
                @foreach($usuariosPopulares as $usuario)
                <div class="usuario-card">
                   <div class="usuario-avatar">
                        @if($usuario->foto)
                            <img src="{{ asset('storage/' . $usuario->foto) }}" 
                                alt="{{ $usuario->apelido ?: $usuario->user }}"
                                onerror="this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';">
                            <div class="avatar-fallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f3f4f6; border-radius: 50%; align-items: center; justify-content: center; color: #6b7280;">
                                <span class="material-symbols-outlined">account_circle</span>
                            </div>
                        @else
                            <span class="material-symbols-outlined" style="color: #6b7280;">account_circle</span>
                        @endif
                        <!-- BADGE ESPECIAL PARA O DONO -->
                        @if($dono && $usuario->id === $dono->id)
                            <div class="usuario-badge-dono" style="background: {{ $interesse->cor }};">
                                <span class="material-symbols-outlined" style="font-size: 12px;">star</span>
                            </div>
                        @endif
                    </div>
                    <div class="usuario-details">
                        <strong class="usuario-nome">{{ $usuario->apelido ?: $usuario->user }}</strong>
                        <!-- IDENTIFICAÇÃO DO CARGO -->
                        @if($dono && $usuario->id === $dono->id)
                            <span class="usuario-role" style="color: {{ $interesse->cor }};">Dono</span>
                        @elseif($interesse->moderadores()->where('usuario_id', $usuario->id)->exists())
                            <span class="usuario-role" style="color: #8B5CF6;">Moderador</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- POSTAGENS MAIS CURTIDAS -->
        <section class="interesses-section">
            <h2 class="section-title">
                <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">favorite</span>
                Postagens Populares
            </h2>
            
            @if($postagensMaisCurtidas && $postagensMaisCurtidas->count() > 0)
                <div class="postagens-mais-curtidas">
                    @foreach($postagensMaisCurtidas as $postagem)
                        <div class="postagem-curtida-item">
                            <div class="postagem-header">
                                <div class="usuario-info">
                                    <div class="usuario-avatar-mini">
                                        @if($postagem->usuario->foto)
                                            <img src="{{ asset('storage/' . $postagem->usuario->foto) }}" 
                                                alt="{{ $postagem->usuario->apelido ?: $postagem->usuario->user }}"
                                                onerror="this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';">
                                            <div class="avatar-fallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f3f4f6; border-radius: 50%; align-items: center; justify-content: center; color: #6b7280;">
                                                <span class="material-symbols-outlined">account_circle</span>
                                            </div>
                                        @else
                                            <span class="material-symbols-outlined" style="font-size: 1.5rem; color: #6b7280;">account_circle</span>
                                        @endif
                                        <!-- MINI BADGE PARA IDENTIFICAR O DONO NAS POSTAGENS -->
                                        @if($dono && $postagem->usuario->id === $dono->id)
                                            <div class="mini-badge-dono" style="background: {{ $interesse->cor }};"></div>
                                        @endif
                                    </div>
                                    <div class="usuario-detalhes">
                                        <strong>{{ $postagem->usuario->apelido ?: $postagem->usuario->user }}</strong>
                                        <!-- IDENTIFICAÇÃO DO CARGO NAS POSTAGENS -->
                                        @if($dono && $postagem->usuario->id === $dono->id)
                                            <span class="mini-role" style="color: {{ $interesse->cor }};">Dono</span>
                                        @elseif($interesse->moderadores()->where('usuario_id', $postagem->usuario->id)->exists())
                                            <span class="mini-role" style="color: #8B5CF6;">Moderador</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="curtidas-count">
                                    <span class="material-symbols-outlined" style="color: #ef4444;">favorite</span>
                                    <strong>{{ $postagem->curtidas_count }}</strong>
                                </div>
                            </div>
                            
                            <div class="postagem-conteudo">
                                <p class="postagem-texto">{{ Str::limit($postagem->texto_postagem, 200) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-results">
                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">favorite</span>
                    <h3>Nenhuma postagem ainda</h3>
                    <p>Seja o primeiro a compartilhar algo neste interesse!</p>
                </div>
            @endif
        </section>
    </div>
</div>

<!-- Modal de Transferência de Propriedade (HTML será injetado via JS quando necessário) -->
<div id="modalTransferirContainer"></div>


<!-- STYLES (Mantendo o CSS original e adicionando apenas o modal) -->
<style>
/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.usuario-info-transferir {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.usuario-avatar-transferir {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    flex-shrink: 0;
}

.usuario-avatar-transferir img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-detalhes-transferir {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0; /* Permite que o texto quebre */
}

.usuario-detalhes-transferir strong {
    font-size: 0.9rem;
    color: #1f2937;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.usuario-detalhes-transferir span {
    font-size: 0.8rem;
    color: #6b7280;
}

.btn-selecionar-transferir {
    padding: 0.5rem 1rem;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background 0.2s;
    flex-shrink: 0;
}

.resultado-usuario-transferir {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
    gap: 1rem;
}

/* Responsividade para mobile */
@media (max-width: 480px) {
    .resultado-usuario-transferir {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .btn-selecionar-transferir {
        align-self: flex-end;
    }
}

/* Resto do CSS original (mantido do seu template) */
/* Header Principal */
.interesse-header-main {
    padding: 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    border-left: 6px solid;
}

.interesse-header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.interesse-avatar {
    width: 100px;
    height: 100px;
    border-radius: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.interesse-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 25px;
    object-fit: cover;
}

.interesse-info-main {
    flex: 1;
}

.interesse-titulo {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.interesse-descricao {
    font-size: 1.2rem;
    color: #666;
    margin: 0 0 1.5rem 0;
}

.interesse-stats-main {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    border: 2px solid;
}

.stat-number {
    font-weight: 700;
    font-size: 1.1rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.interesse-actions-main {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-seguir-interesse, .btn-deixar-seguir, .btn-compartilhar, .btn-gerenciar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-seguir-interesse {
    background: #3B82F6;
    color: white;
    border-color: #3B82F6;
}

.btn-seguir-interesse:hover {
    background: #2563EB;
    transform: translateY(-2px);
}

.btn-deixar-seguir {
    background: #6B7280;
    color: white;
    border-color: #6B7280;
}

.btn-compartilhar {
    background: white;
    color: #374151;
    border-color: #D1D5DB;
}

.btn-gerenciar {
    background: #F3F4F6;
    color: #374151;
    border-color: #D1D5DB;
}

/* Navegação */
.interesse-navigation {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-item:hover, .nav-item.active {
    border-color: #3B82F6;
    color: #3B82F6;
}

.nav-item.active {
    background: #3B82F6;
    color: white;
}

/* Seção do Dono */
.dono-card {
    display: flex;
    gap: 1.5rem;
    padding: 2rem;
    background: white;
    border-radius: 16px;
    border-left: 4px solid;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.dono-avatar {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
    flex-shrink: 0;
}

.dono-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dono-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 2px solid white;
}

.dono-details {
    flex: 1;
}

.dono-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.dono-header h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #1F2937;
}

.dono-role {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.dono-bio {
    color: #6B7280;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.dono-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.dono-stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6B7280;
    font-size: 0.9rem;
}

.dono-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-seguir-dono {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #F3F4F6;
    color: #374151;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
}

.btn-visitar-perfil {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
}

.dono-you {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #10B981;
}

/* Badges de identificação */
.usuario-badge-dono {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 2px solid white;
}

.usuario-role, .mini-role {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.25rem;
}

.mini-badge-dono {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 1px solid white;
}

/* Dropdown de Gerenciamento */
.dropdown-interesse {
    position: relative;
    display: inline-block;
}

.dropdown-menu-interesse {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 200px;
    z-index: 1000;
    display: none;
}

.dropdown-interesse:hover .dropdown-menu-interesse {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    transition: background 0.2s ease;
}

.dropdown-item:hover {
    background: #F9FAFB;
}

.dropdown-item.text-danger {
    color: #EF4444;
}

.dropdown-item.text-danger:hover {
    background: #FEF2F2;
}

/* Layout das seções */
.interesses-section {
    margin-bottom: 2rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #1F2937;
}

.section-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
}

.section-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Grid de usuários */
.usuarios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.usuario-card {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.usuario-avatar {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
    margin: 0 auto 0.5rem;
}

.usuario-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-nome {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

/* Postagens */
.postagem-curtida-item {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.postagem-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.usuario-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.usuario-avatar-mini {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
}

.usuario-avatar-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-detalhes {
    display: flex;
    flex-direction: column;
}

.usuario-detalhes strong {
    font-size: 0.9rem;
}

.curtidas-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #EF4444;
    font-weight: 600;
}

.postagem-texto {
    color: #374151;
    line-height: 1.5;
}

.no-results {
    text-align: center;
    padding: 3rem;
    color: #6B7280;
}

/* Responsividade */
@media (max-width: 768px) {
    .interesse-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .interesse-actions-main {
        justify-content: center;
    }
    
    .dono-card {
        flex-direction: column;
        text-align: center;
    }
    
    .dono-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .dono-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .dono-actions {
        justify-content: center;
    }
    
    .interesse-navigation {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .interesse-header-main {
        padding: 1.5rem;
    }
    
    .interesse-titulo {
        font-size: 2rem;
    }
    
    .interesse-stats-main {
        justify-content: center;
    }
    
    .stat-item {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }

    .avatar-fallback {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

/* Garantir que avatares tenham position relative para os fallbacks funcionarem */
.dono-avatar,
.usuario-avatar,
.usuario-avatar-mini,
.usuario-avatar-mini {
    position: relative;
}

/* Melhorar a aparência dos ícones fallback */
.dono-avatar .avatar-fallback {
    font-size: 3rem;
}

.usuario-avatar .avatar-fallback {
    font-size: 1.5rem;
}

.usuario-avatar-mini .avatar-fallback {
    font-size: 1.2rem;
    background: #e5e7eb;
}
}
</style>

<!-- SCRIPTS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compartilhar interesse
    document.getElementById('btnCompartilhar')?.addEventListener('click', function() {
        const url = window.location.href;
        const title = document.querySelector('.interesse-titulo')?.textContent || 'Interesse';
        
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url,
            });
        } else {
            navigator.clipboard.writeText(url).then(() => {
                mostrarToast('Link copiado para a área de transferência!', 'success');
            });
        }
    });

    // Seguir dono
    document.querySelector('.btn-seguir-dono')?.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        // Implementar lógica AJAX para seguir usuário
        this.innerHTML = '<span class="material-symbols-outlined">check</span>Seguindo';
        this.style.background = '#10B981';
        this.style.color = 'white';
        this.disabled = true;
    });

    // Botão criar primeira postagem
    const btnCriarPostagem = document.getElementById('btnCriarPrimeiraPostagem');
    if (btnCriarPostagem) {
        btnCriarPostagem.addEventListener('click', function() {
            window.location.href = "/feed/create?interesse_id={{ $interesse->id }}";
        });
    }

    // Sistema de seguir/deixar de seguir interesse
    const btnSeguir = document.querySelector('.btn-seguir-interesse');
    const btnDeixarSeguir = document.querySelector('.btn-deixar-seguir');

    if (btnSeguir) {
        btnSeguir.addEventListener('click', function() {
            const interesseId = this.getAttribute('data-interesse-id');
            seguirInteresse(interesseId, this);
        });
    }

    if (btnDeixarSeguir) {
        btnDeixarSeguir.addEventListener('click', function() {
            const interesseId = this.getAttribute('data-interesse-id');
            deixarSeguirInteresse(interesseId, this);
        });
    }

    // Fechar modal ao clicar fora
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalTransferir');
        if (event.target === modal) {
            fecharModalTransferir();
        }
    });
});

// ============================================
// VARIÁVEIS GLOBAIS
// ============================================
let usuarioSelecionadoTransferencia = null;

// ============================================
// FUNÇÕES DE TRANSFERÊNCIA DE PROPRIEDADE
// ============================================

function abrirModalTransferir() {
    criarModalTransferir();
    usuarioSelecionadoTransferencia = null;
}

function fecharModalTransferir() {
    const container = document.getElementById('modalTransferirContainer');
    if (container) {
        container.innerHTML = '';
    }
    usuarioSelecionadoTransferencia = null;
}

function criarModalTransferir() {
    const modalHTML = `
        <div id="modalTransferir" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000;">
            <div style="background: white; border-radius: 12px; width: 95%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-height: 90vh; display: flex; flex-direction: column;">
                <!-- Cabeçalho -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div>
                        <h3 style="margin: 0; color: {{ $interesse->cor }}; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="font-size: 1.4rem;">swap_horiz</span>
                            Transferir Propriedade
                        </h3>
                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 0.9rem;">
                            Interesse: <strong>{{ $interesse->nome }}</strong>
                        </p>
                    </div>
                    <button type="button" onclick="fecharModalTransferir()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 1.5rem; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        ×
                    </button>
                </div>
                
                <!-- Corpo do modal -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <!-- Alerta de atenção -->
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span class="material-symbols-outlined" style="color: #f59e0b; font-size: 1.2rem; flex-shrink: 0;">warning</span>
                            <div style="font-size: 0.9rem;">
                                <strong style="color: #92400e;">Atenção! Esta ação é permanente</strong>
                                <p style="color: #78350f; margin: 0.25rem 0 0 0;">
                                    Você perderá todos os privilégios de dono sobre este interesse.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Busca de usuários -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px; font-size: 1.1rem;">search</span>
                            Buscar novo dono
                        </label>
                        <div style="position: relative;">
                            <input type="text" id="buscarUsuarioTransferir" placeholder="Digite nome, apelido ou usuário..." 
                                   style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; box-sizing: border-box;">
                            <span class="material-symbols-outlined" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none;">
                                search
                            </span>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.85rem;">
                            Procure pelo nome, apelido ou @usuário da pessoa
                        </small>
                    </div>
                    
                    <!-- Resultados da busca -->
                    <div id="resultadosTransferencia" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 1rem; display: none; max-height: 300px; overflow-y: auto;"></div>
                    
                    <!-- Usuário selecionado -->
                    <div id="usuarioSelecionadoInfo" style="display: none; padding: 1.25rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin-top: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="material-symbols-outlined" style="color: #0284c7; font-size: 1.5rem;">check_circle</span>
                            <div>
                                <p style="margin: 0 0 0.25rem 0; font-weight: 600; color: #0369a1;">Novo dono selecionado</p>
                                <p style="margin: 0; color: #0c4a6e; font-size: 1.1rem;">
                                    <strong id="nomeUsuarioSelecionado"></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rodapé do modal -->
                <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: #6b7280; display: flex; align-items: center; gap: 4px;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">info</span>
                            <span>Transferência irreversível</span>
                        </div>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" onclick="fecharModalTransferir()" 
                                    style="padding: 0.75rem 1.5rem; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.2s;">
                                Cancelar
                            </button>
                            <button type="button" id="btnConfirmarTransferencia" onclick="confirmarTransferencia()" 
                                    style="padding: 0.75rem 1.5rem; background: {{ $interesse->cor }}; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: none; transition: all 0.2s;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px;">swap_horiz</span>
                                Confirmar Transferência
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Adicionar ao container
    const container = document.getElementById('modalTransferirContainer');
    if (!container) {
        console.error('Container modalTransferirContainer não encontrado');
        return;
    }
    
    container.innerHTML = modalHTML;
    
    // Configurar evento de busca
    const buscarInput = document.getElementById('buscarUsuarioTransferir');
    let buscaTimeout;
    
    if (buscarInput) {
        buscarInput.addEventListener('input', function(e) {
            clearTimeout(buscaTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                const resultados = document.getElementById('resultadosTransferencia');
                if (resultados) {
                    resultados.innerHTML = '';
                    resultados.style.display = 'none';
                }
                return;
            }
            
            buscaTimeout = setTimeout(() => {
                buscarUsuariosParaTransferir(query);
            }, 500);
        });
        
        // Focar no campo de busca
        setTimeout(() => buscarInput.focus(), 100);
    }
    
    // Adicionar estilos de hover
    const style = document.createElement('style');
    style.textContent = `
        #buscarUsuarioTransferir:focus {
            outline: none;
            border-color: {{ $interesse->cor }};
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-selecionar-transferir:hover {
            background: #059669 !important;
            transform: translateY(-1px);
        }
        .resultado-usuario-transferir:hover {
            background-color: #f9fafb !important;
        }
        #btnConfirmarTransferencia:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px {{ $interesse->cor }}20;
        }
    `;
    document.head.appendChild(style);
}

function buscarUsuariosParaTransferir(query) {
    const resultados = document.getElementById('resultadosTransferencia');
    const buscarInput = document.getElementById('buscarUsuarioTransferir');
    
    if (!resultados || !buscarInput) {
        console.error('Elementos necessários não encontrados');
        return;
    }
    
    // Mostrar loading
    resultados.innerHTML = `
        <div style="padding: 2rem; text-align: center; color: #6b7280;">
            <div style="display: inline-block; width: 24px; height: 24px; border: 2px solid #f3f4f6; border-top-color: {{ $interesse->cor }}; border-radius: 50%; animation: spin 0.6s linear infinite; margin-bottom: 0.5rem;"></div>
            <div>Buscando usuários...</div>
        </div>
    `;
    resultados.style.display = 'block';
    
    // Adicionar feedback no input
    buscarInput.style.borderColor = '{{ $interesse->cor }}';
    
    fetch(`/buscar?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) throw new Error('Erro na busca');
            return response.json();
        })
        .then(usuarios => {
            resultados.innerHTML = '';
            
            if (!usuarios || usuarios.length === 0) {
                resultados.innerHTML = `
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;">person_off</span>
                        <div>Nenhum usuário encontrado</div>
                        <small style="font-size: 0.85rem; margin-top: 0.5rem; display: block;">Tente outro termo de busca</small>
                    </div>
                `;
                return;
            }
            
            // Filtrar apenas usuários (não tendências)
            const usuariosFiltrados = usuarios.filter(u => u.tipo !== 'tendencia');
            
            if (usuariosFiltrados.length === 0) {
                resultados.innerHTML = `
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;">group_off</span>
                        <div>Nenhum usuário encontrado</div>
                    </div>
                `;
                return;
            }
            
            // Exibir resultados
            usuariosFiltrados.forEach(usuario => {
                const resultadoItem = document.createElement('div');
                resultadoItem.className = 'resultado-usuario-transferir';
                resultadoItem.style.cssText = `
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid #f3f4f6;
                    cursor: pointer;
                    transition: background-color 0.2s;
                    gap: 0.75rem;
                `;
                
                resultadoItem.onmouseover = () => resultadoItem.style.backgroundColor = '#f9fafb';
                resultadoItem.onmouseout = () => resultadoItem.style.backgroundColor = 'white';
                
                // Avatar (versão corrigida - sem onerror problemático)
let avatarHTML = '';
if (usuario.foto) {
    // Usar uma função JavaScript para handle do erro
    const fotoUrl = `/storage/${usuario.foto}`;
    avatarHTML = `<div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #f3f4f6; position: relative;">
        <img src="${fotoUrl}" 
             alt="${usuario.apelido || usuario.user}" 
             style="width: 100%; height: 100%; object-fit: cover;"
             onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\"material-symbols-outlined\" style=\"position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #6b7280; font-size: 1.5rem;\">account_circle</span>';">
    </div>`;
} else {
    avatarHTML = `<div style="width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #6b7280;">
        <span class="material-symbols-outlined" style="font-size: 1.5rem;">account_circle</span>
    </div>`;
}
                
                // Informações do usuário
                resultadoItem.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                        ${avatarHTML}
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; color: #1f2937; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${usuario.apelido || usuario.user}
                            </div>
                            <div style="font-size: 0.85rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                @${usuario.user}
                            </div>
                            ${usuario.descricao ? 
                                `<div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ${usuario.descricao}
                                </div>` : ''
                            }
                        </div>
                    </div>
                    <button type="button" 
                            class="btn-selecionar-transferir"
                            onclick="selecionarUsuarioParaTransferir(${usuario.id}, '${(usuario.apelido || usuario.user).replace(/'/g, "\\'")}', '${usuario.user.replace(/'/g, "\\'")}')"
                            style="padding: 0.5rem 1rem; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; flex-shrink: 0;">
                        Selecionar
                    </button>
                `;
                
                resultados.appendChild(resultadoItem);
            });
            
            // Adicionar contador de resultados
            const contador = document.createElement('div');
            contador.style.cssText = 'padding: 0.5rem 1rem; font-size: 0.85rem; color: #6b7280; border-top: 1px solid #f3f4f6; background: #f9fafb;';
            contador.textContent = `${usuariosFiltrados.length} usuário${usuariosFiltrados.length !== 1 ? 's' : ''} encontrado${usuariosFiltrados.length !== 1 ? 's' : ''}`;
            resultados.appendChild(contador);
            
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            resultados.innerHTML = `
                <div style="padding: 2rem; text-align: center; color: #ef4444;">
                    <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; color: #ef4444;">error</span>
                    <div>Erro ao buscar usuários</div>
                    <small style="font-size: 0.85rem; margin-top: 0.5rem; display: block; color: #9ca3af;">
                        ${error.message}
                    </small>
                </div>
            `;
        })
        .finally(() => {
            buscarInput.style.borderColor = '#d1d5db';
        });
}

function selecionarUsuarioParaTransferir(usuarioId, nome, usuario) {
    usuarioSelecionadoTransferencia = { 
        id: usuarioId, 
        nome: nome, 
        usuario: usuario 
    };
    
    // Atualizar exibição
    const nomeElement = document.getElementById('nomeUsuarioSelecionado');
    const infoSection = document.getElementById('usuarioSelecionadoInfo');
    const confirmButton = document.getElementById('btnConfirmarTransferencia');
    
    if (nomeElement) nomeElement.textContent = `${nome} (@${usuario})`;
    if (infoSection) infoSection.style.display = 'block';
    if (confirmButton) confirmButton.style.display = 'block';
    
    // Esconder resultados e limpar busca
    const resultados = document.getElementById('resultadosTransferencia');
    const buscarInput = document.getElementById('buscarUsuarioTransferir');
    
    if (resultados) resultados.style.display = 'none';
    if (buscarInput) buscarInput.value = '';
    
    // Rolar para a seção de confirmação
    setTimeout(() => {
        if (infoSection) {
            infoSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 100);
}

function confirmarTransferencia() {
    if (!usuarioSelecionadoTransferencia) {
        mostrarToast('Selecione um usuário primeiro', 'error');
        return;
    }
    
    const interesseNome = document.querySelector('.interesse-titulo')?.textContent || 'este interesse';
    const mensagem = `⚠️ ATENÇÃO: Você está transferindo a propriedade do interesse "${interesseNome}"\n\n`
                    + `Novo dono: ${usuarioSelecionadoTransferencia.nome} (@${usuarioSelecionadoTransferencia.usuario})\n\n`
                    + `✅ ${usuarioSelecionadoTransferencia.nome} será o novo dono\n`
                    + `❌ Você perderá o controle total\n`
                    + `🔄 Esta ação é PERMANENTE e IRREVERSÍVEL\n\n`
                    + `Tem certeza que deseja continuar?`;
    
    if (!confirm(mensagem)) {
        return;
    }
    
    const interesseSlug = '{{ $interesse->slug }}';
    
    // Buscar token CSRF de forma segura
    let token = '';
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) {
        token = tokenInput.value;
    } else {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            token = metaToken.getAttribute('content');
        }
    }
    
    if (!token) {
        mostrarToast('Erro de segurança. Recarregue a página e tente novamente.', 'error');
        return;
    }
    
    // Mostrar loading
    const btn = document.getElementById('btnConfirmarTransferencia');
    if (!btn) return;
    
    const originalText = btn.innerHTML;
    btn.innerHTML = `
        <span style="display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite; margin-right: 8px;"></span>
        Processando...
    `;
    btn.disabled = true;
    
    // Enviar requisição
    fetch(`/interesses/${interesseSlug}/transferir-propriedade`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            novo_dono_id: usuarioSelecionadoTransferencia.id
        })
    })
    .then(response => {
        console.log('Status:', response.status);
        
        if (response.status === 500) {
            return response.text().then(text => {
                console.error('Erro 500 detalhado:', text);
                throw new Error('Erro interno no servidor');
            });
        }
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        
        if (data.sucesso) {
            mostrarToast(data.mensagem, 'success');
            fecharModalTransferir();
            
            // Recarregar após 1.5 segundos
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.mensagem || 'Erro desconhecido ao transferir propriedade');
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        mostrarToast(error.message || 'Erro ao transferir propriedade', 'error');
        
        // Restaurar botão
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// ============================================
// FUNÇÕES AUXILIARES
// ============================================

function seguirInteresse(interesseId, elemento) {
    fetch(`/interesses/${interesseId}/seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ notificacoes: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            elemento.outerHTML = `
                <button class="btn-deixar-seguir" data-interesse-id="${interesseId}">
                    <span class="material-symbols-outlined">close</span>
                    Deixar de Seguir
                </button>
            `;
            // Reconectar evento
            document.querySelector('.btn-deixar-seguir')?.addEventListener('click', function() {
                deixarSeguirInteresse(interesseId, this);
            });
            mostrarToast(data.mensagem, 'success');
        } else {
            mostrarToast(data.mensagem, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast('Erro ao seguir interesse', 'error');
    });
}

function deixarSeguirInteresse(interesseId, elemento) {
    fetch(`/interesses/${interesseId}/deixar-seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            elemento.outerHTML = `
                <button class="btn-seguir-interesse" data-interesse-id="${interesseId}" style="background: {{ $interesse->cor }}; border-color: {{ $interesse->cor }};">
                    <span class="material-symbols-outlined">add</span>
                    Seguir
                </button>
            `;
            document.querySelector('.btn-seguir-interesse')?.addEventListener('click', function() {
                seguirInteresse(interesseId, this);
            });
            mostrarToast(data.mensagem, 'success');
        } else {
            mostrarToast(data.mensagem, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast('Erro ao deixar de seguir interesse', 'error');
    });
}

function confirmarDelecaoInteresse(slug) {
    if (confirm('⚠️ ATENÇÃO: Esta ação é PERMANENTE e IRREVERSÍVEL!\n\nTodos os dados deste interesse serão deletados permanentemente.\n\nTem certeza que deseja continuar?')) {
        // Buscar token CSRF de qualquer formulário na página
        const tokenInput = document.querySelector('input[name="_token"]');
        let token = '';
        
        if (tokenInput) {
            token = tokenInput.value;
        } else {
            token = '{{ csrf_token() }}';
        }
        
        console.log('Deletando interesse:', slug, 'Token:', token ? 'Encontrado' : 'Não encontrado');
        
        // Criar um formulário dinâmico
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/interesses/${slug}`;
        form.style.display = 'none';
        
        // Token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = token;
        
        // Método spoofing para DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        
        // Enviar o formulário
        form.submit();
    }
}

function mostrarToast(mensagem, tipo = 'info') {
    // Remove toast existente
    const toastExistente = document.querySelector('.custom-toast');
    if (toastExistente) {
        toastExistente.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'error' ? '#e53e3e' : tipo === 'success' ? '#38a169' : '#3182ce'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10001;
        font-size: 14px;
        font-weight: 500;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        max-width: 90%;
    `;
    
    // Ícone baseado no tipo
    let icone = '';
    switch(tipo) {
        case 'success':
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">check_circle</span>';
            break;
        case 'error':
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">error</span>';
            break;
        default:
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">info</span>';
    }
    
    toast.innerHTML = `${icone}<span>${mensagem}</span>`;
    document.body.appendChild(toast);
    
    // Auto remover após 3 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// ============================================
// ESTILOS GLOBAIS
// ============================================

// Adicionar animações CSS
const globalStyles = document.createElement('style');
globalStyles.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    /* Estilos para scroll */
    #resultadosTransferencia::-webkit-scrollbar {
        width: 6px;
    }
    #resultadosTransferencia::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #resultadosTransferencia::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    #resultadosTransferencia::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
`;
document.head.appendChild(globalStyles);

// Função para verificar conexão
function verificarConexao() {
    if (!navigator.onLine) {
        mostrarToast('Sem conexão com a internet', 'error');
    }
}

// Verificar conexão ao carregar
window.addEventListener('load', verificarConexao);
window.addEventListener('online', () => mostrarToast('Conexão restabelecida', 'success'));
window.addEventListener('offline', () => mostrarToast('Sem conexão com a internet', 'error'));

// Prevenir fechamento acidental durante transferência
window.addEventListener('beforeunload', function(e) {
    const btn = document.getElementById('btnConfirmarTransferencia');
    if (btn && btn.disabled) {
        e.preventDefault();
        e.returnValue = 'Uma transferência está em andamento. Tem certeza que deseja sair?';
        return e.returnValue;
    }
});

// Adicionar efeito de clique nos botões
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
        e.target.style.transform = 'scale(0.98)';
        setTimeout(() => {
            e.target.style.transform = '';
        }, 150);
    }
});

// ============================================
// CORREÇÃO DE IMAGENS QUEBRADAS EM MEMBROS POPULARES
// ============================================

function corrigirImagensQuebradas() {
    console.log('🔍 Corrigindo imagens quebradas...');
    
    // 1. Imagens do criador do interesse
    document.querySelectorAll('.dono-avatar img, .dono-card img').forEach(img => {
        verificarECorrigirImagem(img, 'dono');
    });
    
    // 2. Imagens dos membros populares
    document.querySelectorAll('.usuario-avatar img, .usuarios-grid img').forEach(img => {
        verificarECorrigirImagem(img, 'membro');
    });
    
    // 3. Imagens nas postagens
    document.querySelectorAll('.usuario-avatar-mini img, .postagem-curtida-item img').forEach(img => {
        verificarECorrigirImagem(img, 'postagem');
    });
    
    // 4. Imagens de usuários em geral
    document.querySelectorAll('.avatar-usuario img').forEach(img => {
        verificarECorrigirImagem(img, 'usuario');
    });
}

function verificarECorrigirImagem(imgElement, tipo) {
    if (!imgElement || imgElement.tagName !== 'IMG') return;
    
    // Se a imagem já falhou, substituir por fallback
    if (imgElement.complete && imgElement.naturalHeight === 0) {
        substituirPorFallback(imgElement, tipo);
        return;
    }
    
    // Adicionar listener de erro
    imgElement.onerror = function() {
        console.log(`❌ Imagem falhou: ${this.src}`);
        substituirPorFallback(this, tipo);
    };
    
    // Adicionar listener de load bem-sucedido
    imgElement.onload = function() {
        console.log(`✅ Imagem carregada: ${this.src}`);
    };
}

function substituirPorFallback(imgElement, tipo) {
    const parent = imgElement.parentElement;
    if (!parent) return;
    
    // Verificar se já tem um fallback
    const fallbacks = parent.querySelectorAll('.avatar-fallback, .material-symbols-outlined');
    if (fallbacks.length > 0) {
        imgElement.style.display = 'none';
        fallbacks.forEach(fallback => {
            if (fallback.classList.contains('avatar-fallback')) {
                fallback.style.display = 'flex';
            } else {
                fallback.style.display = 'block';
            }
        });
        return;
    }
    
    // Criar novo fallback
    const fallback = document.createElement('div');
    fallback.className = 'avatar-fallback';
    
    // Estilos baseados no tipo
    let styles = {
        position: 'absolute',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        background: '#f3f4f6',
        borderRadius: '50%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: '#6b7280'
    };
    
    // Ajustes por tipo
    if (tipo === 'postagem') {
        styles.background = '#e5e7eb';
        styles.fontSize = '1.5rem';
    } else if (tipo === 'dono') {
        styles.background = '#f3f4f6';
        styles.fontSize = '3rem';
    }
    
    // Aplicar estilos
    Object.assign(fallback.style, styles);
    
    // Ícone
    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined';
    icon.textContent = 'account_circle';
    icon.style.fontSize = styles.fontSize || '1.5rem';
    
    fallback.appendChild(icon);
    parent.appendChild(fallback);
    imgElement.style.display = 'none';
}

// Executar quando o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(corrigirImagensQuebradas, 1000); // Aguardar 1 segundo para imagens carregarem
});

// Executar quando todas as imagens carregarem
window.addEventListener('load', function() {
    setTimeout(corrigirImagensQuebradas, 500); // Verificar novamente após load
});

// Executar quando o usuário voltar para a página
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        setTimeout(corrigirImagensQuebradas, 300);
    }
});
</script>
@endsection