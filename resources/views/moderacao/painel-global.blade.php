@extends('feed.post.template.layout')

@section('styles')
    @parent
    <style>
        .moderacao-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .moderacao-card:hover {
            transform: translateY(-5px);
        }
        
        .moderacao-card h4 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .moderacao-card small {
            opacity: 0.9;
            font-weight: 500;
        }
        
        .btn-mod {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-mod:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .btn-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .infracao-item {
            border-left: 4px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #f8fafc;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
        }
        
        .infracao-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .infracao-item.critica {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        }
        
        .infracao-item.media {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fed7aa 100%);
        }
        
        .infracao-item.leve {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #bbf7d0 100%);
        }
        
        .badge-mod {
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
        }
        
        /* MODAL ESTILIZADO */
        #meuModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 10000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content-custom {
            background: white;
            margin: 100px auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            border: none;
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-header-custom {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .modal-header-custom h4 {
            margin: 0;
            font-weight: 700;
        }
        
        .modal-body-custom {
            padding: 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
        }
        
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10001;
            min-width: 300px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
@endsection

@section('main')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-dark text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">
                                <i class="material-symbols-outlined me-2">admin_panel_settings</i>
                                Painel Global de Moderação
                            </h3>
                            <p class="mb-0 opacity-75">
                                <i class="material-symbols-outlined me-1">shield</i>
                                Sistema completo de gestão e moderação
                            </p>
                        </div>
                        <button class="btn btn-primary btn-mod" onclick="abrirModal()">
                            <i class="material-symbols-outlined me-2">add</i>
                            Adicionar Palavra Global
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Modal -->
                    <div id="meuModal">
                        <div class="modal-content-custom">
                            <div class="modal-header-custom">
                                <h4>
                                    <i class="material-symbols-outlined me-2">add</i>
                                    Adicionar Palavra Proibida
                                </h4>
                                <button type="button" class="btn-close btn-close-white" onclick="fecharModal()"></button>
                            </div>
                            <div class="modal-body-custom">
                                <form method="POST" action="/moderacao/palavras-proibidas-globais">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Palavra/Frase</label>
                                        <input type="text" name="palavra" class="form-control" required placeholder="Digite a palavra ou frase proibida">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Tipo</label>
                                        <select name="tipo" class="form-select" required>
                                            <option value="">Selecione o tipo</option>
                                            <option value="exata">Exata (palavra completa)</option>
                                            <option value="parcial">Parcial (contém a palavra)</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Motivo</label>
                                        <textarea name="motivo" class="form-control" rows="4" required placeholder="Explique o motivo para bloquear esta palavra"></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary btn-mod" onclick="fecharModal()">Cancelar</button>
                                        <button type="submit" class="btn btn-primary btn-mod">Adicionar Palavra</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="moderacao-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4>{{ $infracoesPendentes->total() }}</h4>
                                            <small>Infrações Pendentes</small>
                                        </div>
                                        <i class="material-symbols-outlined" style="font-size: 2.5rem; opacity: 0.8;">warning</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="moderacao-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4>{{ $palavrasProibidasGlobais->count() }}</h4>
                                            <small>Palavras Globais</small>
                                        </div>
                                        <i class="material-symbols-outlined" style="font-size: 2.5rem; opacity: 0.8;">block</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="moderacao-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4>{{ $penalidadesRecentes->count() }}</h4>
                                            <small>Penalidades Recentes</small>
                                        </div>
                                        <i class="material-symbols-outlined" style="font-size: 2.5rem; opacity: 0.8;">history</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="moderacao-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4>{{ $estatisticas['infracoes_pendentes'] ?? 0 }}</h4>
                                            <small>Total do Sistema</small>
                                        </div>
                                        <i class="material-symbols-outlined" style="font-size: 2.5rem; opacity: 0.8;">analytics</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="mb-4 p-4 border rounded bg-light">
                        <h5 class="mb-3">
                            <i class="material-symbols-outlined me-2">bolt</i>
                            Ações Rápidas
                        </h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="/moderacao/processar-banimentos-automaticos" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-mod">
                                    <i class="material-symbols-outlined me-2">autorenew</i>
                                    Processar Banimentos
                                </button>
                            </form>
                            
                            <form method="POST" action="/moderacao/relatorios" class="d-inline">
                                @csrf
                                <input type="hidden" name="periodo_inicio" value="{{ now()->format('Y-m-d') }}">
                                <input type="hidden" name="periodo_fim" value="{{ now()->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-info btn-mod">
                                    <i class="material-symbols-outlined me-2">description</i>
                                    Gerar Relatório
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Infrações -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="material-symbols-outlined me-2">warning</i>
                                        Infrações Pendentes
                                        <span class="badge bg-danger badge-mod">{{ $infracoesPendentes->total() }}</span>
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
                                                                 class="user-avatar me-3" alt="Avatar">
                                                            <div>
                                                                <strong>{{ $infracao->usuario->nome }}</strong>
                                                                <div class="d-flex align-items-center mt-1">
                                                                    <span class="badge bg-secondary badge-mod me-2">{{ $infracao->tipo }}</span>
                                                                    <small class="text-muted">{{ $infracao->created_at->diffForHumans() }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="mb-2"><strong>Descrição:</strong> {{ $infracao->descricao }}</p>
                                                        @if($infracao->conteudo_original)
                                                            <p class="mb-2"><strong>Conteúdo:</strong> {{ Str::limit($infracao->conteudo_original, 200) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="acoes-mod mt-3 d-flex gap-2 flex-wrap">
                                                    <form method="POST" action="/moderacao/infracoes/{{ $infracao->id }}/verificar" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="acao" value="aplicar_penalidade">
                                                        <input type="hidden" name="motivo_penalidade" value="Infração verificada">
                                                        <input type="hidden" name="peso_penalidade" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm btn-mod">
                                                            Aplicar Penalidade
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" action="/moderacao/infracoes/{{ $infracao->id }}/verificar" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="acao" value="ignorar">
                                                        <button type="submit" class="btn btn-secondary btn-sm btn-mod">
                                                            Ignorar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="material-symbols-outlined text-success" style="font-size: 4rem;">check_circle</i>
                                            <h5 class="text-muted mt-3">Nenhuma infração pendente</h5>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Palavras Proibidas -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="material-symbols-outlined me-2">block</i>
                                        Palavras Proibidas Globais
                                        <span class="badge bg-secondary badge-mod">{{ $palavrasProibidasGlobais->count() }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($palavrasProibidasGlobais->count() > 0)
                                        @foreach($palavrasProibidasGlobais as $palavra)
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded bg-white">
                                                <div class="d-flex align-items-center">
                                                    <strong class="text-dark">{{ $palavra->palavra }}</strong>
                                                    <span class="badge {{ $palavra->tipo === 'exata' ? 'bg-danger' : 'bg-warning text-dark' }} badge-mod ms-3">
                                                        {{ $palavra->tipo }}
                                                    </span>
                                                </div>
                                                <form method="POST" action="/moderacao/palavras-proibidas-globais/{{ $palavra->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-mod" onclick="return confirm('Tem certeza que deseja remover esta palavra?')">
                                                        <i class="material-symbols-outlined">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3">
                                            <i class="material-symbols-outlined text-muted" style="font-size: 3rem;">block</i>
                                            <p class="text-muted mt-2 mb-0">Nenhuma palavra global definida</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mensagens -->
@if(session('success'))
<div class="alert alert-success alert-fixed alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-fixed alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<script>
// FUNÇÕES DO MODAL
function abrirModal() {
    document.getElementById('meuModal').style.display = 'block';
}

function fecharModal() {
    document.getElementById('meuModal').style.display = 'none';
}

// Fechar modal clicando fora
document.addEventListener('click', function(e) {
    if (e.target.id === 'meuModal') {
        fecharModal();
    }
});

// Loading nos formulários
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            }
        });
    });
});

// Auto-remover alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => alert.remove());
}, 5000);
</script>
@endsection