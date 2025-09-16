<x-app-layout>
    <div class="py-8 px-6 bg-gradient-to-b from-blue-900 to-white min-h-screen">

        <!-- Saudação -->
        <h1 class="text-2xl font-bold text-white mb-8">
            Bom dia, {{ strtoupper(Auth::user()->name) }} 👋
        </h1>

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
            <a href="#" class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
                <div class="text-3xl mb-3">🚗</div>
                <h2 class="font-semibold text-lg">Veículos</h2>
                <p class="text-sm text-gray-600 mt-2">Gerencie seus veículos.</p>
            </a>

            <a href="#" class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
                <div class="text-3xl mb-3">🚌</div>
                <h2 class="font-semibold text-lg">Frotas</h2>
                <p class="text-sm text-gray-600 mt-2">Organize grupos de veículos.</p>
            </a>

            <a href="#" class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
                <div class="text-3xl mb-3">💰</div>
                <h2 class="font-semibold text-lg">Gastos</h2>
                <p class="text-sm text-gray-600 mt-2">Registre despesas e manutenções.</p>
            </a>

            <a href="#" class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
                <div class="text-3xl mb-3">🔎</div>
                <h2 class="font-semibold text-lg">Buscar Veículos</h2>
                <p class="text-sm text-gray-600 mt-2">Consulte veículos públicos e compare gastos.</p>
            </a>

            <a href="#" class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 hover:bg-gray-200">
                <div class="text-3xl mb-3">👥</div>
                <h2 class="font-semibold text-lg">Responsáveis</h2>
                <p class="text-sm text-gray-600 mt-2">Convide e gerencie usuários.</p>
            </a>
        </div>

        <!-- Resumo -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 text-center hover:bg-gray-200">
                <p class="text-gray-600">Total de Veículos</p>
                <p class="text-2xl font-bold text-blue-600">12</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 text-center hover:bg-gray-200">
                <p class="text-gray-600">Gastos este mês</p>
                <p class="text-2xl font-bold text-green-600">R$ 2.345,00</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105 text-center hover:bg-gray-200">
                <p class="text-gray-600">Responsáveis Ativos</p>
                <p class="text-2xl font-bold text-purple-600">5</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Evolução de Gastos -->
            <div class="bg-white rounded-2xl shadow-lg p-6 h-96 w-full lg:w-[60%] mx-auto transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105">
                <h2 class="text-lg font-semibold mb-4">Evolução de Gastos (últimos 5 meses)</h2>
                <div class="flex items-center justify-center h-[85%]">
                    <canvas id="evolucaoGastos"></canvas>
                </div>
            </div>

            <!-- Distribuição dos Gastos -->
            <div class="bg-white rounded-2xl shadow-lg p-6 h-96 w-full lg:w-[60%] mx-auto transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 hover:scale-105">
                <h2 class="text-lg font-semibold mb-4">Distribuição dos Gastos</h2>
                <div class="flex items-center justify-center h-[85%]">
                    <canvas id="distribuicaoGastos"></canvas>
                </div>
            </div>
        </div>


        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Mock Evolução
            const ctxEvolucao = document.getElementById('evolucaoGastos').getContext('2d');
            new Chart(ctxEvolucao, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai'],
                    datasets: [{
                        label: 'Gastos (R$)',
                        data: [1200, 900, 1500, 800, 1345],
                        backgroundColor: '#3b82f6'
                    }]
                }
            });

            // Mock Distribuição
            const ctxDistribuicao = document.getElementById('distribuicaoGastos').getContext('2d');
            new Chart(ctxDistribuicao, {
                type: 'doughnut',
                data: {
                    labels: ['Combustível', 'Manutenção', 'Impostos', 'Outros'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                    }]
                }
            });
        </script>
</x-app-layout>