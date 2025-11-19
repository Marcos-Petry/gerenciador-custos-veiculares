@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">🚗 Meus Veículos</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-3">
        <div id="filtros-container" class="flex flex-col gap-3">

            <!-- Primeira linha -->
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="modelo" {{ request('campo') == 'modelo' ? 'selected' : '' }}>Modelo</option>
                        <option value="placa" {{ request('campo') == 'placa' ? 'selected' : '' }}>Placa</option>
                        <option value="ano" {{ request('campo') == 'ano' ? 'selected' : '' }}>Ano</option>
                        <option value="visibilidade" {{ request('campo') == 'visibilidade' ? 'selected' : '' }}>Visibilidade</option>
                        <option value="vinculo" {{ request('campo') == 'vinculo' ? 'selected' : '' }}>Vínculo</option>
                    </select>
                </div>

                <!-- Operador -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador"
                            class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"
                            data-old="{{ request('operador') }}">
                    </select>
                </div>

                <!-- Valor -->
                <div class="valor-container flex items-end gap-2">

                    <!-- Valor texto -->
                    <div class="valor-texto">
                        <label class="block text-white text-sm font-semibold mb-1">Valor</label>
                        <input type="text" name="valor" value="{{ request('valor') }}"
                               class="rounded-lg border-gray-300 px-3 py-1.5 w-80"
                               placeholder="Digite o valor">
                    </div>

                    <!-- Valor ENTRE (Ano) -->
                    <div class="valor-entre hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Entre</label>
                        <div class="flex gap-2">
                            <input type="number" name="valor_de" value="{{ request('valor_de') }}"
                                   class="rounded-lg border-gray-300 px-3 py-1.5 w-28"
                                   placeholder="De">

                            <input type="number" name="valor_ate" value="{{ request('valor_ate') }}"
                                   class="rounded-lg border-gray-300 px-3 py-1.5 w-28"
                                   placeholder="Até">
                        </div>
                    </div>

                    <!-- Valor visibilidade -->
                    <div class="valor-visibilidade hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Visibilidade</label>
                        <select name="valor_visibilidade"
                                class="rounded-lg border-gray-300 px-3 py-1.5 w-44"
                                {{ request('campo') == 'visibilidade' ? '' : 'disabled' }}>
                            <option value="">Selecione</option>
                            <option value="1" {{ request('valor_visibilidade') == '1' ? 'selected' : '' }}>Público</option>
                            <option value="0" {{ request('valor_visibilidade') == '0' ? 'selected' : '' }}>Privado</option>
                        </select>
                    </div>

                    <!-- Valor vínculo -->
                    <div class="valor-vinculo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Vínculo</label>
                        <select name="valor_vinculo"
                                class="rounded-lg border-gray-300 px-3 py-1.5 w-44"
                                {{ request('campo') == 'vinculo' ? '' : 'disabled' }}>
                            <option value="">Selecione</option>
                            <option value="dono" {{ request('valor_vinculo') == 'dono' ? 'selected' : '' }}>Sou dono</option>
                            <option value="responsavel" {{ request('valor_vinculo') == 'responsavel' ? 'selected' : '' }}>Sou responsável</option>
                        </select>
                    </div>
                </div>

                <!-- Botões principais -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                        class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filtrar
                    </button>

                    <a href="{{ route('veiculo.index') }}"
                       class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                        Limpar
                    </a>
                </div>

                <!-- Remover item -->
                <button type="button"
                        class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

    <!-- 🔹 Ações -->
    <div class="flex gap-2 mb-6">
        @if($origemCampoExterno)
        <button type="submit" form="form-selecao-veiculos"
                class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            ✅ Confirmar Seleção
        </button>
        @else
        <a href="{{ route('veiculo.create') }}"
           class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            ➕ Incluir
        </a>
        @endif
    </div>

    @if($origemCampoExterno)
    <form id="form-selecao-veiculos" method="GET"
          action="{{ request('frota_id') ? route('frota.edit', request('frota_id')) : route('frota.create') }}">

        {{-- Manter filtros --}}
        @foreach(request()->except(['_token','origemCampoExterno']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    @endif

    <!-- GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($veiculos as $veiculo)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">

            @if($veiculo->foto)
            <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/'.$veiculo->foto) }}"
                     class="h-full w-auto object-cover">
            </div>
            @else
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">🚗</div>
            @endif

            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $veiculo->modelo }}</h2>
                <p class="text-sm text-gray-600">Placa: <strong>{{ $veiculo->placa }}</strong></p>
                <p class="text-sm text-gray-600">Ano: {{ $veiculo->ano }}</p>
                <p class="text-sm text-gray-600">Frota: {{ $veiculo->frota?->nome ?? '—' }}</p>

                <p class="text-sm mt-2">
                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                        {{ $veiculo->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $veiculo->visibilidade == 1 ? 'Público' : 'Privado' }}
                    </span>
                </p>

                @if($origemCampoExterno)
                <div class="mt-4 flex items-center">
                    <input type="checkbox"
                        name="veiculos[]"
                        value="{{ $veiculo->veiculo_id }}"
                        class="h-4 w-4 text-blue-600 rounded">
                    <span class="ml-2 text-sm text-gray-600">Selecionar</span>
                </div>
                @else
                <div class="flex flex-wrap justify-center mt-5 gap-x-6 gap-y-2 border-t pt-3 text-sm font-medium">
                    <a href="{{ route('veiculo.show',$veiculo) }}"
                        class="flex items-center text-blue-600 hover:text-blue-800 gap-1">
                        👁 Visualizar
                    </a>

                    @if($veiculo->usuario_dono_id === Auth::id())
                    <a href="{{ route('veiculo.edit',$veiculo) }}"
                        class="flex items-center text-yellow-600 hover:text-yellow-800 gap-1">
                        ✏ Editar
                    </a>

                    <form method="POST"
                        action="{{ route('veiculo.destroy',$veiculo) }}"
                        onsubmit="return confirm('Excluir veículo?')">
                        @csrf @method('DELETE')
                        <button class="flex items-center text-red-600 hover:text-red-800 gap-1">
                            🗑 Excluir
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('veiculo.gastos.index',$veiculo->veiculo_id) }}"
                        class="flex items-center text-green-600 hover:text-green-800 gap-1">
                        💰 Gastos
                    </a>
                </div>
                @endif

            </div>
        </div>

        @empty
        <p class="col-span-3 text-center text-gray-500">Nenhum veículo encontrado.</p>
        @endforelse
    </div>
    </form>

    <!-- Paginação -->
    <div class="mt-6 text-sm text-gray-700">
        <div class="hidden sm:flex justify-between items-center">
            <div class="bg-white/40 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 shadow-sm">
                Página <strong>{{ $veiculos->currentPage() }}</strong>
                de <strong>{{ $veiculos->lastPage() }}</strong> —
                <strong>{{ $veiculos->total() }}</strong>
            </div>

            <div>{{ $veiculos->onEachSide(1)->links() }}</div>
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
        { valor: 'between', texto: 'Entre' },
    ],
    visibilidade: [ { valor: '=', texto: 'Igual a (=)' } ],
    vinculo: [ { valor: '=', texto: 'Igual a (=)' } ],
};

// mostra só bloco específico
function mostrarSomente(mostra, ...esconde) {
    mostra.classList.remove("hidden");
    const ativo = mostra.querySelector("input,select");
    if (ativo) ativo.disabled = false;

    esconde.forEach(e => {
        e.classList.add("hidden");
        const x = e.querySelector("input,select");
        if (x) x.disabled = true;
    });
}

// lógica geral
function atualizarLinha(item) {
    const campo = item.querySelector(".campo");
    const operador = item.querySelector(".operador");

    const blocoTexto = item.querySelector(".valor-texto");
    const blocoEntre = item.querySelector(".valor-entre");
    const blocoVisib = item.querySelector(".valor-visibilidade");
    const blocoVinc  = item.querySelector(".valor-vinculo");

    const operadorAnterior = operador.value;

    operador.innerHTML = "";
    (operadoresPorCampo[campo.value] || []).forEach(o => {
        const opt = document.createElement("option");
        opt.value = o.valor;
        opt.textContent = o.texto;
        operador.appendChild(opt);
    });

    if ([...operador.options].some(opt => opt.value === operadorAnterior)) {
        operador.value = operadorAnterior;
    }

    // lógica tipo de campo
    if (campo.value === "visibilidade")
        return mostrarSomente(blocoVisib, blocoTexto, blocoEntre, blocoVinc);

    if (campo.value === "vinculo")
        return mostrarSomente(blocoVinc, blocoTexto, blocoEntre, blocoVisib);

    if (campo.value === "ano" && operador.value === "between")
        return mostrarSomente(blocoEntre, blocoTexto, blocoVisib, blocoVinc);

    mostrarSomente(blocoTexto, blocoEntre, blocoVisib, blocoVinc);
}

// inicializar primeira linha
document.querySelectorAll(".filtro-item").forEach(atualizarLinha);

// reatividade
document.addEventListener("change", e => {
    const item = e.target.closest(".filtro-item");
    if (!item) return;

    if (e.target.classList.contains("campo") ||
        e.target.classList.contains("operador"))
        atualizarLinha(item);
});

// adicionar novo filtro
document.getElementById("add-filtro").addEventListener("click", () => {
    const container = document.getElementById("filtros-container");
    const novo = container.querySelector(".filtro-item").cloneNode(true);

    novo.querySelectorAll("input").forEach(i => (i.value = ""));
    novo.querySelectorAll("select").forEach(s => (s.selectedIndex = 0));

    const botoes = novo.querySelector(".botoes-principais");
    if (botoes) botoes.remove();

    const rm = novo.querySelector(".remover-filtro");
    rm.classList.remove("hidden");
    rm.onclick = () => novo.remove();

    atualizarLinha(novo);
    container.appendChild(novo);
});
</script>

@endsection