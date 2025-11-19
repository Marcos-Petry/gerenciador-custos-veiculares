@extends('layouts.app')

@section('content')
<div class="py-4 px-6">
@php
    $somentePublico = request('from_publico') == 1;
@endphp
    <h1 class="text-2xl font-bold text-white mb-6">💰 Meus Gastos</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-4">
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="veiculo"    {{ request('campo') == 'veiculo' ? 'selected' : '' }}>Veículo</option>
                        <option value="frota"      {{ request('campo') == 'frota' ? 'selected' : '' }}>Frota</option>
                        <option value="descricao"  {{ request('campo') == 'descricao' ? 'selected' : '' }}>Descrição</option>
                        <option value="valor"      {{ request('campo') == 'valor' ? 'selected' : '' }}>Valor</option>
                        <option value="data_gasto" {{ request('campo') == 'data_gasto' ? 'selected' : '' }}>Data</option>
                        <option value="categoria"  {{ request('campo') == 'categoria' ? 'selected' : '' }}>Categoria</option>
                        <option value="usuario"    {{ request('campo') == 'usuario' ? 'selected' : '' }}>Incluído por</option>
                        <option value="vinculo"    {{ request('campo') == 'vinculo' ? 'selected' : '' }}>Vínculo</option>
                        <option value="anexos"     {{ request('campo') == 'anexos' ? 'selected' : '' }}>Anexos</option>
                    </select>
                </div>

                <!-- Operador -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador"
                            class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"
                            data-old="{{ request('operador') }}"></select>
                </div>

                <!-- Valores -->
                <div class="valor-container flex items-end gap-2">

                    <!-- Texto -->
                    <div class="valor-texto">
                        <label class="block text-white text-sm font-semibold mb-1">Valor</label>
                        <input type="text"
                               name="valor"
                               value="{{ request('valor') }}"
                               class="inp-texto rounded-lg border-gray-300 px-3 py-1.5 w-80"
                               placeholder="Digite o valor">
                    </div>

                    <!-- Entre (para valor) -->
                    <div class="valor-entre hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Entre</label>
                        <div class="flex gap-2">
                            <input type="number"
                                   name="valor_de"
                                   value="{{ request('valor_de') }}"
                                   class="inp-de rounded-lg border-gray-300 px-3 py-1.5 w-28"
                                   placeholder="De">
                            <input type="number"
                                   name="valor_ate"
                                   value="{{ request('valor_ate') }}"
                                   class="inp-ate rounded-lg border-gray-300 px-3 py-1.5 w-28"
                                   placeholder="Até">
                        </div>
                    </div>

                    <!-- Data (entre duas datas) -->
                    <div class="valor-data hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Data</label>
                        <div class="flex gap-2">
                            <input type="date"
                                   name="data_de"
                                   value="{{ request('data_de') }}"
                                   class="inp-data rounded-lg border-gray-300 px-3 py-1.5 w-52">
                            <input type="date"
                                   name="data_ate"
                                   value="{{ request('data_ate') }}"
                                   class="inp-data rounded-lg border-gray-300 px-3 py-1.5 w-52">
                        </div>
                    </div>

                    <!-- Categoria -->
                    <div class="valor-categoria hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Categoria</label>
                        <select name="categoriaFiltro"
                                class="sel-categoria rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                disabled>
                            <option value="">Selecione</option>
                            <option value="1" {{ request('categoriaFiltro') == '1' ? 'selected' : '' }}>Combustível</option>
                            <option value="2" {{ request('categoriaFiltro') == '2' ? 'selected' : '' }}>Manutenção</option>
                            <option value="3" {{ request('categoriaFiltro') == '3' ? 'selected' : '' }}>Seguro</option>
                            <option value="4" {{ request('categoriaFiltro') == '4' ? 'selected' : '' }}>Imposto</option>
                            <option value="5" {{ request('categoriaFiltro') == '5' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>

                    <!-- Frota -->
                    <div class="valor-frota hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Frota</label>
                        <select name="frotaFiltro"
                                class="sel-frota rounded-lg border-gray-300 px-3 py-1.5 w-64"
                                disabled>
                            <option value="">Selecione a frota</option>
                            @foreach($frotas as $f)
                                <option value="{{ $f->frota_id }}"
                                    {{ request('frotaFiltro') == $f->frota_id ? 'selected' : '' }}>
                                    {{ $f->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Vínculo -->
                    <div class="valor-vinculo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Vínculo</label>
                        <select name="vinculoFiltro"
                                class="sel-vinculo rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                disabled>
                            <option value="">Selecione</option>
                            <option value="dono"        {{ request('vinculoFiltro') == 'dono' ? 'selected' : '' }}>Sou dono</option>
                            <option value="responsavel" {{ request('vinculoFiltro') == 'responsavel' ? 'selected' : '' }}>Sou responsável</option>
                        </select>
                    </div>

                    <!-- Usuário -->
                    <div class="valor-usuario hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Incluído por</label>
                        <input type="text"
                               name="usuarioFiltro"
                               value="{{ request('usuarioFiltro') }}"
                               class="inp-usuario rounded-lg border-gray-300 px-3 py-1.5 w-72"
                               placeholder="Nome do usuário"
                               disabled>
                    </div>

                    <!-- Anexos -->
                    <div class="valor-anexo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Anexos</label>
                        <select name="anexoFiltro"
                                class="sel-anexo rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                disabled>
                            <option value="">Selecione</option>
                            <option value="com" {{ request('anexoFiltro') == 'com' ? 'selected' : '' }}>Com anexos</option>
                            <option value="sem" {{ request('anexoFiltro') == 'sem' ? 'selected' : '' }}>Sem anexos</option>
                        </select>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                        class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>

                    <a href="{{ route('gastos.index') }}"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
                </div>

                <button type="button"
                    class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

<!-- 🔹 Ações -->
<div class="flex flex-wrap gap-2 mb-4 mt-4 sm:mt-0">

    @if(!$somentePublico)
        <a href="{{ route('gastos.create') }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
           ➕ Incluir
        </a>

        <button id="btnEditar"
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg disabled:opacity-50"
                disabled>✏️ Editar</button>

        <button id="btnExcluir"
                class="px-4 py-2 bg-red-600 text-white rounded-lg disabled:opacity-50"
                disabled>🗑️ Excluir</button>
    @endif

    {{-- 👁 Sempre disponível --}}
    <button id="btnVer"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50"
            disabled>👁️ Visualizar</button>

</div>

    <!-- 🔹 Tabela -->
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <div class="overflow-x-auto max-sm:overflow-x-scroll">
        <div class="overflow-x-auto max-w-full">
        <table class="border-collapse text-sm max-sm:text-xs w-max sm:w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Veículo</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Frota</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Categoria</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Descrição</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Valor</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Data</th>
                    <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Incluído por</th>
                    <th class="px-4 py-3 max-sm:px-1 max-sm:py-2 text-center font-semibold w-16">Anexos</th>
                </tr>
            </thead>

            <tbody>
                @forelse($gastos as $gasto)
                <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer"
                    data-id="{{ $gasto->gasto_id }}">

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ $gasto->veiculo->frota->nome ?? '—' }}
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ $gasto->categoriaTexto() }}
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ $gasto->descricao ?? '—' }}
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        R$ {{ number_format($gasto->valor, 2, ',', '.') }}
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}
                    </td>

                    <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                        {{ $gasto->usuario->name ?? 'Desconhecido' }}
                    </td>

                    <td class="px-4 py-3 max-sm:px-1 max-sm:py-2 text-center">
                        @if($gasto->anexos->count() > 0) 📎 @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">
                        Nenhum gasto encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

    </div>
</div>

<!-- Paginação -->
<div class="mt-6 text-sm text-gray-700">
    {{-- ... (toda a parte de paginação igual a sua, não mexi) --}}
    {{-- mantive exatamente o mesmo conteúdo que você já tinha aqui --}}
    {{-- ... --}}
    <div class="hidden sm:flex justify-between items-center">

        <div class="bg-white/40 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 shadow-sm">
            Página <strong>{{ $gastos->currentPage() }}</strong>
            de <strong>{{ $gastos->lastPage() }}</strong> — 
            <strong>{{ $gastos->total() }}</strong> registros
        </div>

        <div>
            {{ $gastos->onEachSide(1)->links() }}
        </div>

    </div>

</div>

</div>

<script>
const operadoresPorCampo = {
    veiculo:   [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    frota:     [{ valor: '=', texto: 'Igual a (=)' }],
    descricao: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
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
    usuario:   [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    vinculo:   [{ valor: '=', texto: 'Igual a (=)' }],
    anexos:    [{ valor: '=', texto: 'Igual a (=)' }],
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
    const campo    = item.querySelector('.campo');
    const operador = item.querySelector('.operador');

    const vTexto = item.querySelector('.valor-texto');
    const vEntre = item.querySelector('.valor-entre');
    const vData  = item.querySelector('.valor-data');
    const vCat   = item.querySelector('.valor-categoria');
    const vUsu   = item.querySelector('.valor-usuario');
    const vVin   = item.querySelector('.valor-vinculo');
    const vFro   = item.querySelector('.valor-frota');
    const vAnx   = item.querySelector('.valor-anexo');

    const inputTexto = vTexto.querySelector('input'); // o "Valor"

    // --- monta operadores mantendo o valor antigo ---
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

    // primeira vez, aplica o data-old vindo do request
    if (operador.dataset.old && !item.dataset.iniciado) {
        operador.value = operador.dataset.old;
    }
    item.dataset.iniciado = true;

    const op = operador.value;

    // reseta input de texto para padrão (casos não data)
    if (inputTexto) {
        inputTexto.type = 'text';
        inputTexto.placeholder = 'Digite o valor';
    }

    // =========================
    // REGRAS POR CAMPO
    // =========================
    if (campo.value === 'frota') {
        mostrarSomente(vFro, vTexto, vEntre, vData, vCat, vUsu, vVin, vAnx);

    } else if (campo.value === 'anexos') {
        mostrarSomente(vAnx, vTexto, vEntre, vData, vCat, vUsu, vVin, vFro);

    } else if (campo.value === 'valor' && op === 'between') {
        // Valor numérico entre
        mostrarSomente(vEntre, vTexto, vData, vCat, vUsu, vVin, vFro, vAnx);

    } else if (campo.value === 'data_gasto' && op === 'between') {
        // Data ENTRE duas datas -> usa bloco vData (data_de / data_ate)
        mostrarSomente(vData, vTexto, vEntre, vCat, vUsu, vVin, vFro, vAnx);

    } else if (campo.value === 'data_gasto') {
        // Data com operador simples (=, >, <) -> único campo tipo date chamado "valor"
        if (inputTexto) {
            inputTexto.type = 'date';
            inputTexto.placeholder = '';
        }
        mostrarSomente(vTexto, vEntre, vData, vCat, vUsu, vVin, vFro, vAnx);

    } else if (campo.value === 'categoria') {
        mostrarSomente(vCat, vTexto, vEntre, vData, vUsu, vVin, vFro, vAnx);

    } else if (campo.value === 'usuario') {
        mostrarSomente(vUsu, vTexto, vEntre, vData, vCat, vVin, vFro, vAnx);

    } else if (campo.value === 'vinculo') {
        mostrarSomente(vVin, vTexto, vEntre, vData, vCat, vUsu, vFro, vAnx);

    } else {
        // padrão: usa texto
        mostrarSomente(vTexto, vEntre, vData, vCat, vUsu, vVin, vFro, vAnx);
    }
}

// eventos
document.addEventListener('change', e => {
    const item = e.target.closest('.filtro-item');
    if (!item) return;
    if (e.target.classList.contains('campo') || e.target.classList.contains('operador')) {
        atualizarLinha(item);
    }
});

// botão de adicionar filtro
document.getElementById('add-filtro').addEventListener('click', () => {
    const container = document.getElementById('filtros-container');
    const original  = container.querySelector('.filtro-item');
    const clone     = original.cloneNode(true);

    // limpa inputs
    clone.querySelectorAll('input').forEach(i => {
        i.value = '';
        i.disabled = false;
    });

    // reseta somente o campo "campo"
    const campoSelect = clone.querySelector('.campo');
    if (campoSelect) campoSelect.selectedIndex = 0;

    // limpa operadores antigos
    const operadorSelect = clone.querySelector('.operador');
    if (operadorSelect) operadorSelect.dataset.old = '';

    const botoes = clone.querySelector('.botoes-principais');
    if (botoes) botoes.remove();

    const rm = clone.querySelector('.remover-filtro');
    rm.classList.remove('hidden');
    rm.onclick = () => clone.remove();

    clone.dataset.iniciado = false;
    atualizarLinha(clone);

    container.appendChild(clone);
});

// inicializa a primeira linha com valores do request
document.querySelectorAll('.filtro-item').forEach(atualizarLinha);

// ===== Seleção da linha + botões (mantido igual) =====
let linhaSelecionada = null;
let idSelecionado = null;

document.querySelectorAll('.linha-gasto').forEach(linha => {
    linha.addEventListener('click', () => {
        if (linhaSelecionada)
            linhaSelecionada.classList.remove('bg-blue-100');

        linhaSelecionada = linha;
        linhaSelecionada.classList.add('bg-blue-100');

        idSelecionado = linha.getAttribute('data-id');

        document.getElementById('btnVer').disabled = false;

        @if(!$somentePublico)
            document.getElementById('btnEditar').disabled = false;
            document.getElementById('btnExcluir').disabled = false;
        @endif
    });
});

document.getElementById('btnVer').addEventListener('click', () => {
    if (idSelecionado)
        window.location.href = @json(route('gastos.show', ':id')).replace(':id', idSelecionado);
});

@if(!$somentePublico)
document.getElementById('btnEditar').addEventListener('click', () => {
    if (idSelecionado)
        window.location.href = @json(route('gastos.edit', ':id')).replace(':id', idSelecionado);
});

document.getElementById('btnExcluir').addEventListener('click', () => {
    if (!idSelecionado) return;

    if (confirm('Tem certeza que deseja excluir este gasto?')) {
        fetch(@json(route('gastos.destroy', ':id')).replace(':id', idSelecionado), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).then(r =>
            r.ok ? location.reload() : alert('Erro ao excluir gasto.')
        );
    }
});
@endif
</script>

@endsection
