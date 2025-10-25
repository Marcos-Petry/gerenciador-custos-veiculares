@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h1 class="text-2xl font-bold text-white mb-6">🔎 Consulta Pública</h1>

    {{-- 🔹 Filtros --}}
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-6">

        <!-- 🔸 Filtro Exibir e Relacionamento -->
        <div class="flex flex-wrap items-end gap-3 mb-3">
            <div>
                <label class="block text-white text-sm font-semibold mb-1">Exibir</label>
                <select name="tipoFiltro" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                    <option value="">Todos</option>
                    <option value="veiculo" {{ request('tipoFiltro') == 'veiculo' ? 'selected' : '' }}>Apenas Veículos</option>
                    <option value="frota" {{ request('tipoFiltro') == 'frota' ? 'selected' : '' }}>Apenas Frotas</option>
                </select>
            </div>

            <div>
                <label class="block text-white text-sm font-semibold mb-1">Relacionamento</label>
                <select name="relacionamentoFiltro" class="rounded-lg border-gray-300 px-3 py-1.5 w-56">
                    <option value="">Todos os Veículos</option>
                    <option value="com_frota" {{ request('relacionamentoFiltro') == 'com_frota' ? 'selected' : '' }}>Veículos com Frota</option>
                    <option value="sem_frota" {{ request('relacionamentoFiltro') == 'sem_frota' ? 'selected' : '' }}>Veículos sem Frota</option>
                </select>
            </div>
        </div>

        <!-- 🔸 Filtros dinâmicos -->
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo[]" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="titulo">Nome/Modelo</option>
                        <option value="placa">Placa</option>
                        <option value="ano">Ano</option>
                        <option value="frota_nome">Nome da Frota</option>
                    </select>
                </div>

                <!-- Operador -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador[]" class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"></select>
                </div>

                <!-- Valor -->
                <div class="valor-container flex items-end gap-2">
                    <!-- Texto -->
                    <div class="valor-texto">
                        <label class="block text-white text-sm font-semibold mb-1">Valor</label>
                        <input type="text" class="inp-texto rounded-lg border-gray-300 px-3 py-1.5 w-80"
                               placeholder="Digite o valor">
                    </div>

                    <!-- Entre -->
                    <div class="valor-entre hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Entre</label>
                        <div class="flex gap-2">
                            <input type="number" class="inp-de rounded-lg border-gray-300 px-3 py-1.5 w-28" placeholder="De">
                            <input type="number" class="inp-ate rounded-lg border-gray-300 px-3 py-1.5 w-28" placeholder="Até">
                        </div>
                    </div>
                </div>

                <!-- Botões principais -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                            class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>

                    <a href="{{ route('publico.index') }}"
                       class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
                </div>

                <!-- Remover -->
                <button type="button"
                        class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

    {{-- 🔹 Cards --}}
    <form id="form-selecao-veiculos" method="GET" action="{{ route('publico.index') }}">
        <input type="hidden" name="origemCampoExterno" value="{{ $origem ?? '' }}">
        <input type="hidden" name="selecionado" id="selecionadoInput">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($itens as $item)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
                @if($item->foto)
                <div class="w-full h-40 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Imagem" class="h-full w-auto object-cover">
                </div>
                @else
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">
                    {{ $item->tipo === 'frota' ? '🚛' : '🚗' }}
                </div>
                @endif

                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $item->titulo }}</h2>
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                            {{ $item->tipo === 'veiculo' ? 'bg-blue-500' : 'bg-orange-500' }}">
                            {{ ucfirst($item->tipo) }}
                        </span>
                    </div>

                    @if($item->tipo === 'veiculo')
                    <p class="text-sm text-gray-600">Placa: <strong>{{ $item->placa }}</strong></p>
                    <p class="text-sm text-gray-600">Ano: {{ $item->ano ?? '—' }}</p>
                    <p class="text-sm text-gray-600">Frota: {{ $item->frota_nome ?? '—' }}</p>
                    @else
                    <p class="text-sm text-gray-600 mb-1">Criada em: {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</p>
                    @endif

                    <div class="flex justify-between items-center mt-4">
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                            {{ $item->visibilidade ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $item->visibilidade ? 'Público' : 'Privado' }}
                        </span>

                        <div class="flex gap-2">
                            <a href="{{ $item->tipo === 'veiculo'
                                        ? route('veiculo.show', $item->id)
                                        : route('frota.show', $item->id) }}"
                                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                👁️ Ver
                            </a>

                            @if($item->tipo === 'veiculo')
                            <a href="{{ route('veiculo.gastos.index', $item->id) }}"
                                class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                                💰 Gastos
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p class="col-span-3 text-center text-gray-500">Nenhum veículo ou frota pública encontrada.</p>
            @endforelse
        </div>
    </form>

    <!-- 🔹 Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $itens->count() }}</strong> de <strong>{{ $itens->total() }}</strong> registros
        </div>
        <div>{{ $itens->onEachSide(1)->links() }}</div>
    </div>
</div>

<!-- 🔹 Script -->
<script>
const operadoresPorCampo = {
    titulo: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
        { valor: 'starts', texto: 'Começa com' },
        { valor: 'ends', texto: 'Termina com' },
    ],
    placa: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
    ],
    ano: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Maior que (>)' },
        { valor: '<', texto: 'Menor que (<)' },
        { valor: 'between', texto: 'Entre' },
    ],
    frota_nome: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
    ],
};

function mostrarSomente(mostra, ...esconde) {
    mostra.classList.remove('hidden');
    const ativo = mostra.querySelector('input,select');
    if (ativo) ativo.disabled = false;
    esconde.forEach(e => {
        e.classList.add('hidden');
        const i = e.querySelector('input,select');
        if (i) i.disabled = true;
    });
}

function atualizarLinha(item) {
    const campo = item.querySelector('.campo');
    const operador = item.querySelector('.operador');
    const vTexto = item.querySelector('.valor-texto');
    const vEntre = item.querySelector('.valor-entre');

    const operadorSelecionado = operador.value;

    operador.innerHTML = '';
    (operadoresPorCampo[campo.value] || []).forEach(o => {
        const opt = document.createElement('option');
        opt.value = o.valor;
        opt.textContent = o.texto;
        operador.appendChild(opt);
    });

    if ([...operador.options].some(opt => opt.value === operadorSelecionado)) {
        operador.value = operadorSelecionado;
    }

    const opValue = operador.value;

    if (campo.value === 'ano' && opValue === 'between') {
        mostrarSomente(vEntre, vTexto);
    } else {
        mostrarSomente(vTexto, vEntre);
    }
}

document.addEventListener('change', e => {
    const item = e.target.closest('.filtro-item');
    if (!item) return;
    if (e.target.classList.contains('campo') || e.target.classList.contains('operador')) {
        atualizarLinha(item);
    }
});

document.getElementById('add-filtro').addEventListener('click', () => {
    const container = document.getElementById('filtros-container');
    const clone = container.querySelector('.filtro-item').cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value = '');
    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    const botoes = clone.querySelector('.botoes-principais');
    if (botoes) botoes.remove();
    const rm = clone.querySelector('.remover-filtro');
    rm.classList.remove('hidden');
    rm.onclick = () => clone.remove();
    atualizarLinha(clone);
    container.appendChild(clone);
});

document.querySelectorAll('.filtro-item').forEach(atualizarLinha);
</script>
@endsection
