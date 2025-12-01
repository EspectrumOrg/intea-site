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
        <a href="{{ route('interesses.show', $interesse->slug) }}" class="nav-item active" style="border-color: {{ $interesse->cor }}; color: {{ $interesse->cor }};">
            <span class="material-symbols-outlined">info</span>
            Sobre
        </a>
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

<!-- STYLES -->
<style>
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
                alert('Link copiado para a área de transferência!');
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

    // Botões de seguir interesse (implementação básica)
    document.querySelector('.btn-seguir-interesse')?.addEventListener('click', function() {
        const interesseId = this.getAttribute('data-interesse-id');
        // Implementar lógica AJAX para seguir interesse
        this.innerHTML = '<span class="material-symbols-outlined">check</span>Seguindo';
        this.style.background = '#10B981';
        this.disabled = true;
    });

    document.querySelector('.btn-deixar-seguir')?.addEventListener('click', function() {
        const interesseId = this.getAttribute('data-interesse-id');
        // Implementar lógica AJAX para deixar de seguir
        this.innerHTML = '<span class="material-symbols-outlined">add</span>Seguir';
        this.style.background = '#3B82F6';
    });
});

function confirmarDelecaoInteresse(slug) {
    if (confirm('⚠️ ATENÇÃO: Esta ação é PERMANENTE!\n\nTem certeza que deseja deletar este interesse?')) {
        fetch(`/interesses/${slug}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            }
        });
    }
}
</script>
@endsection