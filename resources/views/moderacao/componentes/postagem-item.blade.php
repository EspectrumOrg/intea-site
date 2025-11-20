{{-- 
    Componente: Item de Postagem para Moderação
    Uso: @include('moderacao.componentes.postagem-item', ['postagem' => $postagem])
--}}

@props(['postagem'])

<div class="postagem-item {{ $postagem->suspeita ? 'suspeita' : '' }} {{ $postagem->bloqueada_auto ? 'proibida' : '' }}">
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <!-- Cabeçalho da Postagem -->
            <div class="d-flex align-items-center mb-2">
                <img src="{{ $postagem->usuario->foto ?? asset('assets/images/avatar-default.png') }}" 
                     alt="{{ $postagem->usuario->nome }}" 
                     class="rounded-circle me-2" width="32" height="32">
                <div>
                    <strong>{{ $postagem->usuario->nome }}</strong>
                    @if($postagem->usuario->temPenalidadesAtivas())
                        <span class="badge bg-danger ms-1" title="Usuário com penalidades ativas">
                            <i class="material-symbols-outlined" style="font-size: 14px;">warning</i>
                        </span>
                    @endif
                </div>
                <small class="text-muted ms-2">{{ $postagem->created_at->diffForHumans() }}</small>
                
                <!-- Status da Postagem -->
                @if($postagem->bloqueada_auto)
                    <span class="badge bg-danger ms-2">Bloqueada Automaticamente</span>
                @elseif($postagem->removida_manual)
                    <span class="badge bg-warning ms-2">Removida Manualmente</span>
                @elseif($postagem->suspeita)
                    <span class="badge bg-warning ms-2">Suspeita</span>
                @endif
            </div>

            <!-- Conteúdo da Postagem -->
            <div class="postagem-conteudo mb-2">
                <p class="mb-2">{{ $postagem->texto_postagem }}</p>
                
                @if($postagem->imagens->count() > 0)
                    <div class="postagem-imagens mb-2">
                        <small class="text-muted">
                            <i class="material-symbols-outlined">image</i>
                            {{ $postagem->imagens->count() }} imagem(ns)
                        </small>
                        <div class="miniaturas mt-1">
                            @foreach($postagem->imagens->take(3) as $imagem)
                                <img src="{{ Storage::url($imagem->caminho_imagem) }}" 
                                     alt="Imagem da postagem" 
                                     class="rounded me-1" 
                                     width="60" 
                                     height="60"
                                     style="object-fit: cover;">
                            @endforeach
                            @if($postagem->imagens->count() > 3)
                                <span class="badge bg-secondary">+{{ $postagem->imagens->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Interesses da Postagem -->
            @if($postagem->interesses->count() > 0)
                <div class="postagem-interesses mb-2">
                    <small class="text-muted">Interesses:</small>
                    @foreach($postagem->interesses as $interesse)
                        <span class="badge bg-light text-dark me-1">
                            <i class="material-symbols-outlined" style="font-size: 14px; color: {{ $interesse->cor }};">{{ $interesse->icone }}</i>
                            {{ $interesse->nome }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Estatísticas da Postagem -->
            <div class="postagem-stats d-flex gap-3 text-muted small">
                <span>
                    <i class="material-symbols-outlined" style="font-size: 16px;">favorite</i>
                    {{ $postagem->curtidas_count ?? 0 }}
                </span>
                <span>
                    <i class="material-symbols-outlined" style="font-size: 16px;">chat</i>
                    {{ $postagem->comentarios_count ?? 0 }}
                </span>
                @if($postagem->visibilidade_interesse !== 'publico')
                    <span>
                        <i class="material-symbols-outlined" style="font-size: 16px;">visibility</i>
                        {{ $postagem->visibilidade_interesse }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Ações de Moderação -->
    <div class="acoes-mod mt-3">
        @if(!$postagem->removida_manual && !$postagem->bloqueada_auto)
            <button class="btn-mod btn-remover" 
                    onclick="removerPostagem({{ $postagem->id }})"
                    title="Remover postagem">
                <i class="material-symbols-outlined">delete</i>
                Remover
            </button>
            
            <button class="btn-mod btn-suspeitar" 
                    onclick="marcarComoSuspeita({{ $postagem->id }})"
                    title="Marcar como suspeita">
                <i class="material-symbols-outlined">flag</i>
                Suspeita
            </button>
            
            @if(isset($aplicarPenalidade) && $aplicarPenalidade)
                <button class="btn-mod btn-penalidade" 
                        onclick="aplicarPenalidadeUsuario({{ $postagem->usuario_id }}, {{ $postagem->id }})"
                        title="Aplicar penalidade ao usuário">
                    <i class="material-symbols-outlined">gavel</i>
                    Penalizar
                </button>
            @endif
        @elseif($postagem->removida_manual)
            <button class="btn-mod btn-restaurar" 
                    onclick="restaurarPostagem({{ $postagem->id }})"
                    title="Restaurar postagem">
                <i class="material-symbols-outlined">restore</i>
                Restaurar
            </button>
        @endif
        
        <a href="{{ route('post.read', $postagem->id) }}" 
           class="btn-mod btn-ver" 
           target="_blank"
           title="Ver postagem completa">
            <i class="material-symbols-outlined">visibility</i>
            Ver
        </a>
        
        <button class="btn-mod btn-detalhes" 
                onclick="mostrarDetalhesPostagem({{ $postagem->id }})"
                title="Mais detalhes">
            <i class="material-symbols-outlined">info</i>
            Detalhes
        </button>
    </div>
</div>

<style>
.postagem-item {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #e5e7eb;
    transition: all 0.3s ease;
}

.postagem-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.postagem-item.suspeita {
    border-left-color: #f59e0b;
    background: #fffbeb;
}

.postagem-item.proibida {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.postagem-conteudo {
    line-height: 1.5;
}

.miniaturas img {
    border: 1px solid #e5e7eb;
}

.acoes-mod {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-mod {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    text-decoration: none;
}

.btn-remover {
    background: #ef4444;
    color: white;
}

.btn-remover:hover {
    background: #dc2626;
}

.btn-suspeitar {
    background: #f59e0b;
    color: white;
}

.btn-suspeitar:hover {
    background: #d97706;
}

.btn-penalidade {
    background: #8b5cf6;
    color: white;
}

.btn-penalidade:hover {
    background: #7c3aed;
}

.btn-restaurar {
    background: #10b981;
    color: white;
}

.btn-restaurar:hover {
    background: #059669;
}

.btn-ver {
    background: #3b82f6;
    color: white;
}

.btn-ver:hover {
    background: #2563eb;
}

.btn-detalhes {
    background: #6b7280;
    color: white;
}

.btn-detalhes:hover {
    background: #4b5563;
}

.postagem-stats i {
    vertical-align: middle;
}
</style>

<script>
function mostrarDetalhesPostagem(postagemId) {
    // Implementar modal com detalhes da postagem
    fetch(`/api/postagens/${postagemId}/detalhes`)
        .then(response => response.json())
        .then(data => {
            // Abrir modal com os detalhes
            alert('Detalhes da postagem:\n' + JSON.stringify(data, null, 2));
        });
}

function marcarComoSuspeita(postagemId) {
    if (confirm('Marcar esta postagem como suspeita?')) {
        fetch(`/api/postagens/${postagemId}/suspeita`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function aplicarPenalidadeUsuario(usuarioId, postagemId) {
    const motivo = prompt('Motivo da penalidade:');
    const peso = prompt('Peso (1-3):', '1');
    
    if (motivo && peso) {
        fetch('/moderacao/penalidades/aplicar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario_id: usuarioId,
                postagem_id: postagemId,
                motivo: motivo,
                peso: parseInt(peso)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert('Penalidade aplicada com sucesso');
            }
        });
    }
}
</script>