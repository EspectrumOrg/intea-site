// selecao-interesse.js - Sistema inteligente de seleção de interesses

console.log('✅ selecao-interesse.js carregado!');

document.addEventListener('DOMContentLoaded', function() {
    // Determinar o contexto atual
    const contexto = determinarContexto();
    console.log('Contexto detectado:', contexto);
    
    // CONFIGURAR FORMULÁRIOS CENTRAIS (feed)
    configurarFormulariosCentrais(contexto);
    
    // CONFIGURAR MODAL (se existir)
    configurarModal(contexto);
});

function determinarContexto() {
    const urlPath = window.location.pathname;
    
    // Modo 1: Interesse Específico
    if (urlPath.includes('/interesse/') || urlPath.includes('/i/')) {
        const interesseSlug = obterInteresseDaURL(urlPath);
        return {
            modo: 'interesse_especifico',
            interesseSlug: interesseSlug
        };
    }
    
    // Modo 2: Feed Personalizado
    if (urlPath.includes('/personalizado') || urlPath === '/personalizado') {
        return {
            modo: 'personalizado',
            multiplaSelecao: true
        };
    }
    
    // Modo 3: Feed Geral (Home, Seguindo)
    return {
        modo: 'geral',
        multiplaSelecao: false
    };
}

function configurarFormulariosCentrais(contexto) {
    // Encontrar TODOS os formulários de postagem na página
    const formularios = document.querySelectorAll('form[action*="/feed"], form[action*="/post"], form[action*="/postagem"]');
    
    console.log('Formulários encontrados:', formularios.length);
    
    formularios.forEach((form, index) => {
        // Pular formulários dentro de modais
        if (form.closest('.modal, [role="dialog"]')) {
            return;
        }
        
        // Configurar baseado no contexto
        switch (contexto.modo) {
            case 'interesse_especifico':
                configurarFormularioInteresseEspecifico(form, contexto);
                break;
            case 'personalizado':
                configurarFormularioPersonalizado(form);
                break;
            case 'geral':
                // Não fazer nada - postagem vai para feed geral
                break;
        }
    });
}

function configurarFormularioInteresseEspecifico(form, contexto) {
    console.log('Configurando formulário para interesse específico:', contexto.interesseSlug);
    
    // Buscar ID do interesse
    buscarInteresseId(contexto.interesseSlug).then(interesseId => {
        if (interesseId) {
            // Adicionar campo hidden com o interesse
            adicionarCampoInteresseFormulario(form, interesseId);
            
            // Atualizar placeholder do textarea discretamente
            const textarea = form.querySelector('textarea[name="texto_postagem"]');
            if (textarea && !textarea.placeholder.includes('interesse')) {
                textarea.placeholder = `No que você está pensando sobre ${contexto.interesseSlug}?`;
            }
        }
    });
}

function configurarFormularioPersonalizado(form) {
    console.log('Configurando formulário para feed personalizado');
    
    // Transformar em seleção múltipla se houver select
    const select = form.querySelector('select[name="interesse_id"]');
    if (select) {
        transformarSelectMultiplo(select);
    }
}

function configurarModal(contexto) {
    const toggleBtn = document.getElementById('toggleInteresse');
    const selectionDiv = document.getElementById('interesseSelection');
    
    if (!toggleBtn || !selectionDiv) return;
    
    switch (contexto.modo) {
        case 'interesse_especifico':
            selectionDiv.style.display = 'none';
            toggleBtn.style.display = 'none';
            
            // Configurar modal quando for aberto
            document.addEventListener('click', function(e) {
                if (e.target.matches('#postagem-modal, [onclick*="abrirModalPostar"]')) {
                    setTimeout(() => {
                        const modalForm = document.querySelector('#postagem-form form, .modal-content form');
                        if (modalForm && contexto.interesseSlug) {
                            buscarInteresseId(contexto.interesseSlug).then(interesseId => {
                                if (interesseId) {
                                    adicionarCampoInteresseFormulario(modalForm, interesseId);
                                }
                            });
                        }
                    }, 100);
                }
            });
            break;
            
        case 'personalizado':
            toggleBtn.style.display = 'flex';
            selectionDiv.style.display = 'none';
            toggleBtn.innerHTML = '<span class="material-symbols-outlined">category</span> Escolher Interesses';
            toggleBtn.style.background = '#8B5CF6';
            
            toggleBtn.addEventListener('click', function() {
                selectionDiv.style.display = selectionDiv.style.display === 'none' ? 'block' : 'none';
                toggleBtn.innerHTML = selectionDiv.style.display === 'none' 
                    ? '<span class="material-symbols-outlined">category</span> Escolher Interesses'
                    : '<span class="material-symbols-outlined">close</span> Fechar Seleção';
                toggleBtn.style.background = selectionDiv.style.display === 'none' ? '#8B5CF6' : '#6B7280';
            });
            
            transformarSelectMultiplo(document.getElementById('interesse_id'));
            break;
            
        case 'geral':
            toggleBtn.style.display = 'flex';
            selectionDiv.style.display = 'none';
            toggleBtn.innerHTML = '<span class="material-symbols-outlined">category</span> Postar em Interesse Específico';
            toggleBtn.style.background = '#FF8C42';
            
            toggleBtn.addEventListener('click', function() {
                selectionDiv.style.display = selectionDiv.style.display === 'none' ? 'block' : 'none';
                toggleBtn.innerHTML = selectionDiv.style.display === 'none' 
                    ? '<span class="material-symbols-outlined">category</span> Postar em Interesse Específico'
                    : '<span class="material-symbols-outlined">close</span> Fechar Seleção';
                toggleBtn.style.background = selectionDiv.style.display === 'none' ? '#FF8C42' : '#6B7280';
            });
            break;
    }
}

function adicionarCampoInteresseFormulario(form, interesseId) {
    // Remover campo existente
    const camposExistentes = form.querySelectorAll('[name="interesse_id"], [name="interesses_ids[]"]');
    camposExistentes.forEach(campo => campo.remove());
    
    // Adicionar novo campo hidden
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'interesse_id';
    hiddenInput.value = interesseId;
    form.appendChild(hiddenInput);
    
    console.log('Campo de interesse adicionado ao formulário:', interesseId);
}

function transformarSelectMultiplo(select) {
    if (!select) return;
    
    select.multiple = true;
    select.name = 'interesses_ids[]';
    select.size = 4;
    
    // Adicionar opção "Todos os meus interesses"
    const optionTodos = document.createElement('option');
    optionTodos.value = 'todos';
    optionTodos.textContent = 'Todos os meus interesses';
    optionTodos.selected = true;
    select.insertBefore(optionTodos, select.firstChild);
}

function buscarInteresseId(slug) {
    return fetch(`/api/interesses/slug/${slug}`)
        .then(response => {
            if (!response.ok) throw new Error('Interesse não encontrado');
            return response.json();
        })
        .then(data => data.id)
        .catch(error => {
            console.error('Erro ao buscar interesse:', error);
            return null;
        });
}

function obterInteresseDaURL(url) {
    if (url.includes('/interesse/')) {
        return url.split('/interesse/')[1].split('/')[0];
    }
    if (url.includes('/i/')) {
        return url.split('/i/')[1].split('/')[0];
    }
    return null;
}