@extends('feed.post.template.layout')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
@endsection

@section('main')
<div class="container-post">
    <div class="interesses-page-header">
        <h1>Gerenciar Moderadores</h1>
        <p>Gerencie a equipe de moderação do interesse "<strong style="color: {{ $interesse->cor }};">{{ $interesse->nome }}</strong>"</p>
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
                    @if($dono->foto)
                        <img src="{{ asset('storage/' . $dono->foto) }}" 
                             alt="{{ $dono->apelido ?: $dono->user }}"
                             onerror="this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';">
                        <div class="avatar-fallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f3f4f6; border-radius: 50%; align-items: center; justify-content: center; color: #6b7280;">
                            <span class="material-symbols-outlined">account_circle</span>
                        </div>
                    @else
                        <span class="material-symbols-outlined" style="font-size: 2.5rem; color: #6b7280;">account_circle</span>
                    @endif
                    <div class="moderador-badge dono-badge" style="background: {{ $interesse->cor }};">
                        <span class="material-symbols-outlined" style="font-size: 12px;">star</span>
                    </div>
                </div>
                <div class="moderador-info">
                    <h3>{{ $dono->apelido ?: $dono->user }}</h3>
                    <span class="moderador-cargo dono-cargo" style="background: {{ $interesse->cor }}20; color: {{ $interesse->cor }};">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">verified</span>
                        Dono & Criador
                    </span>
                    <p class="moderador-email">{{ $dono->email }}</p>
                    <span class="moderador-adicionado">Desde {{ $dono->pivot->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="moderador-actions">
                    <span class="moderador-status" style="color: {{ $interesse->cor }};">Proprietário</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Seção de Transferência de Propriedade - APENAS PARA DONO -->
        @if($usuarioEhDono)
        <div class="danger-zone" style="margin-top: 3rem; padding: 1.5rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px;">
            <h3 style="color: #dc2626; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-symbols-outlined" style="color: #dc2626;">warning</span>
                Transferir Propriedade
            </h3>
            <p style="color: #7f1d1d; margin-bottom: 1.5rem;">
                ⚠️ <strong>Atenção:</strong> Transfira a propriedade deste interesse para outro usuário. 
                Esta ação é <strong>permanente e irreversível</strong>. Você perderá todos os privilégios de dono.
            </p>
            
            <div class="transferir-container">
                <button type="button" class="btn-transferir" onclick="abrirModalTransferir()" 
                        style="background: linear-gradient(135deg, {{ $interesse->cor }} 0%, {{ $interesse->cor }}cc 100%); border: none; color: white;">
                    <span class="material-symbols-outlined">swap_horiz</span>
                    Transferir Propriedade
                </button>
                <small style="display: block; margin-top: 0.5rem; color: #dc2626;">
                    <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">info</span>
                    Apenas o dono atual pode realizar esta ação
                </small>
            </div>
        </div>
        @endif

        <!-- Lista de Moderadores -->
        <div class="moderador-section" style="margin-top: 3rem;">
            <div class="section-header">
                <h2>Moderadores</h2>
                @if($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))
                <button type="button" class="btn-adicionar-moderador" onclick="abrirModalAdicionar()">
                    <span class="material-symbols-outlined">person_add</span>
                    Adicionar Moderador
                </button>
                @endif
            </div>

            @php
                $moderadores = $interesse->moderadores()->wherePivot('cargo', 'moderador')->get();
            @endphp
            
            @if($moderadores->count() > 0)
                <div class="moderadores-list">
                    @foreach($moderadores as $moderador)
                    <div class="moderador-card">
                        <div class="moderador-avatar">
                            @if($moderador->foto)
                                <img src="{{ asset('storage/' . $moderador->foto) }}" 
                                     alt="{{ $moderador->apelido ?: $moderador->user }}"
                                     onerror="this.style.display='none'; this.parentElement.querySelector('.avatar-fallback').style.display='flex';">
                                <div class="avatar-fallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f3f4f6; border-radius: 50%; align-items: center; justify-content: center; color: #6b7280;">
                                    <span class="material-symbols-outlined">account_circle</span>
                                </div>
                            @else
                                <span class="material-symbols-outlined" style="font-size: 2.5rem; color: #6b7280;">account_circle</span>
                            @endif
                            <div class="moderador-badge moderador-badge" style="background: #8B5CF6;">
                                <span class="material-symbols-outlined" style="font-size: 12px;">shield</span>
                            </div>
                        </div>
                        <div class="moderador-info">
                            <h3>{{ $moderador->apelido ?: $moderador->user }}</h3>
                            <span class="moderador-cargo" style="background: #8B5CF620; color: #8B5CF6;">
                                <span class="material-symbols-outlined" style="font-size: 1rem;">shield</span>
                                Moderador
                            </span>
                            <p class="moderador-email">{{ $moderador->email }}</p>
                            <span class="moderador-adicionado">Adicionado em {{ $moderador->pivot->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="moderador-actions">
                            @if($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))
                            <button type="button" class="btn-remover-moderador" 
                                    onclick="confirmarRemocaoModerador({{ $moderador->id }}, '{{ ($moderador->apelido ?: $moderador->user) }}')">
                                <span class="material-symbols-outlined">person_remove</span>
                                Remover
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <span class="material-symbols-outlined" style="font-size: 4rem; color: #d1d5db;">group</span>
                    <h3>Nenhum moderador adicionado</h3>
                    <p>Adicione moderadores para ajudar a gerenciar este interesse.</p>
                </div>
            @endif
        </div>

        <!-- Estatísticas de Moderação -->
        <div class="estatisticas-section" style="margin-top: 3rem;">
            <h2>Estatísticas de Moderação</h2>
            <div class="estatisticas-grid">
                <div class="estatistica-card">
                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">group</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->moderadores()->count() }}</strong>
                        <span>Total de Moderadores</span>
                    </div>
                </div>
                <div class="estatistica-card">
                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">people</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->contador_membros }}</strong>
                        <span>Membros Totais</span>
                    </div>
                </div>
                <div class="estatistica-card">
                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">chat</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->contador_postagens }}</strong>
                        <span>Postagens Totais</span>
                    </div>
                </div>
            <!--
                <div class="estatistica-card">
                    <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">shield</span>
                    <div class="estatistica-info">
                        <strong>{{ $interesse->palavrasProibidas()->count() }}</strong>
                        <span>Palavras Proibidas</span>
                    </div>
                </div>
-->
                <br> <br>  <br>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Transferência de Propriedade (APENAS DONO) -->
@if($usuarioEhDono)
<div id="modalTransferirContainer"></div>
@endif

<!-- Modal para Adicionar Moderador -->
@if($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))
<div id="modalAdicionarContainer"></div>
@endif

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
    background: {{ $interesse->cor }};
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-adicionar-moderador:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn-transferir {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-transferir:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.moderador-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
    transition: transform 0.2s ease;
}

.moderador-card:hover {
    transform: translateY(-2px);
}

.dono-card {
    border-left: 4px solid {{ $interesse->cor }};
    background: {{ $interesse->cor }}08;
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

.moderador-info {
    flex: 1;
}

.moderador-info h3 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.moderador-cargo {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
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

/* Estilos para modais */
.modal-fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.modal-content-fixed {
    background: white;
    border-radius: 12px;
    width: 95%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

/* Resultados da busca */
.resultados-busca {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.resultado-usuario {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.resultado-usuario:hover {
    background: #f9fafb;
}

.resultado-usuario:last-child {
    border-bottom: none;
}

.usuario-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
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
    flex-shrink: 0;
}

.usuario-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-detalhes {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.usuario-detalhes strong {
    color: #1f2937;
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
    transition: background 0.2s;
    flex-shrink: 0;
}

.btn-adicionar:hover {
    background: #059669;
}

.nenhum-resultado {
    padding: 2rem;
    text-align: center;
    color: #6b7280;
    font-style: italic;
}

/* Estatísticas */
.estatisticas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.empty-state .material-symbols-outlined {
    margin-bottom: 1rem;
}

/* Estilos para avatar fallback */
.avatar-fallback {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

/* Responsividade */
@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .moderador-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .moderador-info {
        text-align: center;
    }
    
    .moderador-actions {
        justify-content: center;
        width: 100%;
    }
    
    .estatisticas-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content-fixed {
        width: 98%;
        margin: 0 1%;
        border-radius: 8px;
    }
}

@media (max-width: 480px) {
    .btn-adicionar-moderador,
    .btn-transferir {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .moderador-card {
        padding: 1rem;
    }
}
</style>

<script>
// ============================================
// GERENCIAMENTO DE MODERADORES
// ============================================

let timeoutBusca;
let usuarioSelecionadoAdicionar = null;
let cacheBusca = {};
let ultimaQuery = '';

// Cache para resultados de busca
const searchCache = {
    get: (query) => cacheBusca[query],
    set: (query, results) => {
        cacheBusca[query] = results;
        // Limitar cache a 50 entradas
        const keys = Object.keys(cacheBusca);
        if (keys.length > 50) {
            delete cacheBusca[keys[0]];
        }
    },
    clear: () => { cacheBusca = {}; }
};

// ============================================
// MODAL ADICIONAR MODERADOR
// ============================================

function abrirModalAdicionar() {
    if (!@json($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))) {
        mostrarToast('Apenas o dono pode adicionar moderadores', 'error');
        return;
    }
    
    criarModalAdicionar();
    usuarioSelecionadoAdicionar = null;
}

function fecharModalAdicionar() {
    const container = document.getElementById('modalAdicionarContainer');
    if (container) {
        container.innerHTML = '';
    }
    usuarioSelecionadoAdicionar = null;
    document.removeEventListener('keydown', fecharModalAdicionarComEsc);
}

function fecharModalAdicionarComEsc(e) {
    if (e.key === 'Escape') {
        fecharModalAdicionar();
    }
}

function criarModalAdicionar() {
    const modalHTML = `
        <div id="modalAdicionar" class="modal-fixed">
            <div class="modal-content-fixed">
                <!-- Cabeçalho -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div>
                        <h3 style="margin: 0; color: {{ $interesse->cor }}; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="font-size: 1.4rem;">person_add</span>
                            Adicionar Moderador
                        </h3>
                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 0.9rem;">
                            Interesse: <strong>{{ $interesse->nome }}</strong>
                        </p>
                    </div>
                    <button type="button" onclick="fecharModalAdicionar()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 1.5rem; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;">
                        ×
                    </button>
                </div>
                
                <!-- Corpo do modal -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <!-- Instruções -->
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span class="material-symbols-outlined" style="color: #0284c7; font-size: 1.2rem; flex-shrink: 0;">info</span>
                            <div style="font-size: 0.9rem;">
                                <strong style="color: #0369a1;">Adicionar novo moderador</strong>
                                <p style="color: #0c4a6e; margin: 0.25rem 0 0 0;">
                                    O usuário selecionado terá privilégios para moderar este interesse.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Busca de usuários -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px; font-size: 1.1rem;">search</span>
                            Buscar Usuário
                        </label>
                        <div style="position: relative;">
                            <input type="text" id="buscarUsuarioModal" placeholder="Digite nome, apelido ou usuário..." 
                                   style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; box-sizing: border-box; transition: border-color 0.2s;">
                            <span class="material-symbols-outlined" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none;">
                                search
                            </span>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.85rem;">
                            Busque por membros do interesse para adicionar como moderador
                        </small>
                    </div>
                    
                    <!-- Resultados da busca -->
                    <div id="resultadosBuscaModal" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 1rem; display: none; max-height: 300px; overflow-y: auto;"></div>
                    
                    <!-- Usuário selecionado -->
                    <div id="usuarioSelecionadoInfoModal" style="display: none; padding: 1.25rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin-top: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="material-symbols-outlined" style="color: #0284c7; font-size: 1.5rem;">check_circle</span>
                            <div>
                                <p style="margin: 0 0 0.25rem 0; font-weight: 600; color: #0369a1;">Moderador selecionado</p>
                                <p style="margin: 0; color: #0c4a6e; font-size: 1.1rem;">
                                    <strong id="nomeUsuarioSelecionadoModal"></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rodapé do modal -->
                <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: #6b7280; display: flex; align-items: center; gap: 4px;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">shield</span>
                            <span>Privilégios de moderação</span>
                        </div>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" onclick="fecharModalAdicionar()" 
                                    style="padding: 0.75rem 1.5rem; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.2s;">
                                Cancelar
                            </button>
                            <button type="button" id="btnConfirmarAdicionar" onclick="confirmarAdicionarModerador()" 
                                    style="padding: 0.75rem 1.5rem; background: {{ $interesse->cor }}; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: none; transition: all 0.2s;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px;">person_add</span>
                                Adicionar Moderador
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Adicionar ao container
    const container = document.getElementById('modalAdicionarContainer');
    if (!container) {
        console.error('Container modalAdicionarContainer não encontrado');
        return;
    }
    
    container.innerHTML = modalHTML;
    
    // Configurar evento de busca
    const buscarInput = document.getElementById('buscarUsuarioModal');
    
    if (buscarInput) {
        buscarInput.addEventListener('input', function(e) {
            clearTimeout(timeoutBusca);
            const query = e.target.value.trim();
            ultimaQuery = query;
            
            if (query.length < 2) {
                const resultados = document.getElementById('resultadosBuscaModal');
                if (resultados) {
                    resultados.innerHTML = '';
                    resultados.style.display = 'none';
                }
                return;
            }
            
            timeoutBusca = setTimeout(() => {
                buscarUsuariosParaAdicionar(query);
            }, 500);
        });
        
        // Focar no campo de busca
        setTimeout(() => buscarInput.focus(), 100);
        
        // Adicionar listener para ESC
        document.addEventListener('keydown', fecharModalAdicionarComEsc);
    }
    
    // Adicionar estilos de hover
    const style = document.createElement('style');
    style.textContent = `
        #buscarUsuarioModal:focus {
            outline: none;
            border-color: {{ $interesse->cor }};
            box-shadow: 0 0 0 3px {{ $interesse->cor }}20;
        }
        .btn-selecionar-adicionar:hover {
            background: #059669 !important;
            transform: translateY(-1px);
        }
        .resultado-usuario-adicionar:hover {
            background-color: #f9fafb !important;
        }
        #btnConfirmarAdicionar:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px {{ $interesse->cor }}20;
        }
        button[onclick="fecharModalAdicionar()"]:hover {
            background: #f3f4f6 !important;
        }
    `;
    document.head.appendChild(style);
}

function buscarUsuariosParaAdicionar(query) {
    // Verificar cache
    const cached = searchCache.get(query);
    if (cached) {
        exibirResultadosBusca(cached, query);
        return;
    }
    
    const resultados = document.getElementById('resultadosBuscaModal');
    const buscarInput = document.getElementById('buscarUsuarioModal');
    
    if (!resultados || !buscarInput) {
        console.error('Elementos necessários não encontrados');
        return;
    }
    
    // Mostrar loading
    resultados.innerHTML = `
        <div style="padding: 2rem; text-align: center; color: #6b7280;">
            <div style="display: inline-block; width: 24px; height: 24px; border: 2px solid #f3f4f6; border-top-color: {{ $interesse->cor }}; border-radius: 50%; animation: spin 0.6s linear infinite; margin-bottom: 0.5rem;"></div>
            <div>Buscando usuários...</div>
        </div>
    `;
    resultados.style.display = 'block';
    
    // Adicionar feedback no input
    buscarInput.style.borderColor = '{{ $interesse->cor }}';
    
    fetch(`/buscar?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) throw new Error('Erro na busca');
            return response.json();
        })
        .then(usuarios => {
            // Salvar no cache
            searchCache.set(query, usuarios);
            
            // Verificar se a query ainda é a mesma
            if (query === ultimaQuery) {
                exibirResultadosBusca(usuarios, query);
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            if (query === ultimaQuery) {
                resultados.innerHTML = `
                    <div style="padding: 2rem; text-align: center; color: #ef4444;">
                        <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; color: #ef4444;">error</span>
                        <div>Erro ao buscar usuários</div>
                        <small style="font-size: 0.85rem; margin-top: 0.5rem; display: block; color: #9ca3af;">
                            ${error.message}
                        </small>
                    </div>
                `;
            }
        })
        .finally(() => {
            buscarInput.style.borderColor = '#d1d5db';
        });
}

function exibirResultadosBusca(usuarios, query) {
    const resultados = document.getElementById('resultadosBuscaModal');
    if (!resultados) return;
    
    resultados.innerHTML = '';
    
    if (!usuarios || usuarios.length === 0) {
        resultados.innerHTML = `
            <div style="padding: 2rem; text-align: center; color: #6b7280;">
                <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;">person_off</span>
                <div>Nenhum usuário encontrado</div>
                <small style="font-size: 0.85rem; margin-top: 0.5rem; display: block;">Tente outro termo de busca</small>
            </div>
        `;
        return;
    }
    
    // Filtrar apenas usuários (não tendências)
    const usuariosFiltrados = usuarios.filter(u => u.tipo !== 'tendencia');
    
    if (usuariosFiltrados.length === 0) {
        resultados.innerHTML = `
            <div style="padding: 2rem; text-align: center; color: #6b7280;">
                <span class="material-symbols-outlined" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;">group_off</span>
                <div>Nenhum usuário encontrado</div>
            </div>
        `;
        return;
    }
    
    // Carregar IDs dos moderadores atuais via PHP
    const moderadoresIds = @json($interesse->moderadores()->pluck('tb_usuario.id')->toArray());
    
    // Exibir resultados
    usuariosFiltrados.forEach(usuario => {
        // Verificar se já é moderador
        const jaEhModerador = moderadoresIds.includes(usuario.id);
        
        const resultadoItem = document.createElement('div');
        resultadoItem.className = 'resultado-usuario-adicionar';
        resultadoItem.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            cursor: ${jaEhModerador ? 'not-allowed' : 'pointer'};
            transition: background-color 0.2s;
            gap: 0.75rem;
            opacity: ${jaEhModerador ? '0.7' : '1'};
        `;
        
        resultadoItem.onmouseover = () => {
            if (!jaEhModerador) {
                resultadoItem.style.backgroundColor = '#f9fafb';
            }
        };
        resultadoItem.onmouseout = () => {
            resultadoItem.style.backgroundColor = 'white';
        };
        
        // Avatar
        let avatarHTML = '';
        if (usuario.foto) {
            const fotoUrl = `/storage/${usuario.foto}`;
            avatarHTML = `<div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #f3f4f6; position: relative;">
                <img src="${fotoUrl}" 
                     alt="${usuario.apelido || usuario.user}" 
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\"material-symbols-outlined\" style=\"position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #6b7280; font-size: 1.5rem;\">account_circle</span>';">
            </div>`;
        } else {
            avatarHTML = `<div style="width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                <span class="material-symbols-outlined" style="font-size: 1.5rem;">account_circle</span>
            </div>`;
        }
        
        // Informações do usuário
        resultadoItem.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                ${avatarHTML}
                <div style="min-width: 0;">
                    <div style="font-weight: 600; color: ${jaEhModerador ? '#9ca3af' : '#1f2937'}; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${usuario.apelido || usuario.user}
                        ${jaEhModerador ? ' <span style="color: #8B5CF6; font-size: 0.8rem;">(Já é moderador)</span>' : ''}
                    </div>
                    <div style="font-size: 0.85rem; color: ${jaEhModerador ? '#9ca3af' : '#6b7280'}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        @${usuario.user}
                    </div>
                    ${usuario.descricao ? 
                        `<div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${usuario.descricao}
                        </div>` : ''
                    }
                </div>
            </div>
            ${jaEhModerador ? 
                '<span style="color: #8B5CF6; font-size: 0.85rem; font-weight: 500; padding: 0.25rem 0.75rem; background: #8B5CF620; border-radius: 6px;">Já moderador</span>' : 
                `<button type="button" 
                        class="btn-selecionar-adicionar"
                        onclick="selecionarUsuarioParaAdicionar(${usuario.id}, '${(usuario.apelido || usuario.user).replace(/'/g, "\\'")}', '${usuario.user.replace(/'/g, "\\'")}')"
                        style="padding: 0.5rem 1rem; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; flex-shrink: 0;">
                    Selecionar
                </button>`
            }
        `;
        
        resultados.appendChild(resultadoItem);
    });
    
    // Adicionar contador de resultados
    const contador = document.createElement('div');
    contador.style.cssText = 'padding: 0.5rem 1rem; font-size: 0.85rem; color: #6b7280; border-top: 1px solid #f3f4f6; background: #f9fafb;';
    contador.textContent = `${usuariosFiltrados.length} usuário${usuariosFiltrados.length !== 1 ? 's' : ''} encontrado${usuariosFiltrados.length !== 1 ? 's' : ''}`;
    resultados.appendChild(contador);
}

function selecionarUsuarioParaAdicionar(usuarioId, nome, usuario) {
    usuarioSelecionadoAdicionar = { 
        id: usuarioId, 
        nome: nome, 
        usuario: usuario 
    };
    
    // Atualizar exibição
    const nomeElement = document.getElementById('nomeUsuarioSelecionadoModal');
    const infoSection = document.getElementById('usuarioSelecionadoInfoModal');
    const confirmButton = document.getElementById('btnConfirmarAdicionar');
    
    if (nomeElement) nomeElement.textContent = `${nome} (@${usuario})`;
    if (infoSection) infoSection.style.display = 'block';
    if (confirmButton) confirmButton.style.display = 'block';
    
    // Esconder resultados e limpar busca
    const resultados = document.getElementById('resultadosBuscaModal');
    const buscarInput = document.getElementById('buscarUsuarioModal');
    
    if (resultados) resultados.style.display = 'none';
    if (buscarInput) buscarInput.value = '';
    
    // Rolar para a seção de confirmação
    setTimeout(() => {
        if (infoSection) {
            infoSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 100);
}

function confirmarAdicionarModerador() {
    if (!usuarioSelecionadoAdicionar) {
        mostrarToast('Selecione um usuário primeiro', 'error');
        return;
    }
    
    if (!@json($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))) {
        mostrarToast('Apenas o dono pode adicionar moderadores', 'error');
        return;
    }
    
    const interesseNome = '{{ $interesse->nome }}';
    const mensagem = `Adicionar "${usuarioSelecionadoAdicionar.nome}" como moderador do interesse "${interesseNome}"?\n\n`
                    + `✅ ${usuarioSelecionadoAdicionar.nome} terá privilégios para:\n`
                    + `   • Moderar postagens\n`
                    + `   • Gerenciar conteúdo\n`
                    + `   • Aplicar regras\n\n`
                    + `Tem certeza que deseja continuar?`;
    
    if (!confirm(mensagem)) {
        return;
    }
    
    // Buscar token CSRF
    const token = getCSRFToken();
    if (!token) {
        mostrarToast('Erro de segurança. Recarregue a página e tente novamente.', 'error');
        return;
    }
    
    // Mostrar loading
    const btn = document.getElementById('btnConfirmarAdicionar');
    if (!btn) return;
    
    const originalText = btn.innerHTML;
    btn.innerHTML = `
        <span style="display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite; margin-right: 8px;"></span>
        Adicionando...
    `;
    btn.disabled = true;
    
    // Timeout para evitar loading infinito
    const timeoutId = setTimeout(() => {
        if (btn.disabled) {
            mostrarToast('Tempo esgotado. Tente novamente.', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }, 10000);
    
    // Enviar requisição - USANDO A ROTA CORRETA
    fetch(`/interesses/{{ $interesse->slug }}/adicionar-moderador`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            usuario_id: usuarioSelecionadoAdicionar.id
        })
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        // Tentar parsear o JSON mesmo se houver erro
        return response.json().then(data => {
            return {
                data: data,
                ok: response.ok,
                status: response.status
            };
        }).catch(() => {
            // Se não conseguir parsear JSON
            return {
                data: { mensagem: 'Erro inesperado no servidor' },
                ok: false,
                status: response.status
            };
        });
    })
    .then(({ data, ok, status }) => {
        if (ok) {
            if (data.sucesso) {
                mostrarToast(`✅ ${data.mensagem}`, 'success');
                fecharModalAdicionar();
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.mensagem || 'Erro ao adicionar moderador');
            }
        } else {
            throw new Error(data.mensagem || `Erro ${status}: ${data.mensagem || 'Erro no servidor'}`);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast(`❌ ${error.message}`, 'error');
        
        // Restaurar botão
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// ============================================
// FUNÇÃO DE REMOVER MODERADOR
// ============================================

function confirmarRemocaoModerador(usuarioId, nomeUsuario) {
    if (!@json($usuarioEhDono || (auth()->user() && auth()->user()->tipo_usuario == 1))) {
        mostrarToast('Apenas o dono pode remover moderadores', 'error');
        return;
    }
    
    if (!confirm(`⚠️ Tem certeza que deseja remover "${nomeUsuario}" como moderador?\n\nEsta ação removerá todos os privilégios de moderação deste usuário.`)) {
        return;
    }
    
    // Mostrar loading
    const buttons = document.querySelectorAll(`.btn-remover-moderador`);
    buttons.forEach(btn => btn.disabled = true);
    
    fetch(`/interesses/{{ $interesse->slug }}/remover-moderador`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            usuario_id: usuarioId 
        })
    })
    .then(response => {
        // Tentar parsear o JSON mesmo se houver erro
        return response.json().then(data => {
            return {
                data: data,
                ok: response.ok,
                status: response.status
            };
        }).catch(() => {
            // Se não conseguir parsear JSON
            return {
                data: { mensagem: 'Erro inesperado no servidor' },
                ok: false,
                status: response.status
            };
        });
    })
    .then(({ data, ok, status }) => {
        if (ok) {
            if (data.sucesso) {
                mostrarToast(`✅ ${data.mensagem}`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.mensagem || 'Erro ao remover moderador');
            }
        } else {
            throw new Error(data.mensagem || `Erro ${status}: ${data.mensagem || 'Erro no servidor'}`);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarToast(`❌ ${error.message}`, 'error');
        
        // Restaurar botões
        const buttons = document.querySelectorAll(`.btn-remover-moderador`);
        buttons.forEach(btn => btn.disabled = false);
    });
}
// ============================================
// MODAL TRANSFERIR PROPRIEDADE (APENAS DONO)
// ============================================

function abrirModalTransferir() {
    if (!@json($usuarioEhDono)) {
        mostrarToast('Apenas o dono pode transferir a propriedade', 'error');
        return;
    }
    
    criarModalTransferir();
}

function fecharModalTransferir() {
    const container = document.getElementById('modalTransferirContainer');
    if (container) {
        container.innerHTML = '';
    }
    document.removeEventListener('keydown', fecharModalTransferirComEsc);
}

function fecharModalTransferirComEsc(e) {
    if (e.key === 'Escape') {
        fecharModalTransferir();
    }
}

function criarModalTransferir() {
    const modalHTML = `
        <div id="modalTransferir" class="modal-fixed">
            <div class="modal-content-fixed">
                <!-- Cabeçalho -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div>
                        <h3 style="margin: 0; color: {{ $interesse->cor }}; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="font-size: 1.4rem;">swap_horiz</span>
                            Transferir Propriedade
                        </h3>
                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 0.9rem;">
                            Interesse: <strong>{{ $interesse->nome }}</strong>
                        </p>
                    </div>
                    <button type="button" onclick="fecharModalTransferir()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 1.5rem; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        ×
                    </button>
                </div>
                
                <!-- Corpo do modal -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <!-- Alerta de atenção -->
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span class="material-symbols-outlined" style="color: #f59e0b; font-size: 1.2rem; flex-shrink: 0;">warning</span>
                            <div style="font-size: 0.9rem;">
                                <strong style="color: #92400e;">Atenção! Esta ação é permanente</strong>
                                <p style="color: #78350f; margin: 0.25rem 0 0 0;">
                                    Você perderá todos os privilégios de dono sobre este interesse.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Busca de usuários -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px; font-size: 1.1rem;">search</span>
                            Buscar novo dono
                        </label>
                        <div style="position: relative;">
                            <input type="text" id="buscarUsuarioTransferir" placeholder="Digite nome, apelido ou usuário..." 
                                   style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; box-sizing: border-box;">
                            <span class="material-symbols-outlined" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none;">
                                search
                            </span>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; color: #6b7280; font-size: 0.85rem;">
                            Procure pelo nome, apelido ou @usuário da pessoa
                        </small>
                    </div>
                    
                    <!-- Resultados da busca -->
                    <div id="resultadosTransferencia" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 1rem; display: none; max-height: 300px; overflow-y: auto;"></div>
                    
                    <!-- Usuário selecionado -->
                    <div id="usuarioSelecionadoInfo" style="display: none; padding: 1.25rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin-top: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="material-symbols-outlined" style="color: #0284c7; font-size: 1.5rem;">check_circle</span>
                            <div>
                                <p style="margin: 0 0 0.25rem 0; font-weight: 600; color: #0369a1;">Novo dono selecionado</p>
                                <p style="margin: 0; color: #0c4a6e; font-size: 1.1rem;">
                                    <strong id="nomeUsuarioSelecionado"></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rodapé do modal -->
                <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; flex-shrink: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: #6b7280; display: flex; align-items: center; gap: 4px;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">info</span>
                            <span>Transferência irreversível</span>
                        </div>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" onclick="fecharModalTransferir()" 
                                    style="padding: 0.75rem 1.5rem; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.2s;">
                                Cancelar
                            </button>
                            <button type="button" id="btnConfirmarTransferencia" onclick="confirmarTransferencia()" 
                                    style="padding: 0.75rem 1.5rem; background: {{ $interesse->cor }}; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: none; transition: all 0.2s;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 4px;">swap_horiz</span>
                                Confirmar Transferência
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const container = document.getElementById('modalTransferirContainer');
    if (container) {
        container.innerHTML = modalHTML;
        document.addEventListener('keydown', fecharModalTransferirComEsc);
    }
}

// ============================================
// FUNÇÕES AUXILIARES
// ============================================

function getCSRFToken() {
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) return tokenInput.value;
    
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) return metaToken.getAttribute('content');
    
    return '';
}

function mostrarToast(mensagem, tipo = 'info') {
    // Remove toast existente
    const toastExistente = document.querySelector('.custom-toast');
    if (toastExistente) {
        toastExistente.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'error' ? '#e53e3e' : tipo === 'success' ? '#38a169' : '#3182ce'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10001;
        font-size: 14px;
        font-weight: 500;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        max-width: 90%;
    `;
    
    // Ícone baseado no tipo
    let icone = '';
    switch(tipo) {
        case 'success':
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">check_circle</span>';
            break;
        case 'error':
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">error</span>';
            break;
        default:
            icone = '<span class="material-symbols-outlined" style="font-size: 1.2rem;">info</span>';
    }
    
    toast.innerHTML = `${icone}<span>${mensagem}</span>`;
    document.body.appendChild(toast);
    
    // Auto remover após 3 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modalAdicionar = document.getElementById('modalAdicionar');
    if (event.target === modalAdicionar) {
        fecharModalAdicionar();
    }
    
    const modalTransferir = document.getElementById('modalTransferir');
    if (event.target === modalTransferir) {
        fecharModalTransferir();
    }
}

// Adicionar animações CSS
const globalStyles = document.createElement('style');
globalStyles.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(globalStyles);
</script>
@endsection