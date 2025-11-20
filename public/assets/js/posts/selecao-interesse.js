// selecao-interesse.js - Controle de seleção de interesses no modal de postagem

console.log('✅ selecao-interesse.js carregado!');

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleInteresse');
    const selectionDiv = document.getElementById('interesseSelection');
    
    if (!toggleBtn || !selectionDiv) {
        console.log('Elementos de seleção de interesse não encontrados');
        return;
    }
    
    // Toggle da seleção de interesses
    toggleBtn.addEventListener('click', function() {
        if (selectionDiv.style.display === 'none') {
            selectionDiv.style.display = 'block';
            toggleBtn.innerHTML = '<span class="material-symbols-outlined">close</span> Remover de Interesse';
            toggleBtn.style.background = '#6B7280';
        } else {
            selectionDiv.style.display = 'none';
            toggleBtn.innerHTML = '<span class="material-symbols-outlined">category</span> Adicionar a Interesse';
            toggleBtn.style.background = '#FF8C42';
            document.getElementById('interesse_id').value = '';
        }
    });
    
    // Verificar se estamos em uma página de interesse específico
    const urlPath = window.location.pathname;
    const interesseSlug = obterInteresseDaURL(urlPath);
    
    if (interesseSlug) {
        console.log('Detectado interesse na URL:', interesseSlug);
        // Aqui você pode buscar o ID do interesse pelo slug se necessário
        // Por enquanto, vamos apenas marcar visualmente
        selectionDiv.style.display = 'block';
        toggleBtn.innerHTML = '<span class="material-symbols-outlined">category</span> Postando neste interesse';
        toggleBtn.style.background = '#10B981';
        toggleBtn.disabled = true;
        
        // Adicionar campo hidden com o interesse (se tiver o ID)
        adicionarCampoInteresseHidden(interesseSlug);
    }
});

function obterInteresseDaURL(url) {
    // Verificar padrões de URL de interesse
    if (url.includes('/interesse/')) {
        return url.split('/interesse/')[1];
    }
    if (url.includes('/i/')) {
        return url.split('/i/')[1];
    }
    return null;
}

function adicionarCampoInteresseHidden(interesseSlug) {
    // Remover campo existente se houver
    const campoExistente = document.getElementById('interesse_slug_hidden');
    if (campoExistente) {
        campoExistente.remove();
    }
    
    // Adicionar campo hidden com o slug do interesse
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'interesse_slug';
    hiddenInput.id = 'interesse_slug_hidden';
    hiddenInput.value = interesseSlug;
    
    const form = document.querySelector('#postagem-form form');
    if (form) {
        form.appendChild(hiddenInput);
    }
}