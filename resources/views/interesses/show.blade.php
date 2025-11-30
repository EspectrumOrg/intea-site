@extends('feed.post.template.layout')

@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
<style>
/* CSS para desativar scripts conflitantes */
script[src*="seguir-interesse.js"],
script[src*="selecao-interesse.js"], 
script[src*="modal.js"],
script[src*="char-count.js"],
script[src*="hashtag-comentario-read.js"],
script[src*="create-resposta-comentario-focus.js"],
script[src*="char-count-focus.js"],
script[src*="error-handler.js"],
script[src*="content-script.js"] {
    display: none !important;
}
</style>
@endsection

@section('scripts')
@parent
<script>
// =============================================
// REMOÇÃO COMPLETA DE SCRIPTS CONFLITANTES
// =============================================

// Função para remover scripts conflitantes AGressivamente
function removeConflictingScripts() {
    console.log('🚫 REMOVENDO SCRIPTS CONFLITANTES...');
    
    const scriptsToRemove = [
        'seguir-interesse.js',
        'selecao-interesse.js',
        'modal.js',
        'char-count.js',
        'hashtag-comentario-read.js',
        'create-resposta-comentario-focus.js',
        'char-count-focus.js',
        'error-handler.js',
        'content-script.js'
    ];
    
    scriptsToRemove.forEach(scriptName => {
        // Remover por src
        const scripts = document.querySelectorAll(`script[src*="${scriptName}"]`);
        scripts.forEach(script => {
            console.log(`🔴 Removendo script: ${scriptName}`);
            script.remove();
        });
    });
}

// Executar imediatamente
removeConflictingScripts();

// =============================================
// CONFIGURAÇÃO GLOBAL
// =============================================
window.INTERESSE_CONFIG = {
    id: {{ $interesse->id }},
    nome: '{{ $interesse->nome }}',
    descricao: '{{ $interesse->descricao }}',
    csrfToken: '{{ csrf_token() }}',
    baseUrl: '{{ url("/") }}',
    routes: {
        // CORREÇÃO: Usar URLs diretas em vez de named routes
        seguir: '/interesses/{{ $interesse->id }}/seguir',
        deixarSeguir: '/interesses/{{ $interesse->id }}/deixar-seguir',
        criarPostagem: '{{ route("post.create") }}',
        login: '{{ route("login") }}'
    }
};

// =============================================
// SISTEMA DE INTERESSES - VERSÃO ISOLADA
// =============================================

class InteresseSystem {
    constructor() {
        this.initialized = false;
        this.init();
    }
    
    init() {
        if (this.initialized) return;
        
        console.log('🎯 INICIANDO SISTEMA DE INTERESSES...');
        
        // Esperar o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
        
        this.initialized = true;
    }
    
    setup() {
        console.log('🔧 CONFIGURANDO BOTÕES...');
        
        this.setupSeguirButtons();
        this.setupActionButtons();
        this.setupHoverEffects();
        
        console.log('✅ SISTEMA DE INTERESSES PRONTO!');
    }
    
    setupSeguirButtons() {
        // Selecionar TODOS os botões de seguir
        const selectors = [
            '.btn-seguir-interesse',
            '[data-interesse-id]',
            '[data-action]'
        ];
        
        let allButtons = [];
        
        selectors.forEach(selector => {
            try {
                const buttons = document.querySelectorAll(selector);
                buttons.forEach(btn => allButtons.push(btn));
            } catch (e) {
                // Ignorar seletores inválidos
            }
        });
        
        // Remover duplicatas
        allButtons = [...new Set(allButtons)];
        
        console.log(`🔍 Encontrados ${allButtons.length} botões de seguir:`, allButtons);
        
        allButtons.forEach(button => {
            // Clonar e substituir para remover event listeners antigos
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Adicionar nosso event listener
            newButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                console.log('🖱️ Botão clicado (Sistema Principal):', newButton);
                this.handleSeguir(newButton);
            });
            
            // Marcar como processado
            newButton.setAttribute('data-processed', 'true');
        });
    }
    
    setupActionButtons() {
        // Botão Compartilhar
        const shareButtons = [
            document.getElementById('btnCompartilhar'),
            ...document.querySelectorAll('[onclick*="compartilharInteresse"]')
        ].filter(btn => btn);
        
        shareButtons.forEach(btn => {
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.compartilharInteresse();
            });
        });
        
        // Botões Criar Postagem
        const createButtons = [
            document.getElementById('btnCriarPostagem'),
            document.getElementById('btnCriarPrimeiraPostagem'),
            ...document.querySelectorAll('[onclick*="abrirCriarPostagem"]')
        ].filter(btn => btn);
        
        createButtons.forEach(btn => {
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.abrirCriarPostagem();
            });
        });
    }
    
    setupHoverEffects() {
        const buttons = document.querySelectorAll('.btn-seguir-interesse, .acao-btn, .nav-item');
        
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                if (!this.disabled) {
                    this.style.transform = 'translateY(-2px)';
                }
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    async handleSeguir(button) {
        console.log('🎯 HANDLE SEGUIR CHAMADO:', button);
        
        const interesseId = button.getAttribute('data-interesse-id') || window.INTERESSE_CONFIG.id;
        const estaSeguindo = button.classList.contains('seguindo');
        
        console.log('📊 Estado:', { interesseId, estaSeguindo });
        
        if (!interesseId) {
            this.showError('ID do interesse não encontrado');
            return;
        }
        
        // Mostrar loading
        const originalHTML = button.innerHTML;
        button.innerHTML = '<span class="material-symbols-outlined">refresh</span> Carregando...';
        button.disabled = true;
        
        try {
            const url = estaSeguindo 
                ? window.INTERESSE_CONFIG.routes.deixarSeguir
                : window.INTERESSE_CONFIG.routes.seguir;
            
            console.log('📤 Enviando para:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.INTERESSE_CONFIG.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `_token=${encodeURIComponent(window.INTERESSE_CONFIG.csrfToken)}`
            });
            
            console.log('📥 Status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Erro na resposta:', errorText);
                throw new Error(`Erro ${response.status}: ${errorText}`);
            }
            
            const data = await response.json();
            console.log('📦 Resposta JSON:', data);
            
            if (data.success || data.sucesso) {
                // Recarregar para atualizar tudo
                window.location.reload();
            } else {
                const errorMsg = data.message || data.mensagem || 'Erro desconhecido';
                throw new Error(errorMsg);
            }
            
        } catch (error) {
            console.error('❌ Erro completo:', error);
            
            if (error.message.includes('401') || error.message.includes('autenticado')) {
                window.location.href = window.INTERESSE_CONFIG.routes.login;
                return;
            }
            
            this.showError(error.message);
            button.innerHTML = originalHTML;
            button.disabled = false;
        }
    }
    
    compartilharInteresse() {
        const shareData = {
            title: window.INTERESSE_CONFIG.nome,
            text: window.INTERESSE_CONFIG.descricao,
            url: window.location.href
        };
        
        if (navigator.share && navigator.canShare(shareData)) {
            navigator.share(shareData)
                .then(() => this.showToast('Conteúdo compartilhado!', 'success'))
                .catch(() => this.copyToClipboard());
        } else {
            this.copyToClipboard();
        }
    }
    
    copyToClipboard() {
        navigator.clipboard.writeText(window.location.href)
            .then(() => this.showToast('Link copiado!', 'success'))
            .catch(() => {
                // Fallback
                const input = document.createElement('input');
                input.value = window.location.href;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                this.showToast('Link copiado!', 'success');
            });
    }
    
    abrirCriarPostagem() {
        window.location.href = `${window.INTERESSE_CONFIG.routes.criarPostagem}?interesse_id=${window.INTERESSE_CONFIG.id}`;
    }
    
    showToast(message, type = 'info') {
        // Remover toasts existentes
        const existingToasts = document.querySelectorAll('.toast-message');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${type === 'success' ? '✅' : '📋'}</span>
            <span class="toast-text">${message}</span>
        `;
        
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: type === 'success' ? '#10B981' : '#3B82F6',
            color: 'white',
            padding: '12px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            zIndex: '10000',
            display: 'flex',
            alignItems: 'center',
            gap: '10px',
            fontSize: '14px',
            fontWeight: '500',
            animation: 'slideInRight 0.3s ease'
        });
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    showError(message) {
        this.showToast(`Erro: ${message}`, 'error');
    }
}

// =============================================
// INICIALIZAÇÃO AGGRESSIVA
// =============================================

// Sobrescrever funções globais conflitantes
window.handleSeguirClick = function(element) {
    console.log('🎯 handleSeguirClick GLOBAL chamado');
    const system = window.interesseSystem;
    if (system) system.handleSeguir(element);
};

window.compartilharInteresse = function() {
    console.log('🎯 compartilharInteresse GLOBAL chamado');
    const system = window.interesseSystem;
    if (system) system.compartilharInteresse();
};

window.abrirCriarPostagem = function() {
    console.log('🎯 abrirCriarPostagem GLOBAL chamado');
    const system = window.interesseSystem;
    if (system) system.abrirCriarPostagem();
};

// Inicializar sistema
console.log('🚀 INICIALIZANDO SISTEMA DE INTERESSES...');
window.interesseSystem = new InteresseSystem();

// Backup: inicializar após um delay
setTimeout(() => {
    console.log('⏰ INICIALIZAÇÃO DE BACKUP...');
    if (window.interesseSystem && !window.interesseSystem.initialized) {
        window.interesseSystem.setup();
    }
}, 500);
</script>

<style>
/* Animações do Toast */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.toast-message {
    animation: slideInRight 0.3s ease;
}

.toast-message.toast-error {
    background: #EF4444 !important;
}

/* Garantir que botões sejam clicáveis */
.btn-seguir-interesse, 
.acao-btn, 
.nav-item,
button[data-interesse-id] {
    cursor: pointer !important;
}

.btn-seguir-interesse:disabled {
    opacity: 0.6;
    cursor: not-allowed !important;
}

/* Esconder scripts conflitantes */
script[src*="seguir-interesse.js"],
script[src*="selecao-interesse.js"] {
    display: none !important;
}
</style>
@endsection

@section('main')
<div class="container-post">
    <!-- Header do Interesse -->
    <div class="interesse-header-main">
        <div class="interesse-header-content">
            <div class="interesse-avatar" style="background-color: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                @if($interesse->is_custom_icone && $interesse->icone)
                    <img src="{{ $interesse->icone }}" alt="{{ $interesse->nome }}" style="width: 80px; height: 80px; border-radius: 20px;" onerror="this.style.display='none'">
                @else
                    <span class="material-symbols-outlined" style="font-size: 3rem;">{{ $interesse->icone ?? 'tag' }}</span>
                @endif
            </div>
            <div class="interesse-info-main">
                <h1 class="interesse-titulo">{{ $interesse->nome }}</h1>
                <p class="interesse-descricao">{{ $interesse->descricao }}</p>
                <div class="interesse-stats-main">
                    <div class="stat-item">
                        <span class="material-symbols-outlined">people</span>
                        <span class="stat-number">{{ $estatisticas['membros'] }}</span>
                        <span class="stat-label">membros</span>
                    </div>
                    <div class="stat-item">
                        <span class="material-symbols-outlined">chat</span>
                        <span class="stat-number">{{ $estatisticas['postagens'] }}</span>
                        <span class="stat-label">postagens</span>
                    </div>
                    <div class="stat-item">
                        <span class="material-symbols-outlined">shield</span>
                        <span class="stat-number">{{ $estatisticas['moderadores'] }}</span>
                        <span class="stat-label">moderadores</span>
                    </div>
                    @if($interesse->destaque)
                    <div class="stat-item destaque">
                        <span class="material-symbols-outlined">star</span>
                        <span class="stat-label">Em destaque</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="interesse-actions-main">
            @auth
                @if($usuarioSegue)
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
                
                @if($usuarioEhModerador || auth()->user()->isAdministrador())
                    <a href="{{ route('moderacao.painel', $interesse->slug) }}" class="btn-moderar">
                        <span class="material-symbols-outlined">shield</span>
                        Moderar
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-seguir-interesse">
                    <span class="material-symbols-outlined">login</span>
                    Entrar para seguir
                </a>
            @endauth
        </div>
    </div>

    <!-- Navegação Simplificada -->
    <div class="interesse-navigation">
        <a href="{{ route('post.interesse', $interesse->slug) }}" class="nav-item active">
            <span class="material-symbols-outlined">feed</span>
            Feed do Interesse
        </a>
        @if($usuarioEhModerador)
        <a href="{{ route('moderacao.painel', $interesse->slug) }}" class="nav-item moderacao">
            <span class="material-symbols-outlined">shield</span>
            Moderação
        </a>
        @endif
    </div>

    <!-- Layout em Coluna Única -->
    <div class="interesse-content-single-column">
        
        <!-- Sobre o Interesse -->
        @if($interesse->sobre)
        <div class="content-section">
            <div class="section-header">
                <h2>Sobre este interesse</h2>
            </div>
            <div class="section-content">
                <p>{{ $interesse->sobre }}</p>
                <div class="interesse-meta-grid">
                    <div class="meta-card">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <div class="meta-info">
                            <strong>Criado em</strong>
                            <span>{{ $estatisticas['criado_em'] }}</span>
                        </div>
                    </div>
                    <div class="meta-card">
                        <span class="material-symbols-outlined">visibility</span>
                        <div class="meta-info">
                            <strong>Status</strong>
                            <span>{{ $estatisticas['ativo'] }}</span>
                        </div>
                    </div>
                    <div class="meta-card">
                        <span class="material-symbols-outlined">moderator</span>
                        <div class="meta-info">
                            <strong>Moderação</strong>
                            <span>{{ $estatisticas['moderacao_ativa'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <!-- Usuários Populares -->
        @if($usuariosPopulares->count() > 0)
        <div class="content-section">
            <div class="section-header">
                <h2>Membros Populares</h2>
            </div>
            <div class="section-content">
                <div class="usuarios-grid">
                    @foreach($usuariosPopulares as $usuario)
                    <div class="usuario-card">
                        <div class="usuario-avatar">
                            @if($usuario->foto && Storage::exists($usuario->foto))
                                <img src="{{ asset('storage/' . $usuario->foto) }}" alt="{{ $usuario->apelido }}" onerror="this.style.display='none'">
                            @endif
                            <span class="material-symbols-outlined">account_circle</span>
                        </div>
                        <div class="usuario-details">
                            <strong class="usuario-nome">{{ $usuario->apelido ?: $usuario->user }}</strong>
                            <span class="usuario-stats">{{ $usuario->postagens_count }} postagens</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Postagens Destacadas -->
        @if($postagensDestacadas->count() > 0)
        <div class="content-section">
            <div class="section-header">
                <h2>Postagens em Destaque</h2>
            </div>
            <div class="section-content">
                <div class="postagens-destaque-grid">
                    @foreach($postagensDestacadas as $postagem)
                    <div class="postagem-destaque-card">
                        <div class="postagem-content">
                            <p class="postagem-texto">{{ Str::limit($postagem->texto_postagem, 120) }}</p>
                            <div class="postagem-stats">
                                <span class="stat">
                                    <span class="material-symbols-outlined">thumb_up</span>
                                    {{ $postagem->curtidas_count }}
                                </span>
                                <span class="stat">
                                    <span class="material-symbols-outlined">chat_bubble</span>
                                    {{ $postagem->comentarios_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Postagens Mais Curtidas -->
        <div class="content-section">
            <div class="section-header">
                <h2>Postagens Mais Curtidas</h2>
                <span class="section-badge">{{ $postagensMaisCurtidas->count() }} postagens</span>
            </div>
            <div class="section-content">
                @if($postagensMaisCurtidas->count() > 0)
                    <div class="postagens-mais-curtidas">
                        @foreach($postagensMaisCurtidas as $postagem)
                            <div class="postagem-curtida-item">
                                <div class="postagem-header">
                                    <div class="usuario-info">
                                        <div class="usuario-avatar-mini">
                                            @if($postagem->usuario->foto && Storage::exists($postagem->usuario->foto))
                                                <img src="{{ asset('storage/' . $postagem->usuario->foto) }}" alt="{{ $postagem->usuario->apelido }}" onerror="this.style.display='none'">
                                            @endif
                                            <span class="material-symbols-outlined">account_circle</span>
                                        </div>
                                        <div class="usuario-detalhes">
                                            <strong>{{ $postagem->usuario->apelido ?: $postagem->usuario->user }}</strong>
                                            <span>{{ $postagem->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="curtidas-count">
                                        <span class="material-symbols-outlined" style="color: #3B82F6;">thumb_up</span>
                                        <strong>{{ $postagem->curtidas_count }}</strong>
                                        <span>curtidas</span>
                                    </div>
                                </div>
                                
                                <div class="postagem-conteudo">
                                    <p class="postagem-texto">{{ $postagem->texto_postagem }}</p>
                                    
                                    @if($postagem->imagens->count() > 0)
                                    <div class="postagem-imagens">
                                        @foreach($postagem->imagens->take(1) as $imagem)
                                            <img src="{{ asset('storage/' . $imagem->caminho_imagem) }}" alt="Imagem da postagem" class="postagem-imagem" onerror="this.style.display='none'">
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="postagem-footer">
                                    <div class="postagem-stats-completo">
                                        <span class="stat">
                                            <span class="material-symbols-outlined">thumb_up</span>
                                            {{ $postagem->curtidas_count }} curtidas
                                        </span>
                                        <span class="stat">
                                            <span class="material-symbols-outlined">chat_bubble</span>
                                            {{ $postagem->comentarios_count }} comentários
                                        </span>
                                    </div>
                                    <a href="{{ route('post.read', $postagem->id) }}" class="btn-ver-postagem">
                                        Ver Postagem Completa
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span class="material-symbols-outlined">thumb_up</span>
                        <h3>Nenhuma postagem ainda</h3>
                        <p>Seja o primeiro a compartilhar algo neste interesse!</p>
                        @auth
                            @if($interesse->usuarioPodePostar(auth()->id()))
                                <button class="btn-primary-large" id="btnCriarPrimeiraPostagem">
                                    <span class="material-symbols-outlined">add</span>
                                    Criar Primeira Postagem
                                </button>
                            @else
                                <p class="text-muted">Você não tem permissão para postar neste interesse no momento.</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary-large">
                                <span class="material-symbols-outlined">login</span>
                                Entrar para postar
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>


<style>
/* Reset e layout principal */
.container-post {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Header do Interesse */
.interesse-header-main {
    background: var(--branco);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #3B82F6;
}

.interesse-header-content {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.interesse-avatar {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.interesse-info-main {
    flex: 1;
}

.interesse-titulo {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #333;
    line-height: 1.2;
}

.interesse-descricao {
    color: #666;
    font-size: 1.2rem;
    margin: 0 0 1.5rem 0;
    line-height: 1.4;
}

.interesse-stats-main {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e1e5e9;
}

.stat-item.destaque {
    background: #fffbeb;
    border-color: #f59e0b;
    color: #f59e0b;
}

.stat-number {
    font-weight: 700;
    font-size: 1.1rem;
    color: #333;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.interesse-actions-main {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-seguir-interesse {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #3B82F6;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-seguir-interesse:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-seguir-interesse.seguindo {
    background: #10B981;
}

.btn-seguir-interesse.seguindo:hover {
    background: #059669;
}

.btn-seguir-interesse:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn-moderar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-moderar:hover {
    background: #e1e5e9;
    color: #374151;
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
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-item:hover,
.nav-item.active {
    border-color: #3B82F6;
    color: #3B82F6;
}

.nav-item.active {
    background: #3B82F6;
    color: white;
}

.nav-item.moderacao {
    border-color: #ef4444;
    color: #ef4444;
}

.nav-item.moderacao:hover {
    background: #ef4444;
    color: white;
}

/* Layout de Conteúdo */
.interesse-content-single-column {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.content-section {
    background: var(--branco);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid #e1e5e9;
}

.section-header h2 {
    margin: 0;
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
}

.section-badge {
    background: #3B82F6;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.section-content {
    padding: 2rem;
}

/* Meta Grid */
.interesse-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.meta-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e1e5e9;
}

.meta-card .material-symbols-outlined {
    color: #3B82F6;
    font-size: 1.5rem;
}

.meta-info {
    display: flex;
    flex-direction: column;
}

.meta-info strong {
    font-size: 0.9rem;
    color: #666;
}

.meta-info span {
    font-weight: 600;
    color: #333;
}

/* Ações Grid */
.acoes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.acao-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    justify-content: center;
    text-align: center;
}

.btn-primary {
    background: #3B82F6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e1e5e9;
}

.btn-secondary:hover {
    background: #e1e5e9;
    color: #374151;
}

.btn-success {
    background: #10B981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

/* Usuários Grid */
.usuarios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.usuario-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
}

.usuario-card:hover {
    background: #e1e5e9;
    transform: translateY(-2px);
}

.usuario-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e1e5e9;
    flex-shrink: 0;
}

.usuario-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-details {
    flex: 1;
}

.usuario-nome {
    display: block;
    font-size: 1rem;
    color: #333;
    margin-bottom: 0.25rem;
}

.usuario-stats {
    font-size: 0.8rem;
    color: #666;
}

/* Postagens Destaque */
.postagens-destaque-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.postagem-destaque-card {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid #3B82F6;
    transition: all 0.3s ease;
}

.postagem-destaque-card:hover {
    background: #e1e5e9;
    transform: translateY(-2px);
}

.postagem-texto {
    margin: 0 0 1rem 0;
    color: #333;
    line-height: 1.4;
}

.postagem-stats {
    display: flex;
    gap: 1rem;
}

.postagem-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    color: #666;
}

/* Postagens Mais Curtidas */
.postagens-mais-curtidas {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.postagem-curtida-item {
    background: #f8fafc;
    border: 1px solid #e1e5e9;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.postagem-curtida-item:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e1e5e9;
    flex-shrink: 0;
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
    color: #333;
}

.usuario-detalhes span {
    font-size: 0.8rem;
    color: #666;
}

.curtidas-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid #e1e5e9;
}

.curtidas-count strong {
    font-size: 1.1rem;
    color: #333;
}

.curtidas-count span {
    font-size: 0.8rem;
    color: #666;
}

.postagem-conteudo {
    margin-bottom: 1rem;
}

.postagem-texto {
    color: #333;
    line-height: 1.5;
    margin-bottom: 1rem;
    word-wrap: break-word;
}

.postagem-imagens {
    margin-top: 1rem;
}

.postagem-imagem {
    width: 100%;
    max-width: 300px;
    border-radius: 8px;
    object-fit: cover;
}

.postagem-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e1e5e9;
}

.postagem-stats-completo {
    display: flex;
    gap: 1.5rem;
}

.postagem-stats-completo .stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #666;
}

.btn-ver-postagem {
    padding: 0.5rem 1rem;
    background: #3B82F6;
    color: white;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-ver-postagem:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.empty-state .material-symbols-outlined {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

.empty-state p {
    margin-bottom: 1.5rem;
}

.btn-primary-large {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: #3B82F6;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary-large:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.text-muted {
    color: #666;
    font-style: italic;
}

/* Responsividade */
@media (max-width: 768px) {
    .interesse-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .interesse-avatar {
        align-self: center;
    }
    
    .interesse-stats-main {
        justify-content: center;
    }
    
    .interesse-actions-main {
        justify-content: center;
    }
    
    .interesse-navigation {
        justify-content: center;
    }
    
    .section-content {
        padding: 1.5rem;
    }
    
    .section-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
    
    .acoes-grid {
        grid-template-columns: 1fr;
    }
    
    .usuarios-grid {
        grid-template-columns: 1fr;
    }
    
    .postagens-destaque-grid {
        grid-template-columns: 1fr;
    }
    
    .interesse-meta-grid {
        grid-template-columns: 1fr;
    }
    
    .postagem-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .curtidas-count {
        align-self: flex-start;
    }
    
    .postagem-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .postagem-stats-completo {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .interesse-header-main {
        padding: 1.5rem;
    }
    
    .interesse-titulo {
        font-size: 2rem;
    }
    
    .interesse-descricao {
        font-size: 1.1rem;
    }
    
    .stat-item {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }
    
    .nav-item {
        flex: 1;
        justify-content: center;
        min-width: 140px;
    }
    
    .postagem-curtida-item {
        padding: 1rem;
    }
    
    .section-header {
        padding: 1rem 1.5rem;
    }
}
</style>
@endsection