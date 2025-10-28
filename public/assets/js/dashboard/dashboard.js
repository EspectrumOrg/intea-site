document.addEventListener('DOMContentLoaded', function () {
    // Ler dados injetados pelo Blade (JSON puro)
    const raw = document.getElementById('dashboard-data').textContent;
    const payload = JSON.parse(raw);

    const labels = payload.labels || [];
    const usuariosData = payload.usuariosPorDia || [];
    const postagensData = payload.postagensPorDia || [];
    const denunciasData = payload.denunciasPorDia || [];
    const tendenciasData = payload.tendenciasPorDia || []; 
    const tipoLabels = payload.usuariosPorTipoKeys || [];
    const tipoValues = payload.usuariosPorTipoVals || [];

    // Configurações comuns
    const lineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15
                }
            },
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
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    };

    // === Gráfico de Linhas (Usuários, Postagens, Denúncias, Tendências) ===
    const ctxLine = document.getElementById('atividadeChart').getContext('2d'); // ✅ MUDOU: atividadeChart
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Novos Usuários',
                    data: usuariosData,
                    borderColor: 'rgba(54, 162, 235, 1)', // Azul
                    backgroundColor: 'rgba(54, 162, 235, 0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Novas Postagens',
                    data: postagensData,
                    borderColor: 'rgba(75, 192, 192, 1)', // Verde água
                    backgroundColor: 'rgba(75, 192, 192, 0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Denúncias',
                    data: denunciasData,
                    borderColor: 'rgba(255, 99, 132, 1)', // Vermelho
                    backgroundColor: 'rgba(255, 99, 132, 0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    fill: true,
                    tension: 0.3
                },
                // Dataset para Tendências
                {
                    label: 'Tendências Ativas',
                    data: tendenciasData,
                    borderColor: 'rgba(153, 102, 255, 1)', // Roxo
                    backgroundColor: 'rgba(153, 102, 255, 0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: lineOptions
    });

   // === Gráfico de Pizza (Usuários por Tipo) ===
const ctxPie = document.getElementById('usuariosTipoChart').getContext('2d');

// Filtrar para remover o Tipo 4 (Profissional de Saúde) --- Já que não é mais usado, MAS AINDA EXISTE NO BANCO DE DADOS
const filteredTipoLabels = [];
const filteredTipoValues = [];

tipoLabels.forEach((label, index) => {
    // Pular se for "Profissional de Saúde" ou tipo 4
    if (label !== 'Profissional de Saúde' && label !== '4') {
        filteredTipoLabels.push(label);
        filteredTipoValues.push(tipoValues[index]);
    }
});

new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: filteredTipoLabels,
        datasets: [{
            data: filteredTipoValues,
            backgroundColor: [
                '#048ABF', // Administrador - Azul
                '#1d3e55', // Autista - Azul escuro
                '#349dff', // Comunidade - Azul claro
                '#7c3aed'  // Responsável - Roxo
                // Removido: '#4f46e5' (Profissional Saúde)
            ],
            borderColor: '#ffffff',
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 11
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed !== undefined ? context.parsed : context.raw;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '50%'
    }
    });
});