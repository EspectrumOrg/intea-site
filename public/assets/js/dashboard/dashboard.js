document.addEventListener('DOMContentLoaded', function () {
    // Ler dados injetados pelo Blade (JSON puro)
    const raw = document.getElementById('dashboard-data').textContent;
    const payload = JSON.parse(raw);

    const labels = payload.labels || [];
    const usuariosData = payload.usuariosPorDia || [];
    const postagensData = payload.postagensPorDia || [];
    const denunciasData = payload.denunciasPorDia || [];
    const tipoLabels = payload.usuariosPorTipoKeys || [];
    const tipoValues = payload.usuariosPorTipoVals || [];

    // Configurações comuns
    const lineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        // context.parsed.y para linhas (Chart.js v3+)
                        const v = context.parsed && context.parsed.y !== undefined ? context.parsed.y : context.raw;
                        return `${context.dataset.label}: ${v}`;
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: { maxRotation: 0, autoSkip: true }
            },
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    };

    // === Gráfico de Linhas (Usuários, Postagens, Denúncias) ===
    const ctxLine = document.getElementById('usuariosChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Novos Usuários',
                    data: usuariosData,
                    borderColor: 'rgba(54,162,235,1)',
                    backgroundColor: 'rgba(54,162,235,0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Novas Postagens',
                    data: postagensData,
                    borderColor: 'rgba(75,192,192,1)',
                    backgroundColor: 'rgba(75,192,192,0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Denúncias',
                    data: denunciasData,
                    borderColor: 'rgba(255,99,132,1)',
                    backgroundColor: 'rgba(255,99,132,0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: lineOptions
    });

    // === Gráfico de Pizza (Usuários por Tipo) ===
    const ctxPie = document.getElementById('usuariosTipoChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: tipoLabels,
            datasets: [{
                data: tipoValues,
                backgroundColor: [
                    '#048ABF',
                    '#1d3e55',
                    '#349dff',
                    'rgba(99, 102, 241, 0.2)',
                    'rgba(0, 83, 238, 0.3)'
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const v = context.parsed !== undefined ? context.parsed : context.raw;
                            return `${context.label}: ${v}`;
                        }
                    }
                }
            }
        }
    });
});