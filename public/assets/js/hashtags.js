/**
 * Processa hashtags no texto e aplica estilo
 * @param {string} texto - Texto com hashtags
 * @returns {string} - Texto com hashtags estilizadas
 */
function processarHashtags(texto) {
    return texto.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
}

/**
 * Aplica o processamento de hashtags a todos os elementos com a classe .post-content
 */
function aplicarHashtags() {
    const postContents = document.querySelectorAll('.post-content');
    
    postContents.forEach(content => {
        // Preserva o HTML existente mas processa o texto para hashtags
        const textoOriginal = content.textContent || content.innerText;
        content.innerHTML = processarHashtags(textoOriginal);
    });
}

/**
 * Processa hashtags em um elemento específico
 * @param {HTMLElement} elemento - Elemento HTML a ser processado
 */
function processarElementoHashtags(elemento) {
    const textoOriginal = elemento.textContent || elemento.innerText;
    elemento.innerHTML = processarHashtags(textoOriginal);
}

// Aplicar hashtags quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    aplicarHashtags();
    
    // Observar mudanças no DOM (para posts carregados via AJAX)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const posts = node.querySelectorAll ? node.querySelectorAll('.post-content') : [];
                    posts.forEach(processarElementoHashtags);
                    
                    // Se o próprio node é um post-content
                    if (node.classList && node.classList.contains('post-content')) {
                        processarElementoHashtags(node);
                    }
                }
            });
        });
    });
    
    // Observar mudanças no body
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Para uso global (caso precise chamar manualmente)
window.ProcessadorHashtags = {
    processar: processarHashtags,
    aplicar: aplicarHashtags,
    processarElemento: processarElementoHashtags
};