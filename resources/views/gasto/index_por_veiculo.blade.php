@extends('layouts.app')

@section('content')
<div class="py-4 px-6">
    <!-- 🔙 Botão Voltar -->
    <div class="mb-4">
        <a href="{{ route('veiculo.index') }}"
           class="inline-flex items-center gap-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
           ← Voltar
        </a>
    </div>

    <!-- 🔹 Cabeçalho -->
    <h2 class="text-2xl font-bold text-white mb-4">💰 Gastos do Veículo {{ $veiculo->modelo }} ({{ $veiculo->placa }})</h2>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-4">
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo[]" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="descricao">Descrição</option>
                        <option value="valor">Valor</option>
                        <option value="data_gasto">Data</option>
                        <option value="categoria">Categoria</option>
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

                    <!-- Data -->
                    <div class="valor-data hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Data</label>
                        <input type="date" class="inp-data rounded-lg border-gray-300 px-3 py-1.5 w-52">
                    </div>

                    <!-- Categoria -->
                    <div class="valor-categoria hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Categoria</label>
                        <select class="sel-categoria rounded-lg border-gray-300 px-3 py-1.5 w-52" disabled>
                            <option value="">Selecione</option>
                            <option value="1">Combustível</option>
                            <option value="2">Manutenção</option>
                            <option value="3">Seguro</option>
                            <option value="4">Imposto</option>
                            <option value="5">Outros</option>
                        </select>
                    </div>
                </div>

                <!-- Botões principais -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                            class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>

                    <a href="{{ route('veiculo.gastos.index', $veiculo->veiculo_id) }}"
                       class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
                </div>

                <!-- Remover -->
                <button type="button"
                        class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

    <!-- 🔹 Ações -->
    <div class="flex gap-2 mb-3">
        <a href="{{ route('veiculo.gastos.create', $veiculo->veiculo_id) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">➕ Adicionar</a>

        <button id="btnEditar" class="px-4 py-2 bg-yellow-500 text-white rounded-lg disabled:opacity-50" disabled>✏️ Editar</button>
        <button id="btnVer" class="px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50" disabled>👁️ Visualizar</button>
        <button id="btnExcluir" class="px-4 py-2 bg-red-600 text-white rounded-lg disabled:opacity-50" disabled>🗑️ Excluir</button>

        <a href="{{ route('veiculo.gastos.linha-tempo', $veiculo->veiculo_id) }}"
           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">🕒 Linha do Tempo</a>
    </div>

    <!-- 🔹 Tabela -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left font-semibold">Categoria</th>
                    <th class="px-6 py-3 text-left font-semibold">Descrição</th>
                    <th class="px-6 py-3 text-left font-semibold">Valor</th>
                    <th class="px-6 py-3 text-left font-semibold">Data</th>
                    <th class="px-4 py-3 text-center font-semibold w-16">Anexos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer" data-id="{{ $gasto->gasto_id }}">
                    <td class="px-6 py-3">{{ $gasto->categoriaTexto() }}</td>
                    <td class="px-6 py-3">{{ $gasto->descricao ?? '—' }}</td>
                    <td class="px-6 py-3">R$ {{ number_format($gasto->valor, 2, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">@if($gasto->anexos->count() > 0)📎@endif</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">Nenhum gasto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 🔹 Total / Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $gastos->count() }}</strong> de <strong>{{ $gastos->total() }}</strong> registros
        </div>
        <div>{{ $gastos->onEachSide(1)->links() }}</div>
    </div>
</div>

<!-- 🔹 Script -->
<script>
const operadoresPorCampo = {
    descricao: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
        { valor: 'starts', texto: 'Começa com' },
        { valor: 'ends', texto: 'Termina com' },
    ],
    valor: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Maior que (>)' },
        { valor: '<', texto: 'Menor que (<)' },
        { valor: 'between', texto: 'Entre' },
    ],
    data_gasto: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Após (>)' },
        { valor: '<', texto: 'Antes (<)' },
        { valor: 'between', texto: 'Entre' },
    ],
    categoria: [{ valor: '=', texto: 'Igual a (=)' }],
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
    const vData = item.querySelector('.valor-data');
    const vCat = item.querySelector('.valor-categoria');

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

    if (campo.value === 'data_gasto' && opValue === 'between') {
        const label = vData.querySelector('label');
        label.textContent = 'Entre';
        const inputExistente = vData.querySelector('.inp-data');
        const container = vData.querySelector('.data-range');

        if (!container) {
            const div = document.createElement('div');
            div.classList.add('flex', 'gap-2', 'data-range');
            div.innerHTML = `
                <input type="date" class="inp-data-de rounded-lg border-gray-300 px-3 py-1.5 w-40">
                <input type="date" class="inp-data-ate rounded-lg border-gray-300 px-3 py-1.5 w-40">
            `;
            inputExistente.replaceWith(div);
        }
        mostrarSomente(vData, vTexto, vEntre, vCat);
    }
    else if (campo.value === 'data_gasto') {
        const label = vData.querySelector('label');
        label.textContent = 'Data';
        if (!vData.querySelector('.inp-data')) {
            vData.querySelector('.data-range')?.remove();
            const input = document.createElement('input');
            input.type = 'date';
            input.classList.add('inp-data', 'rounded-lg', 'border-gray-300', 'px-3', 'py-1.5', 'w-52');
            vData.appendChild(input);
        }
        mostrarSomente(vData, vTexto, vEntre, vCat);
    }
    else if (campo.value === 'categoria') {
        mostrarSomente(vCat, vTexto, vEntre, vData);
    }
    else if (campo.value === 'valor' && opValue === 'between') {
        mostrarSomente(vEntre, vTexto, vData, vCat);
    }
    else {
        mostrarSomente(vTexto, vEntre, vData, vCat);
    }
}

// Eventos
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
