{{--
    Componente: Estatísticas de Moderação
    Uso: @include('moderacao.componentes.estatisticas', ['estatisticas' => $estatisticas, 'titulo' => 'Título Opcional'])
--}}

@props(['estatisticas', 'titulo' => 'Estatísticas de Moderação', 'tipo' => 'default'])

<div class="estatisticas-mod">
    @if($titulo)
        <h6 class="estatisticas-titulo">
            <i class="material-symbols-outlined">analytics</i>
            {{ $titulo }}
        </h6>
    @endif
    
    <div class="estatisticas-grid">
        <!-- Postagens -->
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon">
                <i class="material-symbols-outlined">article</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['postagens_visiveis'] ?? 0 }}</div>
                <div class="estatistica-label">Postagens Visíveis</div>
            </div>
        </div>

        <!-- Bloqueadas -->
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon bloqueadas">
                <i class="material-symbols-outlined">block</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['postagens_bloqueadas_auto'] ?? 0 }}</div>
                <div class="estatistica-label">Bloqueadas Auto</div>
            </div>
        </div>

        <!-- Removidas -->
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon removidas">
                <i class="material-symbols-outlined">delete</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['postagens_removidas_manual'] ?? 0 }}</div>
                <div class="estatistica-label">Removidas Manual</div>
            </div>
        </div>

        <!-- Alertas -->
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon alertas">
                <i class="material-symbols-outlined">warning</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['alertas_ativos'] ?? 0 }}</div>
                <div class="estatistica-label">Alertas Ativos</div>
            </div>
        </div>

        <!-- Expulsões -->
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon expulsoes">
                <i class="material-symbols-outlined">person_remove</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['usuarios_expulsos'] ?? 0 }}</div>
                <div class="estatistica-label">Usuários Expulsos</div>
            </div>
        </div>

        <!-- Infrações -->
        @if(isset($estatisticas['infracoes_pendentes']))
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon infracoes">
                <i class="material-symbols-outlined">gavel</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['infracoes_pendentes'] }}</div>
                <div class="estatistica-label">Infrações Pendentes</div>
            </div>
        </div>
        @endif

        <!-- Penalidades -->
        @if(isset($estatisticas['penalidades_ativas']))
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon penalidades">
                <i class="material-symbols-outlined">balance</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['penalidades_ativas'] }}</div>
                <div class="estatistica-label">Penalidades Ativas</div>
            </div>
        </div>
        @endif

        <!-- Palavras Proibidas -->
        @if(isset($estatisticas['palavras_proibidas']))
        <div class="estatistica-card {{ $tipo }}">
            <div class="estatistica-icon palavras">
                <i class="material-symbols-outlined">block</i>
            </div>
            <div class="estatistica-content">
                <div class="estatistica-valor">{{ $estatisticas['palavras_proibidas'] }}</div>
                <div class="estatistica-label">Palavras Proibidas</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Gráfico de Tendências (Opcional) -->
    @if(isset($estatisticas['tendencias']) && count($estatisticas['tendencias']) > 0)
    <div class="estatisticas-tendencias mt-3">
        <h7 class="mb-2">Tendências Recentes</h7>
        <div class="tendencias-list">
            @foreach($estatisticas['tendencias'] as $tendencia)
            <div class="tendencia-item">
                <span class="tendencia-label">{{ $tendencia['label'] }}</span>
                <span class="tendencia-valor {{ $tendencia['aumento'] ? 'positivo' : 'negativo' }}">
                    {{ $tendencia['aumento'] ? '+' : '' }}{{ $tendencia['valor'] }}%
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
.estatisticas-mod {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.estatisticas-titulo {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.estatisticas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.estatistica-card {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.estatistica-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.estatistica-card.compact {
    padding: 0.75rem;
    gap: 0.75rem;
}

.estatistica-card.compact .estatistica-icon {
    width: 40px;
    height: 40px;
    font-size: 1.25rem;
}

.estatistica-card.compact .estatistica-valor {
    font-size: 1.25rem;
}

.estatistica-card.compact .estatistica-label {
    font-size: 0.75rem;
}

.estatistica-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: #3b82f6;
    color: white;
}

.estatistica-icon.bloqueadas {
    background: #ef4444;
}

.estatistica-icon.removidas {
    background: #f59e0b;
}

.estatistica-icon.alertas {
    background: #f59e0b;
}

.estatistica-icon.expulsoes {
    background: #dc2626;
}

.estatistica-icon.infracoes {
    background: #8b5cf6;
}

.estatistica-icon.penalidades {
    background: #7c3aed;
}

.estatistica-icon.palavras {
    background: #6b7280;
}

.estatistica-content {
    flex: 1;
}

.estatistica-valor {
    font-size: 1.5rem;
    font-weight: bold;
    color: #1f2937;
    line-height: 1;
}

.estatistica-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.estatisticas-tendencias {
    border-top: 1px solid #e5e7eb;
    padding-top: 1rem;
}

.tendencias-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.tendencia-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 6px;
}

.tendencia-label {
    font-size: 0.875rem;
    color: #374151;
}

.tendencia-valor {
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.tendencia-valor.positivo {
    background: #d1fae5;
    color: #065f46;
}

.tendencia-valor.negativo {
    background: #fee2e2;
    color: #dc2626;
}

/* Responsividade */
@media (max-width: 768px) {
    .estatisticas-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .estatistica-card {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .estatistica-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .estatistica-valor {
        font-size: 1.25rem;
    }
    
    .estatistica-label {
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .estatisticas-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>