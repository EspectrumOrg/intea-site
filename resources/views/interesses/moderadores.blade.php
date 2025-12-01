@extends('feed.post.template.layout')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Gerenciar Moderadores</h1>
        <p>Gerencie a equipe de moderação do interesse "{{ $interesse->nome }}"</p>
        <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn-voltar">
            <span class="material-symbols-outlined">arrow_back</span>
            Voltar para o interesse
        </a>
    </div>

    <div class="moderadores-container">
        <!-- Dono do Interesse -->
        <div class="moderador-section">
            <h2>Dono do Interesse</h2>
            @php
                $dono = $interesse->moderadores()->wherePivot('cargo', 'dono')->first();
            @endphp
            @if($dono)
            <div class="moderador-card dono-card">
                <div class="moderador-avatar">
                    @if($dono->foto && Storage::exists($dono->foto))
                        <img src="{{ asset('storage/' . $dono->foto) }}" alt="{{ $dono->apelido }}" onerror="this.style.display='none'">
                    @endif
                    <span class="material-symbols-outlined">account_circle</span>
                    <div class="moderador-badge dono-badge">
                        <span class="material-symbols-outlined">star</span>
                    </div>
                </div>
                <div class="moderador-info">
                    <h3>{{ $dono->apelido ?: $dono->user }}</h3>
                    <span class="moderador-cargo dono-cargo">Dono & Criador</span>
                    <p class="moderador-email">{{ $dono->email }}</p>
                </div>
                <div class="moderador-actions">
                    <span class="moderador-status">Proprietário</span>
                </div>
            </div>
            @endif
        </div>

                    <!-- Seção de Transferência de Propriedade -->
            @if($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))
            <div class="danger-zone" style="margin-top: 3rem;">
                <h3 style="color: #c53030;">
                    <span class="material-symbols-outlined">warning</span>
                    Transferir Propriedade
                </h3>
                <p style="color: #718096; margin-bottom: 1.5rem;">
                    Transfira a propriedade deste interesse para outro usuário. Esta ação é permanente e você perderá o controle total.
                </p>
                
                <div class="transferir-container">
                    <button type="button" class="btn-transferir" onclick="abrirModalTransferir('{{ $interesse->slug }}')">
                        <span class="material-symbols-outlined">swap_horiz</span>
                        Transferir Propriedade do Interesse
                    </button>
                    <small style="display: block; margin-top: 0.5rem; color: #a0aec0;">
                        Apenas o dono atual pode realizar esta ação.
                    </small>
                </div>
            </div>
            @endif

        <!-- Lista de Moderadores -->
        <div class="moderador-section">
            <div class="section-header">
                <h2>Moderadores</h2>
                <button type="button" class="btn-adicionar-moderador" onclick="abrirModalAdicionar()">
                    <span class="material-symbols-outlined">person_add</span>
                    Adicionar Moderador
                </button>
            </div>

            @if($interesse->moderadores()->wherePivot('cargo', 'moderador')->count() > 0)
                <div class="moderadores-list">
                    @foreach($interesse->moderadores()->wherePivot('cargo', 'moderador')->get() as $moderador)
                    <div class="moderador-card">
                        <div class="moderador-avatar">
                            @if($moderador->foto && Storage::exists($moderador->foto))
                                <img src="{{ asset('storage/' . $moderador->foto) }}" alt="{{ $moderador->apelido }}" onerror="this.style.display='none'">
                            @endif
                            <span class="material-symbols-outlined">account_circle</span>
                            <div class="moderador-badge moderador-badge">
                                <span class="material-symbols-outlined">shield</span>
                            </div>
                        </div>
                        <div class="moderador-info">
                            <h3>{{ $moderador->apelido ?: $moderador->user }}</h3>
                            <span class="moderador-cargo">Moderador</span>
                            <p class="moderador-email">{{ $moderador->email }}</p>
                            <span class="moderador-adicionado">Adicionado em {{ $moderador->pivot->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="moderador-actions">
                            <button type="button" class="btn-remover-moderador" onclick="confirmarRemocaoModerador({{ $moderador->id }}, '{{ $moderador->apelido ?: $moderador->user }}')">
                                <span class="material-symbols-outlined">person_remove</span>
                                Remover
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <span class="material-symbols-outlined">group</span>
                    <h3>Nenhum moderador adicionado</h3>
                    <p>Adicione moderadores para ajudar a gerenciar este interesse.</p>
                </div>
            @endif
        </div>

        <!-- Estatísticas de Moderação -->
        <div class="estatisticas-section">
            <h2>Estatísticas de Moderação</h2>
            <div class="estatisticas-grid">
                <div class="estatistica-card">
                    <span class="material-symbols-outlined">group</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->moderadores()->count() }}</strong>
                        <span>Total de Moderadores</span>
                    </div>
                </div>
                <div class="estatistica-card">
                    <span class="material-symbols-outlined">shield</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->contador_membros }}</strong>
                        <span>Membros Totais</span>
                    </div>
                </div>
                <div class="estatistica-card">
                    <span class="material-symbols-outlined">chat</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->contador_postagens }}</strong>
                        <span>Postagens Totais</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Moderador -->
<div id="modalAdicionar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adicionar Novo Moderador</h3>
            <button type="button" class="modal-close" onclick="fecharModalAdicionar()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="buscarUsuario">Buscar Usuário</label>
                <input type="text" id="buscarUsuario" placeholder="Digite o nome, apelido ou usuário..." class="form-input">
                <div id="resultadosBusca" class="resultados-busca"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancelar" onclick="fecharModalAdicionar()">Cancelar</button>
        </div>
    </div>
</div>

<script>
let timeoutBusca;

function abrirModalAdicionar() {
    document.getElementById('modalAdicionar').style.display = 'flex';
}

function fecharModalAdicionar() {
    document.getElementById('modalAdicionar').style.display = 'none';
    document.getElementById('resultadosBusca').innerHTML = '';
    document.getElementById('buscarUsuario').value = '';
}

// Buscar usuários em tempo real
document.getElementById('buscarUsuario').addEventListener('input', function(e) {
    clearTimeout(timeoutBusca);
    const query = e.target.value.trim();
    
    if (query.length < 3) {
        document.getElementById('resultadosBusca').innerHTML = '';
        return;
    }
    
    timeoutBusca = setTimeout(() => {
        fetch(`/api/usuarios/buscar?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(usuarios => {
                const resultados = document.getElementById('resultadosBusca');
                resultados.innerHTML = '';
                
                if (usuarios.length === 0) {
                    resultados.innerHTML = '<div class="nenhum-resultado">Nenhum usuário encontrado</div>';
                    return;
                }
                
                usuarios.forEach(usuario => {
                    const div = document.createElement('div');
                    div.className = 'resultado-usuario';
                    div.innerHTML = `
                        <div class="usuario-info">
                            <div class="usuario-avatar">
                                ${usuario.foto ? `<img src="/storage/${usuario.foto}" alt="${usuario.apelido}">` : '<span class="material-symbols-outlined">account_circle</span>'}
                            </div>
                            <div class="usuario-detalhes">
                                <strong>${usuario.apelido || usuario.user}</strong>
                                <span>@${usuario.user}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-adicionar" onclick="adicionarModerador(${usuario.id})">
                            <span class="material-symbols-outlined">add</span>
                            Adicionar
                        </button>
                    `;
                    resultados.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Erro na busca:', error);
            });
    }, 500);
});

function adicionarModerador(usuarioId) {
    fetch(`/interesses/{{ $interesse->slug }}/adicionar-moderador`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ usuario_id: usuarioId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            mostrarToast('Moderador adicionado com sucesso!', 'success');
            fecharModalAdicionar();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarToast(data.mensagem || 'Erro ao adicionar moderador', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast('Erro ao adicionar moderador', 'error');
    });
}

function confirmarRemocaoModerador(usuarioId, nomeUsuario) {
    if (confirm(`Tem certeza que deseja remover "${nomeUsuario}" como moderador?`)) {
        fetch(`/interesses/{{ $interesse->slug }}/remover-moderador`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ usuario_id: usuarioId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarToast('Moderador removido com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarToast(data.mensagem || 'Erro ao remover moderador', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarToast('Erro ao remover moderador', 'error');
        });
    }
}

function mostrarToast(mensagem, tipo = 'info') {
    // Implementação do toast (mesma do show.blade.php)
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'error' ? '#EF4444' : tipo === 'success' ? '#10B981' : '#3B82F6'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-size: 14px;
        font-weight: 500;
    `;
    toast.textContent = mensagem;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('modalAdicionar');
    if (event.target === modal) {
        fecharModalAdicionar();
    }
}
</script>

<style>
.moderadores-container {
    max-width: 800px;
    margin: 0 auto;
}

.moderador-section {
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.btn-adicionar-moderador {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #10B981;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-adicionar-moderador:hover {
    background: #059669;
}

.moderador-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.dono-card {
    border-left: 4px solid #F59E0B;
    background: #FFFBEB;
}

.moderador-avatar {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    flex-shrink: 0;
}

.moderador-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.moderador-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 2px solid white;
}

.dono-badge {
    background: #F59E0B;
}

.moderador-badge {
    background: #8B5CF6;
}

.moderador-badge .material-symbols-outlined {
    font-size: 12px;
}

.moderador-info {
    flex: 1;
}

.moderador-info h3 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.moderador-cargo {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f1f5f9;
    color: #6b7280;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.dono-cargo {
    background: #FEF3C7;
    color: #92400E;
}

.moderador-email {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0.25rem 0;
}

.moderador-adicionado {
    color: #9ca3af;
    font-size: 0.8rem;
}

.moderador-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-remover-moderador {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-remover-moderador:hover {
    background: #fecaca;
}

.moderador-status {
    color: #6b7280;
    font-size: 0.9rem;
    font-style: italic;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow: hidden;
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
}

.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
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
.resultados-busca {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 1rem;
}

.resultado-usuario {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.resultado-usuario:last-child {
    border-bottom: none;
}

.usuario-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.usuario-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
}

.usuario-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-detalhes {
    display: flex;
    flex-direction: column;
}

.usuario-detalhes strong {
    color: #1f2937;
}

.usuario-detalhes span {
    color: #6b7280;
    font-size: 0.8rem;
}

.btn-adicionar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #10B981;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
}

.nenhum-resultado {
    padding: 2rem;
    text-align: center;
    color: #6b7280;
}

/* Estatísticas */
.estatisticas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.estatistica-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.estatistica-card .material-symbols-outlined {
    font-size: 2rem;
    color: #3B82F6;
}

.estatistica-info {
    display: flex;
    flex-direction: column;
}

.estatistica-info strong {
    font-size: 1.5rem;
    color: #1f2937;
}

.estatistica-info span {
    color: #6b7280;
    font-size: 0.9rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.empty-state .material-symbols-outlined {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}
</style>
@endsection