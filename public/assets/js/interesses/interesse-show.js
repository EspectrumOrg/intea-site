// public/assets/js/interesse-show.js

console.log('🎯 interesse-show.js CARREGADO! Iniciando sistema...');

class InteresseShowSystem {
    constructor() {
        // Lista de scripts que DEVEM ser mantidos (não remover)
        this.essentialScripts = [
            'jquery', 'bootstrap', 'app.js', 'main.js', 'auth.js',
            'notifications.js', 'chat.js', 'pusher', 'axios'
        ];
        
        // Lista de scripts conflitantes (remover apenas estes)
        this.conflictingScripts = [
            'seguir-interesse.js',
            'selecao-interesse.js', 
            'modal.js',
            'char-count.js',
            'hashtag-comentario-read.js',
            'create-resposta-comentario-focus.js',
            'char-count-focus.js'
        ];
        
        this.init();
    }

    init() {
        console.log('🔧 Iniciando sistema de interesses...');
        
        // Remover APENAS scripts conflitantes
        this.removeOnlyConflictingScripts();
        
        // Configurar quando DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
        
        // Backup após 1 segundo
        setTimeout(() => {
            console.log('⏰ Executando setup de backup...');
            this.setup();
        }, 1000);
    }

    removeOnlyConflictingScripts() {
        console.log('🚫 Removendo APENAS scripts conflitantes...');
        
        this.conflictingScripts.forEach(scriptName => {
            const scripts = document.querySelectorAll(`script[src*="${scriptName}"]`);
            scripts.forEach(script => {
                console.log(`🔴 Removendo script conflitante: ${scriptName}`);
                script.remove();
            });
        });
        
        console.log('✅ Scripts essenciais preservados!');
    }

    setup() {
        console.log('⚙️ Configurando sistema de interesses...');
        
        this.setupSeguirButtons();
        this.setupActionButtons();
        this.setupHoverEffects();
        
        console.log('✅ Sistema de interesses configurado!');
    }

    setupSeguirButtons() {
        // Encontrar todos os botões de seguir
        const selectors = [
            '.btn-seguir-interesse',
            '[data-interesse-id]',
            '[data-action="seguir"]',
            '[data-action="deixar-seguir"]'
        ];
        
        let allButtons = [];
        selectors.forEach(selector => {
            const buttons = document.querySelectorAll(selector);
            buttons.forEach(btn => allButtons.push(btn));
        });
        
        // Remover duplicatas
        allButtons = [...new Set(allButtons)];
        
        console.log(`🔍 Encontrados ${allButtons.length} botões de seguir`);
        
        allButtons.forEach((button, index) => {
            console.log(`🔘 Botão ${index + 1}:`, {
                id: button.getAttribute('data-interesse-id'),
                text: button.textContent.trim(),
                isSeguindo: button.classList.contains('seguindo')
            });
            
            // Clonar e substituir para remover event listeners antigos
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Adicionar nosso event listener
            newButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('🖱️ Botão de seguir clicado!', newButton);
                this.handleSeguirClick(newButton);
            });
        });
    }

    setupActionButtons() {
        console.log('⚙️ Configurando botões de ação...');
        
        // Botão Compartilhar
        this.setupSingleButton('btnCompartilhar', () => this.compartilharInteresse());
        
        // Botão Criar Postagem
        this.setupSingleButton('btnCriarPostagem', () => this.criarPostagem());
        
        // Botão Criar Primeira Postagem  
        this.setupSingleButton('btnCriarPrimeiraPostagem', () => this.criarPostagem());
    }

    setupSingleButton(buttonId, clickHandler) {
        const button = document.getElementById(buttonId);
        if (button) {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', (e) => {
                e.preventDefault();
                clickHandler();
            });
            console.log(`✅ Botão ${buttonId} configurado`);
        }
    }

    setupHoverEffects() {
        const buttons = document.querySelectorAll('.btn-seguir-interesse, .acao-btn, .nav-item');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                if (!this.disabled) {
                    this.style.transform = 'translateY(-2px)';
                    this.style.transition = 'all 0.3s ease';
                }
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    async handleSeguirClick(button) {
        console.log('🎯 Iniciando ação de seguir/deixar de seguir...');
        
        const interesseId = button.getAttribute('data-interesse-id');
        const estaSeguindo = button.classList.contains('seguindo');
        
        console.log('📊 Dados do botão:', { 
            interesseId, 
            estaSeguindo,
            texto: button.textContent.trim()
        });
        
        if (!interesseId) {
            this.showError('ID do interesse não encontrado');
            return;
        }
        
        // Mostrar estado de loading
        const originalHTML = button.innerHTML;
        const originalDisabled = button.disabled;
        
        button.innerHTML = '<span class="material-symbols-outlined">refresh</span> Carregando...';
        button.disabled = true;
        
        try {
            const url = estaSeguindo 
                ? `/interesses/${interesseId}/deixar-seguir`
                : `/interesses/${interesseId}/seguir`;
            
            console.log('📤 Fazendo requisição para:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _token: this.getCsrfToken(),
                    interesse_id: interesseId
                })
            });
            
            console.log('📥 Status da resposta:', response.status, response.statusText);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Erro na resposta:', errorText);
                throw new Error(`Erro ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📦 Resposta do servidor:', data);
            
            if (data.success || data.sucesso) {
                console.log('✅ Ação realizada com sucesso! Recarregando página...');
                window.location.reload();
            } else {
                const errorMsg = data.message || data.mensagem || 'Erro desconhecido do servidor';
                throw new Error(errorMsg);
            }
            
        } catch (error) {
            console.error('❌ Erro completo:', error);
            this.showError(error.message);
            
            // Restaurar estado original do botão
            button.innerHTML = originalHTML;
            button.disabled = originalDisabled;
        }
    }

    compartilharInteresse() {
        console.log('📤 Iniciando compartilhamento...');
        
        const title = document.querySelector('.interesse-titulo')?.textContent || 'Interesse';
        const text = document.querySelector('.interesse-descricao')?.textContent || '';
        const url = window.location.href;
        
        if (navigator.share) {
            // Compartilhamento nativo
            navigator.share({
                title: title,
                text: text,
                url: url
            }).then(() => {
                console.log('✅ Conteúdo compartilhado com sucesso');
                this.showToast('Conteúdo compartilhado!', 'success');
            }).catch(error => {
                console.log('❌ Compartilhamento cancelado:', error);
                this.copyToClipboard(url);
            });
        } else {
            // Fallback - copiar para clipboard
            this.copyToClipboard(url);
        }
    }

    copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                this.showToast('📋 Link copiado para a área de transferência!', 'success');
            })
            .catch(err => {
                // Fallback para navegadores antigos
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                this.showToast('📋 Link copiado para a área de transferência!', 'success');
            });
    }

    criarPostagem() {
        console.log('📝 Abrindo criação de postagem...');
        
        const interesseId = document.querySelector('[data-interesse-id]')?.getAttribute('data-interesse-id');
        if (interesseId) {
            window.location.href = `/feed/create?interesse_id=${interesseId}`;
        } else {
            window.location.href = '/feed/create';
        }
    }

    getCsrfToken() {
        // Múltiplas formas de obter o token CSRF
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               document.querySelector('input[name="_token"]')?.value || 
               window.csrfToken || 
               '';
    }

    showError(message) {
        console.error('❌ Erro:', message);
        this.showToast(`Erro: ${message}`, 'error');
    }

    showToast(message, type = 'info') {
        // Toast simples - você pode integrar com seu sistema de toasts existente
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'error' ? '#EF4444' : type === 'success' ? '#10B981' : '#3B82F6'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-size: 14px;
            font-weight: 500;
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }
}

// Inicialização segura
console.log('🚀 Preparando inicialização do sistema...');

// Esperar um pouco para garantir que outros scripts essenciais carreguem
setTimeout(() => {
    console.log('🎯 Inicializando sistema de interesses...');
    window.interesseShowSystem = new InteresseShowSystem();
}, 100);

// Exportar funções globais para compatibilidade (se necessário)
window.handleSeguirClick = function(button) {
    if (window.interesseShowSystem) {
        window.interesseShowSystem.handleSeguirClick(button);
    }
};

window.compartilharInteresse = function() {
    if (window.interesseShowSystem) {
        window.interesseShowSystem.compartilharInteresse();
    }
};

window.abrirCriarPostagem = function() {
    if (window.interesseShowSystem) {
        window.interesseShowSystem.criarPostagem();
    }
};

console.log('🎉 Sistema de interesses carregado e pronto!');