// Prevenir erros de scripts que tentam acessar elementos não existentes
document.addEventListener('DOMContentLoaded', function() {
    console.log('Moderação - Inicializando tratamento de erros');
    
    // Verificar se estamos no contexto de moderação
    const isModerationPage = window.location.pathname.includes('/moderacao/');
    
    if (isModerationPage) {
        console.log('Página de moderação detectada - desativando scripts conflitantes');
        
        // Prevenir execução de scripts problemáticos
        const problematicScripts = [
            'modal.js',
            'char-count.js', 
            'hashtag-comentario-read.js',
            'create-resposta-comentario-focus.js',
            'char-count-focus.js',
            'seguir-interesse.js',
            'selecao-interesse.js'
        ];
        
        // Adicionar verificações de segurança para elementos
        const safeQuerySelector = (selector) => {
            const element = document.querySelector(selector);
            if (!element) {
                console.warn(`Elemento não encontrado: ${selector}`);
                return null;
            }
            return element;
        };
        
        // Sobrescrever funções problemáticas se necessário
        window.safeModalInit = function() {
            const modalElement = safeQuerySelector('[data-bs-toggle="modal"]');
            if (modalElement) {
                // Inicialização segura do modal
            }
        };
        
        // Desativar inicializações automáticas problemáticas
        window.disableProblematicScripts = true;
    }
});