@extends('feed.post.template.layout')

@section('styles')
    @parent
    <style>
        .moderacao-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .moderacao-header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        }
        
        .palavras-proibidas-list {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .palavra-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .palavra-item:last-child {
            border-bottom: none;
        }
        
        .tipo-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .tipo-exata {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .tipo-parcial {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
@endsection

@section('main')
<div class="moderacao-container">
    <!-- Cabeçalho -->
    <div class="moderacao-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Painel de Moderação</h1>
                <p class="text-muted">Interesse: <strong>{{ $interesse->nome }}</strong></p>
                @if(auth()->user()->isAdministrador())
                    <span class="badge bg-danger">Modo Administrador</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('interesses.show', $interesse->slug) }}" class="btn btn-outline-primary">
                    <i class="material-symbols-outlined">arrow_back</i>
                    Voltar ao Interesse
                </a>
                @auth
                    @if(auth()->user()->isAdministrador())
                        <a href="{{ route('moderacao.global') }}" class="btn btn-primary">
                            <i class="material-symbols-outlined">admin_panel_settings</i>
                            Painel Global
                        </a>
                    @endif
                @endauth
            </div>
        </div>
        
        <!-- Estatísticas -->
        @include('moderacao.componentes.estatisticas', [
            'estatisticas' => $estatisticas,
            'titulo' => 'Estatísticas do Interesse'
        ])
    </div>

    <!-- Conteúdo Principal -->
    <div class="row">
        <!-- Postagens para Revisão -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="material-symbols-outlined">flag</i>
                        Postagens para Revisão
                        <span class="badge bg-warning">{{ $postagensParaRevisao->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($postagensParaRevisao->count() > 0)
                        @foreach($postagensParaRevisao as $postagem)
                            @include('moderacao.componentes.postagem-item', [
                                'postagem' => $postagem,
                                'aplicarPenalidade' => true
                            ])
                        @endforeach
                        
                        <!-- Paginação -->
                        <div class="mt-3">
                            {{ $postagensParaRevisao->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="material-symbols-outlined" style="font-size: 4rem; color: #d1d5db;">check_circle</i>
                            <h5 class="mt-2">Nenhuma postagem para revisão</h5>
                            <p class="text-muted">Todas as postagens estão em conformidade com as regras.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Palavras Proibidas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="material-symbols-outlined">block</i>
                        Palavras Proibidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="palavras-proibidas-list">
                        @if($palavrasProibidas->count() > 0)
                            @foreach($palavrasProibidas as $palavra)
                                <div class="palavra-item">
                                    <div>
                                        <strong>{{ $palavra->palavra }}</strong>
                                        <span class="tipo-badge tipo-{{ $palavra->tipo }}">
                                            {{ $palavra->tipo }}
                                        </span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="removerPalavraProibida({{ $palavra->id }})">
                                        <i class="material-symbols-outlined">delete</i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">Nenhuma palavra proibida definida</p>
                        @endif
                    </div>
                    
                    <button class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalAdicionarPalavra">
                        <i class="material-symbols-outlined">add</i>
                        Adicionar Palavra
                    </button>
                </div>
            </div>
            
            <!-- Estatísticas do Moderador -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="material-symbols-outlined">bar_chart</i>
                        Suas Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    @include('moderacao.componentes.estatisticas', [
                        'estatisticas' => $estatisticasUsuario,
                        'titulo' => null,
                        'tipo' => 'compact'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Palavra Proibida -->
<div class="modal fade" id="modalAdicionarPalavra" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Palavra Proibida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAdicionarPalavra">
                    @csrf
                    <div class="mb-3">
                        <label for="palavra" class="form-label">Palavra/Frase</label>
                        <input type="text" class="form-control" id="palavra" name="palavra" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="exata">Exata (palavra completa)</option>
                            <option value="parcial">Parcial (contém a palavra)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (opcional)</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="adicionarPalavraProibida()">Adicionar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function removerPostagem(postagemId) {
    if (confirm('Tem certeza que deseja remover esta postagem?')) {
        const motivo = prompt('Digite o motivo da remoção:');
        if (motivo) {
            fetch(`/moderacao/postagens/${postagemId}/remover`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    motivo: motivo,
                    aplicar_penalidade: confirm('Aplicar penalidade ao usuário?')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    location.reload();
                } else {
                    alert('Erro ao remover postagem: ' + data.mensagem);
                }
            });
        }
    }
}

function adicionarPalavraProibida() {
    const form = document.getElementById('formAdicionarPalavra');
    const formData = new FormData(form);
    
    fetch('/moderacao/interesses/{{ $interesse->id }}/palavras-proibidas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            location.reload();
        } else {
            alert('Erro ao adicionar palavra: ' + data.mensagem);
        }
    });
}

function removerPalavraProibida(palavraId) {
    if (confirm('Tem certeza que deseja remover esta palavra proibida?')) {
        fetch(`/moderacao/palavras-proibidas/${palavraId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            } else {
                alert('Erro ao remover palavra: ' + data.mensagem);
            }
        });
    }
}

function restaurarPostagem(postagemId) {
    if (confirm('Tem certeza que deseja restaurar esta postagem?')) {
        fetch(`/moderacao/postagens/${postagemId}/restaurar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            } else {
                alert('Erro ao restaurar postagem: ' + data.mensagem);
            }
        });
    }
}

function aplicarPenalidadeUsuario(usuarioId, postagemId) {
    const motivo = prompt('Digite o motivo da penalidade:');
    const peso = prompt('Peso da penalidade (1-3):', '1');
    
    if (motivo && peso) {
        fetch('/moderacao/usuarios/expulsar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario_id: usuarioId,
                interesse_id: {{ $interesse->id }},
                motivo: motivo,
                aplicar_penalidade: true,
                peso_penalidade: parseInt(peso)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert('Penalidade aplicada com sucesso');
                location.reload();
            } else {
                alert('Erro ao aplicar penalidade: ' + data.mensagem);
            }
        });
    }
}

function mostrarDetalhesPostagem(postagemId) {
    // Abrir modal ou página com detalhes da postagem
    window.open(`/post/read/${postagemId}`, '_blank');
}

// Fechar modal ao adicionar palavra com sucesso
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAdicionarPalavra');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('formAdicionarPalavra');
            if (form) {
                form.reset();
            }
        });
    }
});
</script>
@endsection