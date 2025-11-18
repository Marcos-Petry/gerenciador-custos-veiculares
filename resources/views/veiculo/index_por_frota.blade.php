@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
@php
    $somentePublico = request('from_publico') == 1;
@endphp

    <!-- 🔙 Botão Voltar -->
<div class="mb-4">
    <a href="{{ $somentePublico
        ? route('frota.show', ['frota' => $frota->frota_id, 'from_publico' => 1])
        : route('frota.show', $frota->frota_id) }}"
        
        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
        ← Voltar à Frota
    </a>
</div>


    <!-- 🔹 Título -->
    <h1 class="text-2xl font-bold text-white mb-6">🚗 Veículos da Frota: {{ $frota->nome }}</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-3">
        <div id="filtros-container" class="flex flex-col gap-3">

            <!-- Primeira linha -->
            <div class="filtro-item flex flex-wrap items-end gap-3">
                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="modelo">Modelo</option>
                        <option value="placa">Placa</option>
                        <option value="ano">Ano</option>
                        <option value="visibilidade">Visibilidade</option>
                        <option value="vinculo">Vínculo</option>
                    </select>
                </div>

                <!-- Operador -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador" class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"></select>
                </div>

                <!-- Valor -->
                <div class="valor-container flex items-end gap-2">
                    <!-- Texto -->
                    <div class="valor-texto">
                        <label class="block text-white text-sm font-semibold mb-1">Valor</label>
                        <input type="text" name="valor[]" placeholder="Digite o valor"
                            class="rounded-lg border-gray-300 px-3 py-1.5 w-80">
                    </div>

                    <!-- Visibilidade -->
                    <div class="valor-visibilidade hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Visibilidade</label>
                        <select name="valor[]" class="rounded-lg border-gray-300 px-3 py-1.5 w-44" disabled>
                            <option value="">Selecione</option>
                            <option value="1">Público</option>
                            <option value="0">Privado</option>
                        </select>
                    </div>

                    <!-- Vínculo -->
                    <div class="valor-vinculo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Vínculo</label>
                        <select name="valor[]" class="rounded-lg border-gray-300 px-3 py-1.5 w-44" disabled>
                            <option value="">Selecione</option>
                            <option value="dono">Sou dono</option>
                            <option value="responsavel">Sou responsável</option>
                        </select>
                    </div>
                </div>

                <!-- Botões principais -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                        class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">
                        +
                    </button>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filtrar
                    </button>

                    <a href="{{ route('frota.veiculos.index', $frota->frota_id) }}"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                        Limpar
                    </a>
                </div>

                <!-- Remover -->
                <button type="button"
                    class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

    <!-- 🔹 Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($veiculos as $veiculo)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
            @if($veiculo->foto)
            <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/' . $veiculo->foto) }}" class="h-full w-auto object-cover" alt="Foto do veículo">
            </div>
            @else
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">🚗</div>
            @endif

            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $veiculo->modelo }}</h2>
                <p class="text-sm text-gray-600">Placa: <strong>{{ $veiculo->placa }}</strong></p>
                <p class="text-sm text-gray-600">Ano: {{ $veiculo->ano }}</p>

                <p class="text-sm mt-2">
                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium {{ $veiculo->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $veiculo->getVisibilidade() }}
                    </span>
                </p>

                <div class="flex justify-center mt-5 border-t pt-3">
                    <a href="{{ route('veiculo.show', ['veiculo' => $veiculo->veiculo_id, 'from_frota' => $frota->frota_id]) }}"
                        class="flex items-center text-blue-600 hover:text-blue-800 transition gap-1 font-medium">
                        👁 <span>Ver Detalhes</span>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-3 text-center text-gray-500">Nenhum veículo encontrado nesta frota.</p>
        @endforelse
    </div>

<!-- Paginação -->
<div class="mt-6 text-sm text-gray-700">

    {{-- 📌 Mobile (até 640px) --}}
    <div class="flex flex-col items-center gap-3 sm:hidden">

        <div class="bg-white/40 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 shadow-sm text-center">
            Página <strong>{{ $veiculos->currentPage() }}</strong>
            de <strong>{{ $veiculos->lastPage() }}</strong> —
            <strong>{{ $veiculos->total() }}</strong> registros
        </div>

        <div class="w-full flex justify-center overflow-x-auto">
            <div class="inline-flex gap-1 px-2 pb-1">

                {{-- Anterior --}}
                @if ($veiculos->onFirstPage())
                    <span class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                        «
                    </span>
                @else
                    <a href="{{ $veiculos->previousPageUrl() }}"
                       class="px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">
                        «
                    </a>
                @endif

                {{-- Compact pages --}}
                @php
                    $total   = $veiculos->lastPage();
                    $current = $veiculos->currentPage();
                    $range   = 2;
                    $inicio  = max(1, $current - $range);
                    $fim     = min($total, $current + $range);
                @endphp

                {{-- Primeira página --}}
                @if ($inicio > 1)
                    <a href="{{ $veiculos->url(1) }}"
                       class="px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">1</a>

                    @if ($inicio > 2)
                        <span class="px-3 py-1">…</span>
                    @endif
                @endif

                {{-- Intervalo central --}}
                @for ($i = $inicio; $i <= $fim; $i++)
                    @if ($i == $current)
                        <span class="px-3 py-1 rounded-lg border border-blue-500 bg-blue-500 text-white font-semibold">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $veiculos->url($i) }}"
                           class="px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- Última página --}}
                @if ($fim < $total)
                    @if ($fim < $total - 1)
                        <span class="px-3 py-1">…</span>
                    @endif

                    <a href="{{ $veiculos->url($total) }}"
                       class="px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">
                        {{ $total }}
                    </a>
                @endif

                {{-- Próxima --}}
                @if ($veiculos->hasMorePages())
                    <a href="{{ $veiculos->nextPageUrl() }}"
                       class="px-3 py-1 rounded-lg border border-gray-300 bg-white hover:bg-gray-50">
                        »
                    </a>
                @else
                    <span class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                        »
                    </span>
                @endif

            </div>
        </div>

    </div>

    {{-- 📌 Desktop (a partir de 640px) --}}
    <div class="hidden sm:flex justify-between items-center">

        <div class="bg-white/40 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 shadow-sm">
            Página <strong>{{ $veiculos->currentPage() }}</strong>
            de <strong>{{ $veiculos->lastPage() }}</strong> —
            <strong>{{ $veiculos->total() }}</strong> registros
        </div>

        <div>
            {{ $veiculos->onEachSide(1)->links() }}
        </div>
    </div>

</div>

</div>

<!-- 🔸 Script -->
<script>
const operadoresPorCampo = {
    modelo: [
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
    ],
    visibilidade: [{ valor: '=', texto: 'Igual a (=)' }],
    vinculo: [{ valor: '=', texto: 'Igual a (=)' }],
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
    const op = item.querySelector('.operador');
    const vTexto = item.querySelector('.valor-texto');
    const vVisib = item.querySelector('.valor-visibilidade');
    const vVinc  = item.querySelector('.valor-vinculo');

    op.innerHTML = '';
    (operadoresPorCampo[campo.value] || []).forEach(o => {
        const opt = document.createElement('option');
        opt.value = o.valor;
        opt.textContent = o.texto;
        op.appendChild(opt);
    });

    if (campo.value === 'visibilidade') {
        mostrarSomente(vVisib, vTexto, vVinc);
    } else if (campo.value === 'vinculo') {
        mostrarSomente(vVinc, vTexto, vVisib);
    } else {
        mostrarSomente(vTexto, vVisib, vVinc);
    }
}

document.querySelectorAll('.filtro-item').forEach(atualizarLinha);
document.addEventListener('change', e => {
    if (e.target.classList.contains('campo'))
        atualizarLinha(e.target.closest('.filtro-item'));
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
</script>
@endsection
