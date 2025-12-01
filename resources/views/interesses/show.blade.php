@extends('feed.post.template.layout')

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
                        @if($dono->foto && Storage::exists($dono->foto))
                            <img src="{{ asset('storage/' . $dono->foto) }}" alt="{{ $dono->apelido }}">
                        @else
                            <span class="material-symbols-outlined">account_circle</span>
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
                                Criador & Dono
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
                        @if($usuario->foto && Storage::exists($usuario->foto))
                            <img src="{{ asset('storage/' . $usuario->foto) }}" alt="{{ $usuario->apelido }}">
                        @else
                            <span class="material-symbols-outlined">account_circle</span>
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
                                        @if($postagem->usuario->foto && Storage::exists($postagem->usuario->foto))
                                            <img src="{{ asset('storage/' . $postagem->usuario->foto) }}" alt="{{ $postagem->usuario->apelido }}">
                                        @else
                                            <span class="material-symbols-outlined">account_circle</span>
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
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="color: {{ $interesse->cor }};">Transferir Propriedade</h3>
            <button type="button" class="modal-close" onclick="fecharModalTransferir()" style="background: none; border: none; cursor: pointer; color: #666;">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom: 1rem; color: #666;">
                Escolha um novo dono para este interesse. Esta ação é permanente e você perderá o controle total.
            </p>
            
            <div class="form-group">
                <label for="buscarUsuarioTransferir" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar Usuário</label>
                <input type="text" id="buscarUsuarioTransferir" placeholder="Digite nome, usuário ou email..." 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 1rem;">
                <div id="resultadosTransferencia" style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; display: none;"></div>
            </div>
            
            <div id="usuarioSelecionadoInfo" style="display: none; padding: 1rem; background: #f9f9f9; border-radius: 8px; margin-top: 1rem;">
                <p><strong>Novo dono selecionado:</strong> <span id="nomeUsuarioSelecionado"></span></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancelar" onclick="fecharModalTransferir()" style="padding: 0.75rem 1.5rem; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer;">Cancelar</button>
            <button type="button" id="btnConfirmarTransferencia" class="btn-confirmar" onclick="confirmarTransferencia()" 
                    style="padding: 0.75rem 1.5rem; background: {{ $interesse->cor }}; color: white; border: none; border-radius: 8px; cursor: pointer; display: none;">
                Transferir Propriedade
            </button>
        </div>
    </div>
</div>

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

/* Resultados da busca */
.resultado-usuario-transferir {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
}

.resultado-usuario-transferir:hover {
    background: #f9fafb;
}

.usuario-info-transferir {
    display: flex;
    align-items: center;
    gap: 0.75rem;
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
}

.usuario-avatar-transferir img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-detalhes-transferir {
    display: flex;
    flex-direction: column;
}

.usuario-detalhes-transferir strong {
    font-size: 0.9rem;
    color: #1f2937;
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

    // Busca de usuários para transferência
    let buscaTimeout;
    let usuarioSelecionado = null;
    
    const buscarInput = document.getElementById('buscarUsuarioTransferir');
    if (buscarInput) {
        buscarInput.addEventListener('input', function(e) {
            clearTimeout(buscaTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                document.getElementById('resultadosTransferencia').innerHTML = '';
                document.getElementById('resultadosTransferencia').style.display = 'none';
                return;
            }
            
            buscaTimeout = setTimeout(() => {
                buscarUsuariosParaTransferir(query);
            }, 500);
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

// Funções de transferência
function abrirModalTransferir() {
    criarModalTransferir(); // Cria o modal dinamicamente
    document.getElementById('buscarUsuarioTransferir').value = '';
    document.getElementById('resultadosTransferencia').innerHTML = '';
    document.getElementById('resultadosTransferencia').style.display = 'none';
    document.getElementById('usuarioSelecionadoInfo').style.display = 'none';
    document.getElementById('btnConfirmarTransferencia').style.display = 'none';
    usuarioSelecionado = null;
}

function fecharModalTransferir() {
    // Remove completamente o modal do DOM
    document.getElementById('modalTransferirContainer').innerHTML = '';
}

function buscarUsuariosParaTransferir(query) {
    fetch(`/api/usuarios/buscar?q=${encodeURIComponent(query)}&exclude_current=true`)
        .then(response => response.json())
        .then(usuarios => {
            const resultados = document.getElementById('resultadosTransferencia');
            resultados.innerHTML = '';
            
            if (usuarios.length === 0) {
                resultados.innerHTML = '<div style="padding: 2rem; text-align: center; color: #6b7280;">Nenhum usuário encontrado</div>';
                resultados.style.display = 'block';
                return;
            }
            
            usuarios.forEach(usuario => {
                const div = document.createElement('div');
                div.className = 'resultado-usuario-transferir';
                div.innerHTML = `
                    <div class="usuario-info-transferir">
                        <div class="usuario-avatar-transferir">
                            ${usuario.foto ? 
                                `<img src="/storage/${usuario.foto}" alt="${usuario.apelido}" onerror="this.style.display='none'">` : 
                                '<span class="material-symbols-outlined" style="color: #6b7280;">account_circle</span>'
                            }
                        </div>
                        <div class="usuario-detalhes-transferir">
                            <strong>${usuario.apelido || usuario.user}</strong>
                            <span>@${usuario.user}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-selecionar-transferir" onclick="selecionarUsuarioParaTransferir(${usuario.id}, '${usuario.apelido || usuario.user}', '${usuario.user}')">
                        Selecionar
                    </button>
                `;
                
                resultados.appendChild(div);
            });
            
            resultados.style.display = 'block';
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            mostrarToast('Erro ao buscar usuários', 'error');
        });
}

function selecionarUsuarioParaTransferir(usuarioId, nome, usuario) {
    usuarioSelecionado = { id: usuarioId, nome: nome, usuario: usuario };
    
    document.getElementById('nomeUsuarioSelecionado').textContent = `${nome} (@${usuario})`;
    document.getElementById('usuarioSelecionadoInfo').style.display = 'block';
    document.getElementById('btnConfirmarTransferencia').style.display = 'block';
    document.getElementById('resultadosTransferencia').style.display = 'none';
}

function confirmarTransferencia() {
    if (!usuarioSelecionado) return;
    
    if (!confirm(`⚠️ ATENÇÃO: Você está transferindo a propriedade do interesse "${document.querySelector('.interesse-titulo').textContent}" para ${usuarioSelecionado.nome} (@${usuarioSelecionado.usuario}).\n\nEsta ação é PERMANENTE e IRREVERSÍVEL.\n\nVocê perderá o controle total sobre este interesse.\n\nTem certeza que deseja continuar?`)) {
        return;
    }
    
    const interesseSlug = '{{ $interesse->slug }}';
    
    fetch(`/interesses/${interesseSlug}/transferir-propriedade`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            novo_dono_id: usuarioSelecionado.id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            mostrarToast(data.mensagem, 'success');
            fecharModalTransferir();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarToast(data.mensagem || 'Erro ao transferir propriedade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast('Erro ao transferir propriedade', 'error');
    });
}

// Funções auxiliares
function seguirInteresse(interesseId, elemento) {
    fetch(`/interesses/${interesseId}/seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            document.querySelector('.btn-deixar-seguir').addEventListener('click', function() {
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            document.querySelector('.btn-seguir-interesse').addEventListener('click', function() {
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
        fetch(`/interesses/${slug}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (data && data.success) {
                window.location.href = data.redirect || '/interesses';
            } else if (data && data.error) {
                mostrarToast(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarToast('Erro ao deletar interesse', 'error');
        });
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
    `;
    
    toast.textContent = mensagem;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Adicionar keyframes de animação
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

function criarModalTransferir() {
    const modalHTML = `
        <div id="modalTransferir" class="modal" style="display: flex;">
            <div class="modal-content" style="max-width: 500px;">
                <div class="modal-header">
                    <h3 style="color: {{ $interesse->cor }};">Transferir Propriedade</h3>
                    <button type="button" class="modal-close" onclick="fecharModalTransferir()" style="background: none; border: none; cursor: pointer; color: #666;">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 1rem; color: #666;">
                        Escolha um novo dono para este interesse. Esta ação é permanente e você perderá o controle total.
                    </p>
                    
                    <div class="form-group">
                        <label for="buscarUsuarioTransferir" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Buscar Usuário</label>
                        <input type="text" id="buscarUsuarioTransferir" placeholder="Digite nome, usuário ou email..." 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 1rem;">
                        <div id="resultadosTransferencia" style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; display: none;"></div>
                    </div>
                    
                    <div id="usuarioSelecionadoInfo" style="display: none; padding: 1rem; background: #f9f9f9; border-radius: 8px; margin-top: 1rem;">
                        <p><strong>Novo dono selecionado:</strong> <span id="nomeUsuarioSelecionado"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" onclick="fecharModalTransferir()" style="padding: 0.75rem 1.5rem; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer;">Cancelar</button>
                    <button type="button" id="btnConfirmarTransferencia" class="btn-confirmar" onclick="confirmarTransferencia()" 
                            style="padding: 0.75rem 1.5rem; background: {{ $interesse->cor }}; color: white; border: none; border-radius: 8px; cursor: pointer; display: none;">
                        Transferir Propriedade
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('modalTransferirContainer').innerHTML = modalHTML;
    
    // Adicionar event listeners após criar o modal
    document.getElementById('buscarUsuarioTransferir').addEventListener('input', function(e) {
        clearTimeout(window.buscaTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            document.getElementById('resultadosTransferencia').innerHTML = '';
            document.getElementById('resultadosTransferencia').style.display = 'none';
            return;
        }
        
        window.buscaTimeout = setTimeout(() => {
            buscarUsuariosParaTransferir(query);
        }, 500);
    });
}

</script>
@endsection