@extends('feed.post.template.layout')

@section('styles')
    @parent
    <style>
        .moderacao-global {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .card-mod {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .card-header-mod {
            background: #1f2937;
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        
        .infracao-item {
            border-left: 4px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8fafc;
        }
        
        .infracao-item.critica {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        .infracao-item.media {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        
        .infracao-item.leve {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
    </style>
@endsection

@section('main')
<div class="moderacao-global">
    <!-- Cabeçalho -->
    <div class="card-mod">
        <div class="card-header-mod">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="material-symbols-outlined">admin_panel_settings</i>
                        Painel Global de Moderação
                    </h1>
                    <p class="mb-0 opacity-75">Gestão centralizada do sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-warning" onclick="processarBanimentosAutomaticos()">
                        <i class="material-symbols-outlined">autorenew</i>
                        Processar Auto
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-light">
                        <i class="material-symbols-outlined">dashboard</i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Estatísticas Globais -->
        <div class="card-body">
            @include('moderacao.componentes.estatisticas', [
                'estatisticas' => $estatisticas,
                'titulo' => 'Estatísticas Globais do Sistema',
                'tipo' => 'compact'
            ])
        </div>
    </div>

    <div class="row">
        <!-- Infrações Pendentes -->
        <div class="col-md-8">
            <div class="card-mod">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="material-symbols-outlined">warning</i>
                        Infrações Pendentes
                        <span class="badge bg-danger">{{ $infracoesPendentes->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($infracoesPendentes->count() > 0)
                        @foreach($infracoesPendentes as $infracao)
                            <div class="infracao-item {{ $infracao->tipo === 'discurso_odio' ? 'critica' : ($infracao->tipo === 'spam' ? 'media' : 'leve') }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="{{ $infracao->usuario->foto ?? asset('assets/images/avatar-default.png') }}" 
                                                 class="rounded-circle me-2" width="32" height="32">
                                            <strong>{{ $infracao->usuario->nome }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $infracao->tipo }}</span>
                                            <small class="text-muted ms-2">{{ $infracao->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1"><strong>Descrição:</strong> {{ $infracao->descricao }}</p>
                                        @if($infracao->conteudo_original)
                                            <p class="mb-1"><strong>Conteúdo:</strong> {{ Str::limit($infracao->conteudo_original, 150) }}</p>
                                        @endif
                                        @if($infracao->interesse)
                                            <p class="mb-1"><strong>Interesse:</strong> {{ $infracao->interesse->nome }}</p>
                                        @endif
                                        @if($infracao->reportadoPor)
                                            <p class="mb-0"><strong>Reportado por:</strong> {{ $infracao->reportadoPor->nome }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="acoes-mod mt-2">
                                    <button class="btn btn-sm btn-success" onclick="verificarInfracao({{ $infracao->id }}, 'aplicar_penalidade')">
                                        <i class="material-symbols-outlined">check</i>
                                        Aplicar Penalidade
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="verificarInfracao({{ $infracao->id }}, 'ignorar')">
                                        <i class="material-symbols-outlined">close</i>
                                        Ignorar
                                    </button>
                                    @if($infracao->postagem)
                                        <a href="{{ route('post.read', $infracao->postagem->id) }}" class="btn btn-sm btn-info">
                                            <i class="material-symbols-outlined">visibility</i>
                                            Ver Postagem
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="mt-3">
                            {{ $infracoesPendentes->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="material-symbols-outlined" style="font-size: 4rem; color: #10b981;">check_circle</i>
                            <h5 class="mt-2">Nenhuma infração pendente</h5>
                            <p class="text-muted">Todas as infrações foram verificadas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Ações Rápidas -->
            <div class="card-mod">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="material-symbols-outlined">bolt</i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarPalavraGlobal">
                            <i class="material-symbols-outlined">add</i>
                            Adicionar Palavra Global
                        </button>
                        <button class="btn btn-warning" onclick="gerarRelatorio()">
                            <i class="material-symbols-outlined">description</i>
                            Gerar Relatório
                        </button>
                        <a href="{{ route('moderacao.estatisticas.globais') }}" class="btn btn-info">
                            <i class="material-symbols-outlined">analytics</i>
                            Estatísticas Detalhadas
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Penalidades Recentes -->
            <div class="card-mod mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="material-symbols-outlined">history</i>
                        Penalidades Recentes
                    </h6>
                </div>
                <div class="card-body">
                    @if($penalidadesRecentes->count() > 0)
                        @foreach($penalidadesRecentes as $penalidade)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $penalidade->usuario->nome }}</strong>
                                    <span class="badge bg-{{ $penalidade->tipo === 'sistema' ? 'danger' : 'warning' }}">
                                        {{ $penalidade->tipo }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ Str::limit($penalidade->motivo, 50) }}</small>
                                <div class="text-end">
                                    <small>{{ $penalidade->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Nenhuma penalidade recente</p>
                    @endif
                </div>
            </div>
            
            <!-- Palavras Proibidas Globais -->
            <div class="card-mod mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="material-symbols-outlined">block</i>
                        Palavras Globais
                    </h6>
                </div>
                <div class="card-body">
                    @if($palavrasProibidasGlobais->count() > 0)
                        @foreach($palavrasProibidasGlobais->take(5) as $palavra)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    {{ $palavra->palavra }}
                                    <span class="badge bg-secondary">{{ $palavra->tipo }}</span>
                                </span>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="removerPalavraGlobal({{ $palavra->id }})">
                                    <i class="material-symbols-outlined">delete</i>
                                </button>
                            </div>
                        @endforeach
                        @if($palavrasProibidasGlobais->count() > 5)
                            <div class="text-center">
                                <small class="text-muted">+{{ $palavrasProibidasGlobais->count() - 5 }} mais</small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">Nenhuma palavra global</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Palavra Global -->
<div class="modal fade" id="modalAdicionarPalavraGlobal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Palavra Proibida Global</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAdicionarPalavraGlobal">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Palavra/Frase</label>
                        <input type="text" class="form-control" name="palavra" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="tipo" required>
                            <option value="exata">Exata</option>
                            <option value="parcial">Parcial</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea class="form-control" name="motivo" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="adicionarPalavraGlobal()">Adicionar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function verificarInfracao(infracaoId, acao) {
    if (acao === 'aplicar_penalidade') {
        const motivo = prompt('Digite o motivo da penalidade:');
        const peso = prompt('Peso da penalidade (1-3):', '1');
        const dias = prompt('Dias de expiração (enter para permanente):', '30');
        
        if (motivo && peso) {
            fetch(`/moderacao/infracoes/${infracaoId}/verificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    acao: 'aplicar_penalidade',
                    motivo_penalidade: motivo,
                    peso_penalidade: parseInt(peso),
                    dias_expiracao: dias ? parseInt(dias) : null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    location.reload();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            });
        }
    } else {
        fetch(`/moderacao/infracoes/${infracaoId}/verificar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ acao: 'ignorar' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            } else {
                alert('Erro: ' + data.mensagem);
            }
        });
    }
}

function adicionarPalavraGlobal() {
    const form = document.getElementById('formAdicionarPalavraGlobal');
    const formData = new FormData(form);
    
    fetch('/moderacao/palavras-proibidas-globais', {
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
            alert('Erro: ' + data.mensagem);
        }
    });
}

function removerPalavraGlobal(palavraId) {
    if (confirm('Remover esta palavra proibida global?')) {
        fetch(`/moderacao/palavras-proibidas-globais/${palavraId}`, {
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
                alert('Erro: ' + data.mensagem);
            }
        });
    }
}

function processarBanimentosAutomaticos() {
    if (confirm('Processar banimentos automáticos?')) {
        fetch('/moderacao/estatisticas/globais')
        .then(response => response.json())
        .then(data => {
            alert('Banimentos processados: ' + JSON.stringify(data.processamento_automatico));
        });
    }
}

function gerarRelatorio() {
    const inicio = prompt('Data início (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    const fim = prompt('Data fim (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    
    if (inicio && fim) {
        fetch('/moderacao/relatorios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                periodo_inicio: inicio,
                periodo_fim: fim
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert('Relatório gerado: ' + JSON.stringify(data.relatorio, null, 2));
            } else {
                alert('Erro ao gerar relatório');
            }
        });
    }
}

// Fechar modal ao adicionar palavra com sucesso
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAdicionarPalavraGlobal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('formAdicionarPalavraGlobal');
            if (form) {
                form.reset();
            }
        });
    }
});
</script>
@endsection