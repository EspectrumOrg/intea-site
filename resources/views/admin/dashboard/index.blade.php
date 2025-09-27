@extends('admin.template.layout')

@section('main')
<div class="container">
    <h1 class="mb-4">📊 Dashboard</h1>

    {{-- Cards estatísticos --}}
    <div class="row mb-6">
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>Total Usuários</h5>
                    <h3>{{ $totalUsuarios }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>Hoje</h5>
                    <h3>{{ $usuariosHoje }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>Semana</h5>
                    <h3>{{ $usuariosSemana }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>Mês</h5>
                    <h3>{{ $usuariosMes }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Novos usuários nos últimos 7 dias</h5>
            <canvas id="usuariosChart"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('usuariosChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dias),
            datasets: [{
                label: 'Novos Usuários',
                data: @json($usuariosPorDia),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
    
@endsection