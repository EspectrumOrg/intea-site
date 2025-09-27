@extends('admin.template.layout')

@section('main')

<link rel="stylesheet" href="{{ url('assets/css/dashboard/style.css') }}">

<div class="dashboard-root">
    <div class="dashboard-inner">
        <h1 class="dashboard-title"><img src="{{ asset('assets/images/logos/symbols/dashboard.png') }}"> Dashboard</h1>

        {{-- Cards estatísticos --}}
        <div class="cards-grid">
            <div class="card">
                <h5>Total Usuários</h5>
                <h3>{{ $totalUsuarios }}</h3>
            </div>
            <div class="card">
                <h5>Hoje</h5>
                <h3>{{ $usuariosHoje }}</h3>
            </div>
            <div class="card">
                <h5>Semana</h5>
                <h3>{{ $usuariosSemana }}</h3>
            </div>
            <div class="card">
                <h5>Mês</h5>
                <h3>{{ $usuariosMes }}</h3>
            </div>
        </div>

        {{-- Gráficos lado a lado (linha maior, pizza menor) --}}
        <div class="charts-grid">
            <div class="chart-box chart-box--wide">
                <h3 class="chart-title">Novos usuários, postagens e denúncias (últimos 7 dias)</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="usuariosChart"></canvas>
                </div>
            </div>

            <div class="chart-box chart-box--narrow">
                <h3 class="chart-title">Usuários por Tipo</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="usuariosTipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Preparar arrays plain (evita problemas com Collection/Blade) --}}
@php
    $dias_arr = (is_object($dias) && method_exists($dias,'toArray')) ? $dias->toArray() : (is_array($dias) ? $dias : (array) $dias);
    $usuariosPorDia_arr = (is_object($usuariosPorDia) && method_exists($usuariosPorDia,'toArray')) ? $usuariosPorDia->toArray() : (is_array($usuariosPorDia) ? $usuariosPorDia : (array) $usuariosPorDia);
    $postagensPorDia_arr = (is_object($postagensPorDia) && method_exists($postagensPorDia,'toArray')) ? $postagensPorDia->toArray() : (is_array($postagensPorDia) ? $postagensPorDia : (array) $postagensPorDia);
    $denunciasPorDia_arr = (is_object($denunciasPorDia) && method_exists($denunciasPorDia,'toArray')) ? $denunciasPorDia->toArray() : (is_array($denunciasPorDia) ? $denunciasPorDia : (array) $denunciasPorDia);

    if (is_object($usuariosPorTipoNomes) && method_exists($usuariosPorTipoNomes, 'toArray')) {
        $tmpTipo = $usuariosPorTipoNomes->toArray();
    } elseif (is_array($usuariosPorTipoNomes)) {
        $tmpTipo = $usuariosPorTipoNomes;
    } else {
        $tmpTipo = (array) $usuariosPorTipoNomes;
    }

     if (count($tmpTipo) === 0) {
        $tmpTipo = [
            'Administrador' => 0,
            'Autista'     => 0,
            'Comunidade'       => 0,
            'Profissional da Saúde'  => 0,
            'Responsável'     => 0,
        ];
    }



    // Garantir labels para tipo 1..5 mesmo que faltem alguns índices
    $tipo_keys = array_keys($tmpTipo); 
    $tipo_vals = array_values($tmpTipo); 

    // Normalizar: se estiver vazio, criar zeros para 1..5
    if (count($tipo_keys) === 0) {
        $tipo_keys = ['1','2','3','4','5'];
        $tipo_vals = [0,0,0,0,0];
    } else {
        // Se chaves forem numéricas (1,2..), padronizar strings
        $tipo_keys = array_map('strval', $tipo_keys);
        
    }
@endphp

{{-- Embutir em JSON puro para ler com JS (evita diretivas dentro do script) --}}
<script id="dashboard-data" type="application/json">
{!! json_encode([
    'labels' => $dias_arr,
    'usuariosPorDia' => $usuariosPorDia_arr,
    'postagensPorDia' => $postagensPorDia_arr,
    'denunciasPorDia' => $denunciasPorDia_arr,
    'usuariosPorTipoKeys' => $tipo_keys,
    'usuariosPorTipoVals' => $tipo_vals
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

{{-- Chart.js CDN (carregar antes do código que cria os charts) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ url('assets/js/dashboard/dashboard.js') }}"></script>



@endsection
