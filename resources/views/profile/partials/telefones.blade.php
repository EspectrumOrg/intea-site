<!-- resources/views/profile/partials/telefones.blade.php -->
<section class="perfil-section">
    <header>
        <h2>{{ __('Telefones') }}</h2>
        <p>{{ __('Gerencie seus números de telefone e defina qual é o principal.') }}</p>
    </header>

    <!-- Lista de telefones -->
    <div class="telefones-list mb-4">
        @forelse($telefones as $telefone)
            <div class="telefone-item" data-id="{{ $telefone->id }}">
                <div class="telefone-info">
                    <div class="telefone-details">
                        <strong class="telefone-numero">{{ $telefone->numero_telefone }}</strong>
                        <span class="telefone-tipo">{{ ucfirst($telefone->tipo_telefone) }}</span>
                        @if($telefone->is_principal)
                            <span class="badge-primary">Principal</span>
                        @endif
                    </div>
                    <div class="telefone-actions">
                        @if(!$telefone->is_principal)
                            <button type="button" class="btn-action" onclick="telefoneDefinirPrincipal('{{ $telefone->id }}')" title="Definir como principal">
                                <span class="material-symbols-outlined">star</span>
                            </button>
                        @endif
                        <button type="button" class="btn-action" onclick="telefoneEditar('{{ $telefone->id }}')" title="Editar">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button type="button" class="btn-action btn-danger" onclick="telefoneExcluir('{{ $telefone->id }}')" title="Excluir">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-telefones">
                <p>{{ __('Nenhum telefone cadastrado.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Botão para adicionar telefone -->
    <div class="flex">
        <button type="button" class="btn-primary" onclick="telefoneAbrirModal()">
            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 5px;">add</span>
            {{ __('Adicionar Telefone') }}
        </button>
    </div>
</section>

<!-- Modal para adicionar/editar telefone - FORA DA SEÇÃO -->
<div id="modalTelefoneContainer" class="modal-telefone-overlay" style="display: none;">
    <div class="modal-telefone-content">
        <div class="modal-telefone-header">
            <h4 id="modalTelefoneTitulo">{{ __('Adicionar Telefone') }}</h4>
            <button type="button" class="modal-telefone-close" onclick="telefoneFecharModal()">&times;</button>
        </div>
        
        <form id="formTelefone" onsubmit="telefoneSalvar(event); return false;">
            <div class="modal-telefone-body">
                <input type="hidden" id="telefoneId" name="id">
                
                <div class="mb-3">
                    <label for="numero_telefone" class="form-label">{{ __('Número do Telefone') }} *</label>
                    <input type="text" id="numeroTelefoneInput" name="numero_telefone" class="form-control" 
                           required placeholder="(11) 99999-9999">
                    <div class="form-error" id="numero_telefone_error"></div>
                </div>
                
                <div class="mb-3">
                    <label for="tipo_telefone" class="form-label">{{ __('Tipo de Telefone') }} *</label>
                    <select id="tipoTelefoneSelect" name="tipo_telefone" class="form-control" required>
                        <option value="">{{ __('Selecione...') }}</option>
                        <option value="celular">{{ __('Celular') }}</option>
                        <option value="whatsapp">{{ __('WhatsApp') }}</option>
                        <option value="residencial">{{ __('Residencial') }}</option>
                        <option value="comercial">{{ __('Comercial') }}</option>
                    </select>
                    <div class="form-error" id="tipo_telefone_error"></div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" id="isPrincipalCheckbox" name="is_principal" class="form-check-input">
                        <label for="isPrincipalCheckbox" class="form-check-label">{{ __('Definir como telefone principal') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="modal-telefone-footer">
                <button type="button" class="btn-secondary" onclick="telefoneFecharModal()">
                    {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn-primary">
                    {{ __('Salvar') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos para a seção de telefones */
.telefones-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.telefone-item {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    background: #ffffff;
    transition: all 0.2s ease;
}

.telefone-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.telefone-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.telefone-details {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.telefone-numero {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.telefone-tipo {
    padding: 4px 8px;
    background: #f3f4f6;
    border-radius: 6px;
    font-size: 12px;
    color: #6b7280;
}

.badge-primary {
    padding: 4px 8px;
    background: #3b82f6;
    color: white;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.telefone-actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    background: none;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-action:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
}

.btn-danger {
    border-color: #fca5a5;
    color: #dc2626;
}

.btn-danger:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #b91c1c;
}

.no-telefones {
    text-align: center;
    padding: 32px;
    color: #6b7280;
    font-style: italic;
}

/* Estilos do modal de telefone */
.modal-telefone-overlay {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
}

.modal-telefone-content {
    background-color: white;
    margin: 5% auto;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: telefoneModalAppear 0.3s ease;
}

@keyframes telefoneModalAppear {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-telefone-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-telefone-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.modal-telefone-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.modal-telefone-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.modal-telefone-body {
    padding: 24px;
}

.modal-telefone-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
}

/* Estilos do formulário */
.form-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-check {
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-check-input {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 1px solid #d1d5db;
}

.form-check-label {
    font-size: 14px;
    color: #374151;
}

.form-error {
    color: #dc2626;
    font-size: 14px;
    margin-top: 4px;
}

/* Botões */
.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    background: #4b5563;
}

.flex {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mb-3 {
    margin-bottom: 16px;
}

.mb-4 {
    margin-bottom: 24px;
}
</style>

<script>
// Variável global para controle
let telefoneEditandoAtual = null;

// Funções principais do modal de telefone
function telefoneAbrirModal(telefone = null) {
    const modal = document.getElementById('modalTelefoneContainer');
    const titulo = document.getElementById('modalTelefoneTitulo');
    
    // Limpar erros
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    
    if (telefone) {
        titulo.textContent = '{{ __("Editar Telefone") }}';
        document.getElementById('telefoneId').value = telefone.id;
        document.getElementById('numeroTelefoneInput').value = telefone.numero_telefone;
        document.getElementById('tipoTelefoneSelect').value = telefone.tipo_telefone;
        document.getElementById('isPrincipalCheckbox').checked = telefone.is_principal;
        telefoneEditandoAtual = telefone.id;
    } else {
        titulo.textContent = '{{ __("Adicionar Telefone") }}';
        document.getElementById('formTelefone').reset();
        document.getElementById('telefoneId').value = '';
        telefoneEditandoAtual = null;
    }
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Previne scroll
}

function telefoneFecharModal() {
    const modal = document.getElementById('modalTelefoneContainer');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Restaura scroll
    
    // Resetar formulário
    document.getElementById('formTelefone').reset();
    document.getElementById('telefoneId').value = '';
    telefoneEditandoAtual = null;
    
    // Limpar erros
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
}

async function telefoneSalvar(event) {
    event.preventDefault();
    
    // Limpar erros anteriores
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    
    const data = {
        numero_telefone: document.getElementById('numeroTelefoneInput').value,
        tipo_telefone: document.getElementById('tipoTelefoneSelect').value,
        is_principal: document.getElementById('isPrincipalCheckbox').checked
    };
    
    const url = telefoneEditandoAtual 
        ? `/telefones/${telefoneEditandoAtual}`
        : '/telefones';
    
    const method = telefoneEditandoAtual ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            telefoneFecharModal();
            telefoneMostrarNotificacao(result.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Mostrar erros de validação
            if (result.errors) {
                Object.keys(result.errors).forEach(field => {
                    const errorElement = document.getElementById(field + '_error');
                    if (errorElement) {
                        errorElement.textContent = result.errors[field][0];
                    }
                });
            } else {
                telefoneMostrarNotificacao(result.message || '{{ __("Erro ao salvar telefone.") }}', 'error');
            }
        }
    } catch (error) {
        console.error('Erro:', error);
        telefoneMostrarNotificacao('{{ __("Erro ao salvar telefone. Tente novamente.") }}', 'error');
    }
}

function telefoneEditar(id) {
    // Buscar os dados do telefone via AJAX
    fetch(`/telefones/${id}/dados`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da API');
            }
            return response.json();
        })
        .then(result => {
            if (result.success && result.telefone) {
                telefoneAbrirModal(result.telefone);
            } else {
                throw new Error(result.message || 'Dados do telefone não encontrados');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar telefone via API:', error);
            
            // Fallback: tentar pegar dados da interface
            const telefoneItem = document.querySelector(`.telefone-item[data-id="${id}"]`);
            if (telefoneItem) {
                const telefone = {
                    id: id,
                    numero_telefone: telefoneItem.querySelector('.telefone-numero').textContent,
                    tipo_telefone: telefoneItem.querySelector('.telefone-tipo').textContent.toLowerCase(),
                    is_principal: telefoneItem.querySelector('.badge-primary') !== null
                };
                telefoneAbrirModal(telefone);
            } else {
                telefoneMostrarNotificacao('Erro ao carregar dados do telefone: ' + error.message, 'error');
            }
        });
}

async function telefoneExcluir(id) {
    if (!confirm('{{ __("Tem certeza que deseja excluir este telefone?") }}')) {
        return;
    }
    
    try {
        const response = await fetch(`/telefones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            telefoneMostrarNotificacao(result.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            telefoneMostrarNotificacao(result.message || '{{ __("Erro ao excluir telefone.") }}', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        telefoneMostrarNotificacao('{{ __("Erro ao excluir telefone. Tente novamente.") }}', 'error');
    }
}

async function telefoneDefinirPrincipal(id) {
    try {
        const response = await fetch(`/telefones/${id}/principal`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            telefoneMostrarNotificacao(result.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            telefoneMostrarNotificacao(result.message || '{{ __("Erro ao definir telefone principal.") }}', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        telefoneMostrarNotificacao('{{ __("Erro ao definir telefone principal. Tente novamente.") }}', 'error');
    }
}

function telefoneMostrarNotificacao(message, type = 'info') {
    // Remover notificações existentes
    const existingNotifications = document.querySelectorAll('.telefone-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = 'telefone-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: telefoneSlideIn 0.3s ease;
        max-width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    `;
    
    if (type === 'success') {
        notification.style.background = '#059669';
    } else if (type === 'error') {
        notification.style.background = '#dc2626';
    } else {
        notification.style.background = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'telefoneSlideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Adicionar estilos de animação para as notificações
const telefoneStyle = document.createElement('style');
telefoneStyle.textContent = `
    @keyframes telefoneSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes telefoneSlideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(telefoneStyle);

// Event listeners para fechar modal
document.addEventListener('DOMContentLoaded', function() {
    // Fechar modal ao clicar fora
    const modal = document.getElementById('modalTelefoneContainer');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                telefoneFecharModal();
            }
        });
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            telefoneFecharModal();
        }
    });
});
</script>