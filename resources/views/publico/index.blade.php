@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h1 class="text-2xl font-bold text-white mb-6">🔎 Consulta Pública</h1>

    {{-- 🔹 Filtros Dinâmicos --}}
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-6">
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo[]" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-52">
                        <option value="titulo">Nome/Modelo</option>
                        <option value="placa">Placa</option>
                        <option value="ano">Ano</option>
                        <option value="frota_nome">Nome da Frota</option>
                        <option value="tipoFiltro">Tipo (Veículo/Frota)</option>
                        <option value="relacionamentoFiltro">Relacionamento (Com/Sem Frota)</option>
                    </select>
                </div>

                <!-- Operador -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador[]" class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"></select>
                </div>

                <!-- Valores -->
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

                    <!-- Tipo -->
                    <div class="valor-tipo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Tipo</label>
                        <select class="sel-tipo rounded-lg border-gray-300 px-3 py-1.5 w-52" disabled>
                            <option value="">Selecione</option>
                            <option value="veiculo">Veículo</option>
                            <option value="frota">Frota</option>
                        </select>
                    </div>

                    <!-- Relacionamento -->
                    <div class="valor-relacionamento hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Relacionamento</label>
                        <select class="sel-relacionamento rounded-lg border-gray-300 px-3 py-1.5 w-64" disabled>
                            <option value="">Selecione</option>
                            <option value="com_frota">Com Frota</option>
                            <option value="sem_frota">Sem Frota</option>
                        </select>
                    </div>
                </div>

                <!-- Botões -->
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
    @if(isset($origem))
        <input type="hidden" name="origemCampoExterno" value="{{ $origem }}">
        <input type="hidden" name="{{ $origem }}" id="selecionado">
        <input type="hidden" name="veiculoA" value="{{ request('veiculoA') }}">
        <input type="hidden" name="veiculoB" value="{{ request('veiculoB') }}">
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($itens as $item)
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
                <div
                    class="w-full h-40 flex items-center justify-center {{ $item->foto ? '' : 'bg-gray-100 text-gray-400 text-5xl' }}">
                    @if($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Imagem"
                            class="h-full w-auto object-cover">
                    @else
                        {{ $item->tipo === 'frota' ? '🚛' : '🚗' }}
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $item->titulo }}</h2>
                        <span
                            class="px-2 py-1 rounded-full text-white text-xs font-medium {{ $item->tipo === 'veiculo' ? 'bg-blue-500' : 'bg-orange-500' }}">
                            {{ ucfirst($item->tipo) }}
                        </span>
                    </div>

                    @if($item->tipo === 'veiculo')
                        <p class="text-sm text-gray-600">Placa: <strong>{{ $item->placa }}</strong></p>
                        <p class="text-sm text-gray-600">Ano: {{ $item->ano ?? '—' }}</p>
                        <p class="text-sm text-gray-600">Frota: {{ $item->frota_nome ?? '—' }}</p>
                    @else
                        <p class="text-sm text-gray-600 mb-1">Criada em:
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</p>
                    @endif

                    <div class="flex justify-between items-center mt-4">
                        <span
                            class="px-2 py-1 rounded-full text-white text-xs font-medium {{ $item->visibilidade ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $item->visibilidade ? 'Público' : 'Privado' }}
                        </span>

                        {{-- ✅ Se for acesso externo, mostra a checkbox --}}
                        @if(isset($origem))
                            <input type="radio" name="selecionado" value="{{ $item->id }}"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer"
                                onclick="this.form.submit()">
                        @else
                            <div class="flex gap-2">
                                {{-- 👁️ Ver --}}
                                <a href="{{ $item->tipo === 'veiculo'
                                            ? route('veiculo.show', ['veiculo' => $item->id, 'from_publico' => 1])
                                            : route('frota.show', $item->id) }}"
                                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                    👁️ Ver
                                </a>

                                {{-- 💰 Gastos --}}
                                @if($item->tipo === 'veiculo')
                                    <a href="{{ route('veiculo.gastos.index', $item->id) }}"
                                        class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                                        💰 Gastos
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-3 text-center text-gray-500">Nenhum veículo ou frota pública encontrada.</p>
        @endforelse
    </div>
</form>


    {{-- 🔹 Paginação (padronizada com outras telas) --}}
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Página <strong>{{ $itens->currentPage() }}</strong> de <strong>{{ $itens->lastPage() }}</strong> —
            <strong>{{ $itens->total() }}</strong> registros
        </div>
        <div>{{ $itens->onEachSide(1)->links() }}</div>
    </div>
</div>

<!-- 🔹 Script de Operadores Dinâmicos -->
<script>
const operadoresPorCampo = {
    titulo: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    placa: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    ano: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Maior que (>)' },
        { valor: '<', texto: 'Menor que (<)' },
        { valor: 'between', texto: 'Entre' },
    ],
    frota_nome: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    tipoFiltro: [{ valor: '=', texto: 'Igual a (=)' }],
    relacionamentoFiltro: [{ valor: '=', texto: 'Igual a (=)' }],
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
    const vTipo = item.querySelector('.valor-tipo');
    const vRel = item.querySelector('.valor-relacionamento');

    const operadorSelecionado = operador.value;
    operador.innerHTML = '';
    (operadoresPorCampo[campo.value] || []).forEach(o => {
        const opt = document.createElement('option');
        opt.value = o.valor;
        opt.textContent = o.texto;
        operador.appendChild(opt);
    });
    if ([...operador.options].some(opt => opt.value === operadorSelecionado)) operador.value = operadorSelecionado;

    const op = operador.value;

    if (campo.value === 'ano' && op === 'between') mostrarSomente(vEntre, vTexto, vTipo, vRel);
    else if (campo.value === 'tipoFiltro') mostrarSomente(vTipo, vTexto, vEntre, vRel);
    else if (campo.value === 'relacionamentoFiltro') mostrarSomente(vRel, vTexto, vEntre, vTipo);
    else mostrarSomente(vTexto, vEntre, vTipo, vRel);
}

document.addEventListener('change', e => {
    const item = e.target.closest('.filtro-item');
    if (!item) return;
    if (e.target.classList.contains('campo') || e.target.classList.contains('operador')) atualizarLinha(item);
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
