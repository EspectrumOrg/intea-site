@extends('admin.template.layout')

@section('main')
<link rel="stylesheet" href="{{ url('assets/css/dashboard/style.css') }}">

<div class="dashboard-root">
    <div class="dashboard-inner">
        <h1 class="dashboard-title">
            <span class="material-symbols-outlined">monitoring</span>
            Dashboard
        </h1>

        {{-- Cards estatísticos --}}
        <div class="cards-grid">
            {{-- Usuários --}}
            <div class="card">
                <div class="card-icon">
                    <span class="material-symbols-outlined">people</span>
                </div>
                <h5>Total Usuários</h5>
                <h3>{{ $totalUsuarios }}</h3>
                <div class="card-stats">
                    <span class="stat-today">+{{ $usuariosHoje }} hoje</span>
                    <span class="stat-week">+{{ $usuariosSemana }} semana</span>
                </div>
            </div>

            {{-- Tendências --}}
            <div class="card">
                <div class="card-icon">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <h5>Total Tendências</h5>
                <h3>{{ $totalTendencias }}</h3>
                <div class="card-stats">
                    <span class="stat-today">+{{ $tendenciasHoje }} hoje</span>
                    <span class="stat-week">+{{ $tendenciasSemana }} semana</span>
                </div>
            </div>

            {{-- Postagens --}}
            <div class="card">
                <div class="card-icon">
                    <span class="material-symbols-outlined">article</span>
                </div>
                <h5>Postagens (7 dias)</h5>
                <h3>{{ $postagensPorDia->sum() }}</h3>
                <div class="card-stats">
                    <span class="stat-week">{{ $postagensPorDia->last() }} hoje</span>
                </div>
            </div>

            {{-- Denúncias --}}
            <div class="card">
                <div class="card-icon">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <h5>Denúncias (7 dias)</h5>
                <h3>{{ $denunciasPorDia->sum() }}</h3>
                <div class="card-stats">
                    <span class="stat-week">{{ $denunciasPorDia->last() }} hoje</span>
                </div>
            </div>
        </div>

        {{-- Gráficos lado a lado --}}
        <div class="charts-grid">
            {{-- Gráfico principal de atividade --}}
            <div class="chart-box chart-box--wide">
                <h3 class="chart-title">Atividade (últimos 7 dias)</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="atividadeChart"></canvas>
                </div>
            </div>

            {{-- Gráfico de tipos de usuário --}}
            <div class="chart-box chart-box--narrow">
                <h3 class="chart-title">Usuários por Tipo</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="usuariosTipoChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Seção: Tendências Populares --}}
        <div class="tendencias-section">
            <div class="section-header">
                <h2>
                    <span class="material-symbols-outlined">trending_up</span>
                    Tendências Populares
                </h2>
                <a href="{{ route('tendencias.index') }}" class="btn-view-all">
                    Ver todas
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            <div class="tendencias-grid">
                @forelse($topTendencias as $tendencia)
                    <div class="tendencia-card">
                        <div class="tendencia-header">
                            <h4 class="tendencia-hashtag">{{ $tendencia->hashtag }}</h4>
                            <span class="tendencia-count">{{ $tendencia->contador_uso }} usos</span>
                        </div>
                        <div class="tendencia-body">
                            <p class="tendencia-slug">#{{ $tendencia->slug }}</p>
                            <div class="tendencia-meta">
                                <span class="tendencia-date">
                                    <span class="material-symbols-outlined">schedule</span>
                                    {{ $tendencia->ultimo_uso ? $tendencia->ultimo_uso->diffForHumans() : 'Nunca usado' }}
                                </span>
                            </div>
                        </div>
                        <div class="tendencia-actions">
                            <a href="{{ route('tendencias.show', $tendencia->slug) }}" class="btn-tendencia">
                                Ver posts
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="no-tendencias">
                        <span class="material-symbols-outlined">search_off</span>
                        <p>Nenhuma tendência encontrada</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Preparar dados para JavaScript --}}
@php
    // Converter collections para arrays para JavaScript
    $dias_arr = $dias->toArray();
    $usuariosPorDia_arr = $usuariosPorDia->toArray();
    $postagensPorDia_arr = $postagensPorDia->toArray();
    $denunciasPorDia_arr = $denunciasPorDia->toArray();
    $tendenciasPorDia_arr = $tendenciasPorDia->toArray();

    // Dados dos tipos de usuário
    $tipo_keys = $usuariosPorTipoNomes->keys()->toArray();
    $tipo_vals = $usuariosPorTipoNomes->values()->toArray();
@endphp

{{-- Dados para JavaScript --}}
<script id="dashboard-data" type="application/json">
{!! json_encode([
    'labels' => $dias_arr,
    'usuariosPorDia' => $usuariosPorDia_arr,
    'postagensPorDia' => $postagensPorDia_arr,
    'denunciasPorDia' => $denunciasPorDia_arr,
    'tendenciasPorDia' => $tendenciasPorDia_arr,
    'usuariosPorTipoKeys' => $tipo_keys,
    'usuariosPorTipoVals' => $tipo_vals
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ url('assets/js/dashboard/dashboard.js') }}"></script>
@endsection