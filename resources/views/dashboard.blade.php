@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <!-- Saudação -->
    <h1 class="text-2xl font-bold text-white mb-8">
        Olá, {{ strtoupper(Auth::user()->name) }} 👋
    </h1>

    <!-- Grid de Acesso Rápido -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
        <a href="{{ route('veiculo.index') }}" class="bg-white rounded-2xl shadow-lg p-6 transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
            <div class="text-3xl mb-3">🚗</div>
            <h2 class="font-semibold text-lg">Veículos</h2>
            <p class="text-sm text-gray-600 mt-2">Gerencie seus veículos.</p>
        </a>

        <a href="{{ route('frota.index') }}" class="bg-white rounded-2xl shadow-lg p-6 transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
            <div class="text-3xl mb-3">🚌</div>
            <h2 class="font-semibold text-lg">Frotas</h2>
            <p class="text-sm text-gray-600 mt-2">Organize grupos de veículos.</p>
        </a>

        <a href="{{ route('gastos.index') }}" class="bg-white rounded-2xl shadow-lg p-6 transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
            <div class="text-3xl mb-3">💰</div>
            <h2 class="font-semibold text-lg">Gastos</h2>
            <p class="text-sm text-gray-600 mt-2">Registre e acompanhe todas as despesas.</p>
        </a>

        <a href="{{ route('publico.index') }}" class="bg-white rounded-2xl shadow-lg p-6 transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
            <div class="text-3xl mb-3">🔎</div>
            <h2 class="font-semibold text-lg">Consulta Pública</h2>
            <p class="text-sm text-gray-600 mt-2">Visualize veículos e frotas públicos</p>
        </a>

        <a href="{{ route('publico.comparar') }}" class="bg-white rounded-2xl shadow-lg p-6 transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
            <div class="text-3xl mb-3">⚖️</div>
            <h2 class="font-semibold text-lg">Comparar Gastos</h2>
            <p class="text-sm text-gray-600 mt-2">Compare gastos entre veículos.</p>
        </a>
    </div>

    <!-- 🔹 Cards de Dados -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Evolução de Gastos -->
        <div class="bg-white rounded-2xl shadow-lg p-6 h-[27rem] transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105">
            <h2 class="text-lg font-semibold mb-4">Evolução de Gastos (últimos 5 meses)</h2>
            <div class="flex items-center justify-center h-[85%] overflow-visible">
                <canvas id="evolucaoGastos"></canvas>
            </div>
        </div>

        <!-- Distribuição dos Gastos -->
        <div class="bg-white rounded-2xl shadow-lg p-6 h-[27rem] transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 relative">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Distribuição dos Gastos (últimos 5 meses)</h2>
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-2xl font-bold text-green-600">
                        R$ {{ number_format($gastosMes, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-center h-[85%] overflow-visible">
                <canvas id="distribuicaoGastos"></canvas>
            </div>
        </div>

        <!-- Resumo de Responsabilidade -->
        <div class="bg-white rounded-2xl shadow-lg p-6 h-[27rem] transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105 flex flex-col">
            <h2 class="text-lg font-semibold mb-8 text-gray-800 text-center">Resumo do Usuário</h2>

            <div class="grid grid-cols-2 gap-6 text-center">
                <!-- Coluna Veículos -->
                <div>
                    <h3 class="text-base font-semibold text-blue-600 mb-3">Veículos</h3>
                    <p class="text-gray-600 text-sm">Dono</p>
                    <p class="text-3xl font-bold text-blue-700 mb-2">{{ $veiculosComoDono }}</p>

                    <p class="text-gray-600 text-sm">Responsável</p>
                    <p class="text-3xl font-bold text-indigo-600 mb-2">{{ $veiculosComoResponsavel }}</p>

                    <hr class="my-3 border-gray-200">
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $veiculosComoDono + $veiculosComoResponsavel }}
                    </p>
                </div>

                <!-- Coluna Frotas -->
                <div>
                    <h3 class="text-base font-semibold text-purple-600 mb-3">Frotas</h3>
                    <p class="text-gray-600 text-sm">Dono</p>
                    <p class="text-3xl font-bold text-purple-700 mb-2">{{ $frotasComoDono }}</p>

                    <p class="text-gray-600 text-sm">Responsável</p>
                    <p class="text-3xl font-bold text-indigo-500 mb-2">{{ $frotasComoResponsavel }}</p>

                    <hr class="my-3 border-gray-200">
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $frotasComoDono + $frotasComoResponsavel }}
                    </p>
                </div>
            </div>

            <hr class="my-6 border-gray-300">
            
            <!-- Total Geral -->
            <div class="text-center mt-auto">
                <p class="text-gray-700 text-sm">Total Geral</p>
                <p class="text-3xl font-bold text-emerald-600">
                    {{ $veiculosComoDono + $veiculosComoResponsavel + $frotasComoDono + $frotasComoResponsavel }}
                </p>
            </div>
        </div>

    </div>
</div>

<!-- Scripts do Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- Dados JSON -->
<script id="chart-data" type="application/json">
{!! json_encode([
    'labels' => $labels,
    'valores' => $valores,
    'gastosPorCategoria' => $gastosPorCategoria->toArray()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const rawData = document.getElementById('chart-data')?.textContent || '{}';
    const data = JSON.parse(rawData);

    const valoresOriginais = data.valores || [];
    const labelsOriginais = data.labels || [];
    const gastosPorCategoria = data.gastosPorCategoria || {};

    // ===== Gráfico de evolução =====
const ctxEvolucao = document.getElementById('evolucaoGastos');
if (ctxEvolucao) {
    const mesesNomes = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    const agora = new Date();
    const mesAtual = agora.getMonth(); // 0 = Jan, 11 = Dez

    // 🔹 Gera os últimos 5 meses (excluindo meses futuros)
    const labels = Array.from({ length: 5 }, (_, i) => {
        const mes = (mesAtual - 4 + i + 12) % 12;
        return mesesNomes[mes];
    });

    // 🔹 Preenche os valores conforme labels do backend
    const valoresCompletos = labels.map(m => {
        const i = labelsOriginais.indexOf(m);
        return i !== -1 ? valoresOriginais[i] : 0;
    });

    new Chart(ctxEvolucao, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gastos (R$)',
                data: valoresCompletos,
                backgroundColor: '#3b82f6',
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#111827',
                    font: { weight: 'bold' },
                    formatter: (val) => val > 0 ? `R$ ${val.toLocaleString('pt-BR')}` : ''
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `R$ ${ctx.formattedValue.replace('.', ',')}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#4b5563',
                        callback: (val) => `R$ ${val.toLocaleString('pt-BR')}`
                    },
                    grid: { color: '#e5e7eb' }
                },
                x: {
                    ticks: { color: '#4b5563' },
                    grid: { display: false }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}


    // ===== Gráfico de distribuição =====
    const ctxDistribuicao = document.getElementById('distribuicaoGastos');
    if (ctxDistribuicao) {
        const categorias = Object.keys(gastosPorCategoria);
        const valoresCat = Object.values(gastosPorCategoria);
        const maxValue = Math.max(...valoresCat);
        const rightPadding = maxValue > 9999 ? 110 : 90;

        if (valoresCat.length > 0 && valoresCat.some(v => v > 0)) {
            new Chart(ctxDistribuicao, {
                type: 'bar',
                data: {
                    labels: categorias,
                    datasets: [{
                        label: 'Gastos por Categoria (R$)',
                        data: valoresCat,
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { right: rightPadding }
                    },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: '#111827',
                            align: 'end',
                            anchor: 'end',
                            clamp: false,
                            clip: false,
                            font: { size: 11, weight: 'bold' },
                            formatter: (val) => `R$ ${val.toLocaleString('pt-BR')}`
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `R$ ${ctx.formattedValue.replace('.', ',')}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#4b5563',
                                callback: (val) => `R$ ${val.toLocaleString('pt-BR')}`
                            },
                            grid: { color: '#e5e7eb' }
                        },
                        y: {
                            ticks: { color: '#4b5563' },
                            grid: { display: false }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        } else {
            ctxDistribuicao.parentNode.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-500 italic">
                    Sem dados suficientes para gerar o gráfico.
                </div>
            `;
        }
    }
});
</script>

@endsection
