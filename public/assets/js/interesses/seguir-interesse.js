/**
 * Sistema de Seguir/Deixar de Seguir Interesses
 * Arquivo: assets/js/interesses/seguir-interesse.js
 */

console.log('✅ seguir-interesse.js carregado!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== SISTEMA DE INTERESSES INICIADO ===');
    
    inicializarFiltros();
    inicializarBotoesSeguir();
    inicializarBotoesDeixarSeguir();
    
    // Log para debug
    console.log('Filtros encontrados:', document.querySelectorAll('.filter-btn').length);
    console.log('Botões Seguir encontrados:', document.querySelectorAll('.btn-seguir-interesse').length);
    console.log('Botões Deixar Seguir encontrados:', document.querySelectorAll('.btn-deixar-seguir').length);
});

/**
 * Inicializa o sistema de filtros
 */
function inicializarFiltros() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const interesseCards = document.querySelectorAll('.interesse-card-full');
    
    if (filterBtns.length === 0 || interesseCards.length === 0) {
        console.log('Nenhum filtro ou card encontrado para inicializar');
        return;
    }
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            console.log('Filtro aplicado:', filter);
            
            // Ativar botão
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar cards
            interesseCards.forEach(card => {
                switch (filter) {
                    case 'all':
                        card.style.display = 'flex';
                        break;
                    case 'destaque':
                        card.style.display = card.dataset.category === 'destaque' ? 'flex' : 'none';
                        break;
                    case 'popular':
                        card.style.display = card.dataset.popular === 'popular' ? 'flex' : 'none';
                        break;
                    default:
                        card.style.display = 'flex';
                }
            });
        });
    });
}

/**
 * Inicializa os botões de Seguir
 */
function inicializarBotoesSeguir() {
    document.querySelectorAll('.btn-seguir-interesse').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botão SEGUIR clicado:', this);
            
            const interesseId = this.dataset.interesseId;
            if (!interesseId) {
                console.error('ID do interesse não encontrado');
                mostrarMensagemErro('Erro: ID do interesse não encontrado');
                return;
            }
            
            seguirInteresse(interesseId, this);
        });
    });
}

/**
 * Inicializa os botões de Deixar de Seguir
 */
function inicializarBotoesDeixarSeguir() {
    document.querySelectorAll('.btn-deixar-seguir').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botão DEIXAR DE SEGUIR clicado:', this);
            
            const interesseId = this.dataset.interesseId;
            if (!interesseId) {
                console.error('ID do interesse não encontrado');
                mostrarMensagemErro('Erro: ID do interesse não encontrado');
                return;
            }
            
            // Confirmação antes de deixar de seguir
            if (!confirm('Tem certeza que deseja deixar de seguir este interesse?')) {
                return;
            }
            
            deixarSeguirInteresse(interesseId, this);
        });
    });
}

/**
 * Função para seguir um interesse
 */
function seguirInteresse(interesseId, buttonElement) {
    console.log('Iniciando seguir interesse:', interesseId);
    
    // Mostrar loading no botão
    const textoOriginal = buttonElement.innerHTML;
    buttonElement.innerHTML = '<span class="material-symbols-outlined">sync</span> Processando...';
    buttonElement.disabled = true;
    
    fetch(`/interesses/${interesseId}/seguir`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            notificacoes: true
        })
    })
    .then(response => {
        console.log('Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta do servidor:', data);
        
        if (data.sucesso) {
            mostrarMensagemSucesso(data.mensagem);
            
            // Atualizar UI se necessário (antes do reload)
            if (buttonElement) {
                buttonElement.innerHTML = '<span class="material-symbols-outlined">check</span> Seguindo';
                buttonElement.classList.add('seguindo');
            }
            
            // Recarregar a página para atualizar toda a interface
            setTimeout(() => {
                window.location.reload();
            }, 1500);
            
        } else {
            mostrarMensagemErro(data.mensagem);
            // Restaurar botão em caso de erro
            if (buttonElement) {
                buttonElement.innerHTML = textoOriginal;
                buttonElement.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        mostrarMensagemErro('Erro de conexão: ' + error.message);
        
        // Restaurar botão em caso de erro
        if (buttonElement) {
            buttonElement.innerHTML = textoOriginal;
            buttonElement.disabled = false;
        }
    });
}

/**
 * Função para deixar de seguir um interesse
 */
function deixarSeguirInteresse(interesseId, buttonElement) {
    console.log('Iniciando deixar de seguir interesse:', interesseId);
    
    // Mostrar loading no botão
    const textoOriginal = buttonElement.innerHTML;
    buttonElement.innerHTML = '<span class="material-symbols-outlined">sync</span> Processando...';
    buttonElement.disabled = true;
    
    fetch(`/interesses/${interesseId}/deixar-seguir`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta do servidor:', data);
        
        if (data.sucesso) {
            mostrarMensagemSucesso(data.mensagem);
            
            // Atualizar UI se necessário (antes do reload)
            if (buttonElement) {
                buttonElement.innerHTML = '<span class="material-symbols-outlined">add</span> Seguir';
                buttonElement.classList.remove('seguindo');
            }
            
            // Recarregar a página para atualizar toda a interface
            setTimeout(() => {
                window.location.reload();
            }, 1500);
            
        } else {
            mostrarMensagemErro(data.mensagem);
            // Restaurar botão em caso de erro
            if (buttonElement) {
                buttonElement.innerHTML = textoOriginal;
                buttonElement.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        mostrarMensagemErro('Erro de conexão: ' + error.message);
        
        // Restaurar botão em caso de erro
        if (buttonElement) {
            buttonElement.innerHTML = textoOriginal;
            buttonElement.disabled = false;
        }
    });
}

/**
 * Obtém o token CSRF
 */
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        return metaTag.getAttribute('content');
    }
    
    // Fallback para Laravel
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) {
        return tokenInput.value;
    }
    
    console.error('Token CSRF não encontrado');
    return '';
}

/**
 * Mostra mensagem de sucesso
 */
function mostrarMensagemSucesso(mensagem) {
    console.log('Sucesso:', mensagem);
    criarMensagem(mensagem, 'success');
}

/**
 * Mostra mensagem de erro
 */
function mostrarMensagemErro(mensagem) {
    console.error('Erro:', mensagem);
    criarMensagem(mensagem, 'error');
}

/**
 * Cria e exibe mensagem na tela
 */
function criarMensagem(mensagem, tipo) {
    // Remover mensagens existentes
    document.querySelectorAll('.interesse-alerta-temporario').forEach(el => el.remove());
    
    const alerta = document.createElement('div');
    alerta.className = `interesse-alerta-temporario interesse-alerta-${tipo}`;
    alerta.textContent = mensagem;
    
    // Estilos da mensagem
    alerta.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        z-index: 10000;
        background: ${tipo === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${tipo === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${tipo === 'success' ? '#c3e6cb' : '#f5c6cb'};
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 400px;
        word-wrap: break-word;
        font-family: system-ui, -apple-system, sans-serif;
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-remover após 3 segundos
    setTimeout(() => {
        if (alerta.parentNode) {
            alerta.parentNode.removeChild(alerta);
        }
    }, 3000);
}