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

        /* Modal customizado para garantir funcionamento */
        .modal-custom {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-custom.show {
            display: block;
        }
        
        .modal-content-custom {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
                    <button class="btn btn-warning" id="btnProcessarAuto">
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
                                                 class="rounded-circle me-2" width="32" height="32" alt="Avatar de {{ $infracao->usuario->nome }}">
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
                                    <button class="btn btn-sm btn-success btn-verificar-infracao" data-infracao-id="{{ $infracao->id }}" data-acao="aplicar_penalidade">
                                        <i class="material-symbols-outlined">check</i>
                                        Aplicar Penalidade
                                    </button>
                                    <button class="btn btn-sm btn-secondary btn-verificar-infracao" data-infracao-id="{{ $infracao->id }}" data-acao="ignorar">
                                        <i class="material-symbols-outlined">close</i>
                                        Ignorar
                                    </button>
                                    @if($infracao->postagem)
                                        <a href="{{ route('post.read', $infracao->postagem->id) }}" class="btn btn-sm btn-info" target="_blank">
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
                        <button class="btn btn-primary" id="btnAbrirModal">
                            <i class="material-symbols-outlined">add</i>
                            Adicionar Palavra Global
                        </button>
                        <button class="btn btn-warning" id="btnGerarRelatorio">
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
                                <button class="btn btn-sm btn-outline-danger btn-remover-palavra" data-palavra-id="{{ $palavra->id }}">
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

<!-- Modal Customizado - Funciona 100% -->
<div class="modal-custom" id="modalPalavraGlobal">
    <div class="modal-content-custom">
        <div class="modal-header">
            <h5 class="modal-title">Adicionar Palavra Proibida Global</h5>
            <button type="button" class="btn-close" id="btnFecharModal"></button>
        </div>
        <div class="modal-body">
            <form id="formAdicionarPalavra">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Palavra/Frase</label>
                    <input type="text" class="form-control" name="palavra" required placeholder="Digite a palavra ou frase proibida">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo" required>
                        <option value="">Selecione o tipo</option>
                        <option value="exata">Exata (palavra completa)</option>
                        <option value="parcial">Parcial (contém a palavra)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Motivo</label>
                    <textarea class="form-control" name="motivo" rows="3" required placeholder="Explique o motivo para bloquear esta palavra"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnAdicionar">
                <span class="btn-text">Adicionar</span>
                <div class="loading" style="display: none;"></div>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
// SISTEMA SIMPLES E FUNCIONAL - SEM DEPENDÊNCIAS EXTERNAS
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de moderação carregado');
    
    // =============================================
    // 1. SISTEMA DE MODAL CUSTOMIZADO - GARANTIDO
    // =============================================
    
    const modal = document.getElementById('modalPalavraGlobal');
    const btnAbrir = document.getElementById('btnAbrirModal');
    const btnFechar = document.getElementById('btnFecharModal');
    const btnCancelar = document.getElementById('btnCancelar');
    const btnAdicionar = document.getElementById('btnAdicionar');
    
    // Funções para controlar o modal
    function abrirModal() {
        console.log('📝 Abrindo modal...');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function fecharModal() {
        console.log('❌ Fechando modal...');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        
        // Limpar formulário
        const form = document.getElementById('formAdicionarPalavra');
        if (form) form.reset();
    }
    
    // Event listeners do modal
    if (btnAbrir) {
        btnAbrir.addEventListener('click', abrirModal);
        console.log('✅ Botão abrir modal configurado');
    }
    
    if (btnFechar) {
        btnFechar.addEventListener('click', fecharModal);
        console.log('✅ Botão fechar modal configurado');
    }
    
    if (btnCancelar) {
        btnCancelar.addEventListener('click', fecharModal);
        console.log('✅ Botão cancelar configurado');
    }
    
    // Fechar modal clicando fora
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            fecharModal();
        }
    });
    
    // =============================================
    // 2. ADICIONAR PALAVRA PROIBIDA
    // =============================================
    
    if (btnAdicionar) {
        btnAdicionar.addEventListener('click', function() {
            console.log('🔄 Clicou em adicionar palavra');
            adicionarPalavraProibida();
        });
    }
    
    function adicionarPalavraProibida() {
        const form = document.getElementById('formAdicionarPalavra');
        const btnText = btnAdicionar.querySelector('.btn-text');
        const loading = btnAdicionar.querySelector('.loading');
        
        if (!form) {
            alert('❌ Erro: Formulário não encontrado');
            return;
        }
        
        // Validar campos
        const palavra = form.querySelector('[name="palavra"]').value.trim();
        const tipo = form.querySelector('[name="tipo"]').value;
        const motivo = form.querySelector('[name="motivo"]').value.trim();
        
        if (!palavra) {
            alert('❌ Por favor, digite uma palavra ou frase');
            form.querySelector('[name="palavra"]').focus();
            return;
        }
        
        if (!tipo) {
            alert('❌ Por favor, selecione o tipo');
            form.querySelector('[name="tipo"]').focus();
            return;
        }
        
        if (!motivo) {
            alert('❌ Por favor, digite o motivo');
            form.querySelector('[name="motivo"]').focus();
            return;
        }
        
        console.log('📨 Enviando dados:', { palavra, tipo, motivo });
        
        // Mostrar loading
        btnText.style.display = 'none';
        loading.style.display = 'inline-block';
        btnAdicionar.disabled = true;
        
        // Preparar dados
        const formData = new FormData(form);
        
        // Fazer requisição
        fetch('/moderacao/palavras-proibidas-globais', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('📞 Resposta do servidor:', response.status);
            if (!response.ok) {
                throw new Error('Erro HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Resposta JSON:', data);
            
            if (data.sucesso) {
                // Sucesso!
                alert('🎉 Palavra adicionada com sucesso!');
                fecharModal();
                
                // Recarregar a página após 1 segundo
                setTimeout(() => {
                    location.reload();
                }, 1000);
                
            } else {
                // Erro do servidor
                throw new Error(data.mensagem || 'Erro desconhecido do servidor');
            }
        })
        .catch(error => {
            console.error('❌ Erro completo:', error);
            alert('❌ Erro ao adicionar palavra: ' + error.message);
        })
        .finally(() => {
            // Restaurar botão
            btnText.style.display = 'inline-block';
            loading.style.display = 'none';
            btnAdicionar.disabled = false;
        });
    }
    
    // =============================================
    // 3. OUTRAS FUNÇÕES DO PAINEL
    // =============================================
    
    // Processar banimentos automáticos
    const btnProcessarAuto = document.getElementById('btnProcessarAuto');
    if (btnProcessarAuto) {
        btnProcessarAuto.addEventListener('click', function() {
            if (confirm('🔍 Deseja processar banimentos automáticos?\nIsso verificará usuários com 3 ou mais penalidades.')) {
                fetch('/moderacao/processar-banimentos-automaticos', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        const sistema = data.processados?.sistema || 0;
                        const interesse = data.processados?.interesse || 0;
                        alert(`✅ Banimentos processados!\nSistema: ${sistema}\nInteresse: ${interesse}`);
                        location.reload();
                    } else {
                        alert('❌ Erro: ' + data.mensagem);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('❌ Erro ao processar banimentos');
                });
            }
        });
    }
    
    // Gerar relatório
    const btnGerarRelatorio = document.getElementById('btnGerarRelatorio');
    if (btnGerarRelatorio) {
        btnGerarRelatorio.addEventListener('click', function() {
            const inicio = prompt('📅 Data início (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
            if (!inicio) return;
            
            const fim = prompt('📅 Data fim (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
            if (!fim) return;
            
            fetch('/moderacao/relatorios', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    periodo_inicio: inicio,
                    periodo_fim: fim
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert('📊 Relatório gerado com sucesso!');
                    if (data.url_download) {
                        window.open(data.url_download, '_blank');
                    }
                } else {
                    alert('❌ Erro: ' + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('❌ Erro ao gerar relatório');
            });
        });
    }
    
    // Verificar infrações
    document.querySelectorAll('.btn-verificar-infracao').forEach(btn => {
        btn.addEventListener('click', function() {
            const infracaoId = this.getAttribute('data-infracao-id');
            const acao = this.getAttribute('data-acao');
            
            if (acao === 'aplicar_penalidade') {
                const motivo = prompt('📝 Digite o motivo da penalidade:');
                if (!motivo) return;
                
                const peso = prompt('⚖️ Peso da penalidade (1-3):', '1');
                if (!peso) return;
                
                const dias = prompt('📅 Dias de expiração (enter para permanente):', '30');
                
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
                        alert('✅ Infração verificada e penalidade aplicada!');
                        location.reload();
                    } else {
                        alert('❌ Erro: ' + data.mensagem);
                    }
                })
                .catch(error => {
                    alert('❌ Erro ao processar infração');
                });
                
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
                        alert('✅ Infração ignorada!');
                        location.reload();
                    } else {
                        alert('❌ Erro: ' + data.mensagem);
                    }
                })
                .catch(error => {
                    alert('❌ Erro ao processar infração');
                });
            }
        });
    });
    
    // Remover palavras
    document.querySelectorAll('.btn-remover-palavra').forEach(btn => {
        btn.addEventListener('click', function() {
            const palavraId = this.getAttribute('data-palavra-id');
            
            if (!confirm('🗑️ Tem certeza que deseja remover esta palavra proibida global?')) {
                return;
            }
            
            fetch(`/moderacao/palavras-proibidas-globais/${palavraId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert('✅ Palavra removida com sucesso!');
                    location.reload();
                } else {
                    alert('❌ Erro: ' + data.mensagem);
                }
            })
            .catch(error => {
                alert('❌ Erro ao remover palavra');
            });
        });
    });
    
    console.log('🎉 Todas as funcionalidades configuradas!');
});

// Prevenir erros de outros scripts
window.IS_MODERATION_PAGE = true;
</script>
@endsection