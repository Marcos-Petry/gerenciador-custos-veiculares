@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
@php
    $somentePublico = request('from_publico') == 1;
@endphp
    <!-- 🔙 Voltar -->
    <div class="mb-4">
        <a href="{{ $somentePublico
            ? route('publico.index')
            : route('veiculo.index') }}"
        
           class="inline-flex items-center gap-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
           ← Voltar
        </a>
    </div>

    <!-- Título -->
    <h2 class="text-2xl font-bold text-white mb-6">
        💰 Gastos do Veículo {{ $veiculo->modelo }} ({{ $veiculo->placa }})
    </h2>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-4">
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="descricao"  {{ request('campo') == 'descricao' ? 'selected' : '' }}>Descrição</option>
                        <option value="valor"      {{ request('campo') == 'valor' ? 'selected' : '' }}>Valor</option>
                        <option value="data_gasto" {{ request('campo') == 'data_gasto' ? 'selected' : '' }}>Data</option>
                        <option value="categoria"  {{ request('campo') == 'categoria' ? 'selected' : '' }}>Categoria</option>
                        <option value="usuario"    {{ request('campo') == 'usuario' ? 'selected' : '' }}>Incluído por</option>
                        <option value="vinculo"    {{ request('campo') == 'vinculo' ? 'selected' : '' }}>Vínculo</option>
                        <option value="anexos"     {{ request('campo') == 'anexos' ? 'selected' : '' }}>Anexos</option>
                    </select>
                </div>

                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Operador</label>
                    <select name="operador"
                            class="operador rounded-lg border-gray-300 px-3 py-1.5 w-48"
                            data-old="{{ request('operador') }}"></select>
                </div>

                <div class="valor-container flex items-end gap-2">

                    <div class="valor-texto">
                        <label class="block text-white text-sm font-semibold mb-1">Valor</label>
                        <input type="text"
                               name="valor"
                               value="{{ request('valor') }}"
                               class="inp-texto rounded-lg border-gray-300 px-3 py-1.5 w-80"
                               placeholder="Digite o valor">
                    </div>

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

                    <div class="valor-data hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Data</label>
                        <input type="date"
                               name="data_gasto"
                               value="{{ request('data_gasto') }}"
                               class="inp-data rounded-lg border-gray-300 px-3 py-1.5 w-52">
                    </div>

                    <div class="valor-categoria {{ request('campo') == 'categoria' ? '' : 'hidden' }}">
                        <label class="block text-white text-sm font-semibold mb-1">Categoria</label>
                        <select name="categoriaFiltro"
                                class="sel-categoria rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                {{ request('campo') == 'categoria' ? '' : 'disabled' }}>
                            <option value="">Selecione</option>
                            <option value="1" {{ request('categoriaFiltro') == '1' ? 'selected' : '' }}>Combustível</option>
                            <option value="2" {{ request('categoriaFiltro') == '2' ? 'selected' : '' }}>Manutenção</option>
                            <option value="3" {{ request('categoriaFiltro') == '3' ? 'selected' : '' }}>Seguro</option>
                            <option value="4" {{ request('categoriaFiltro') == '4' ? 'selected' : '' }}>Imposto</option>
                            <option value="5" {{ request('categoriaFiltro') == '5' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>

                    <div class="valor-usuario {{ request('campo') == 'usuario' ? '' : 'hidden' }}">
                        <label class="block text-white text-sm font-semibold mb-1">Incluído por</label>
                        <input type="text"
                               name="usuarioFiltro"
                               value="{{ request('usuarioFiltro') }}"
                               class="inp-usuario rounded-lg border-gray-300 px-3 py-1.5 w-64"
                               placeholder="Nome do usuário"
                               {{ request('campo') == 'usuario' ? '' : 'disabled' }}>
                    </div>

                    <div class="valor-vinculo {{ request('campo') == 'vinculo' ? '' : 'hidden' }}">
                        <label class="block text-white text-sm font-semibold mb-1">Vínculo</label>
                        <select name="vinculoFiltro"
                                class="sel-vinculo rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                {{ request('campo') == 'vinculo' ? '' : 'disabled' }}>
                            <option value="">Selecione</option>
                            <option value="dono"        {{ request('vinculoFiltro') == 'dono' ? 'selected' : '' }}>Sou dono</option>
                            <option value="responsavel" {{ request('vinculoFiltro') == 'responsavel' ? 'selected' : '' }}>Sou responsável</option>
                        </select>
                    </div>

                    <div class="valor-anexo {{ request('campo') == 'anexos' ? '' : 'hidden' }}">
                        <label class="block text-white text-sm font-semibold mb-1">Anexos</label>
                        <select name="anexoFiltro"
                                class="sel-anexo rounded-lg border-gray-300 px-3 py-1.5 w-52"
                                {{ request('campo') == 'anexos' ? '' : 'disabled' }}>
                            <option value="">Selecione</option>
                            <option value="com" {{ request('anexoFiltro') == 'com' ? 'selected' : '' }}>Com anexos</option>
                            <option value="sem" {{ request('anexoFiltro') == 'sem' ? 'selected' : '' }}>Sem anexos</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                        class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>

                    <a href="{{ route('veiculo.gastos.index', $veiculo->veiculo_id) }}"
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
            <a href="{{ route('veiculo.gastos.create', $veiculo->veiculo_id) }}"
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

        {{-- Sempre permitidos --}}
        <button id="btnVer"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50"
                disabled>👁️ Visualizar</button>

        <a href="{{ route('veiculo.gastos.linha-tempo', $veiculo->veiculo_id) }}"
           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
           🕒 Linha do Tempo
        </a>
    </div>

    <!-- 🔹 Tabela -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto max-sm:overflow-x-scroll">
            <div class="overflow-x-auto max-w-full">
            <table class="border-collapse text-sm max-sm:text-xs w-max sm:w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Categoria</th>
                        <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Descrição</th>
                        <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Valor</th>
                        <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Data</th>
                        <th class="px-6 py-3 max-sm:px-2 max-sm:py-2 text-left font-semibold">Incluído por</th>
                        <th class="px-4 py-3 max-sm:px-1 max-sm:py-2 text-center font-semibold w-16">Anexos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gastos as $gasto)
                    <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer max-sm:text-xs"
                        data-id="{{ $gasto->gasto_id }}">
                        <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">{{ $gasto->categoriaTexto() }}</td>
                        <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">{{ $gasto->descricao ?? '—' }}</td>
                        <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                            R$ {{ number_format($gasto->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">
                            {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3 max-sm:px-2 max-sm:py-2">{{ $gasto->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-3 max-sm:px-1 max-sm:py-2 text-center">
                            @if($gasto->anexos->count() > 0) 📎 @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <!-- Paginação -->
    <div class="mt-6 text-sm text-gray-700">

        {{-- 📌 Mobile (até 640px) --}}
        <div class="flex flex-col items-center gap-3 sm:hidden">

            <div class="bg-white/40 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 shadow-sm text-center">
                Página <strong>{{ $gastos->currentPage() }}</strong>
                de <strong>{{ $gastos->lastPage() }}</strong> —
                <strong>{{ $gastos->total() }}</strong> registros
            </div>

            <div class="w-full flex justify-center overflow-x-auto">
                <div class="inline-flex gap-1 px-2 pb-1">

                    {{-- Anterior --}}
                    @if ($gastos->onFirstPage())
                        <span class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                            «
                        </span>
                    @else
                        <a href="{{ $gastos->previousPageUrl() }}"
                           class="px-3 py-1 rounded-lg border border-gray-300 bg-white hover:bg-gray-50">
                            «
                        </a>
                    @endif

                    {{-- Páginas compactas com ellipsis --}}
                    @php
                        $total   = $gastos->lastPage();
                        $current = $gastos->currentPage();
                        $range   = 2;
                        $inicio  = max(1, $current - $range);
                        $fim     = min($total, $current + $range);
                    @endphp

                    {{-- Primeira página --}}
                    @if ($inicio > 1)
                        <a href="{{ $gastos->url(1) }}"
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
                            <a href="{{ $gastos->url($i) }}"
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

                        <a href="{{ $gastos->url($total) }}"
                           class="px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">
                            {{ $total }}
                        </a>
                    @endif

                    {{-- Próxima --}}
                    @if ($gastos->hasMorePages())
                        <a href="{{ $gastos->nextPageUrl() }}"
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

<!-- Script -->
<script>
const operadoresPorCampo = {
    descricao: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    valor:     [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Maior que (>)' },
        { valor: '<', texto: 'Menor que (<)' },
        { valor: 'between', texto: 'Entre' }
    ],
    data_gasto:[
        { valor: '=', texto: 'Igual a (=)' },
        { valor: '>', texto: 'Após (>)' },
        { valor: '<', texto: 'Antes (<)' }
        // por enquanto SEM between, como você pediu
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
    const vTexto   = item.querySelector('.valor-texto');
    const vEntre   = item.querySelector('.valor-entre');
    const vData    = item.querySelector('.valor-data');
    const vCat     = item.querySelector('.valor-categoria');
    const vUsu     = item.querySelector('.valor-usuario');
    const vVin     = item.querySelector('.valor-vinculo');
    const vAnx     = item.querySelector('.valor-anexo');

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

    // mantém operador da request na primeira carga
    if (operador.dataset.old && !item.dataset.iniciado) {
        operador.value = operador.dataset.old;
    }
    item.dataset.iniciado = true;

    const opValue = operador.value;

    if (campo.value === 'data_gasto')
        mostrarSomente(vData, vTexto, vEntre, vCat, vUsu, vVin, vAnx);
    else if (campo.value === 'categoria')
        mostrarSomente(vCat, vTexto, vEntre, vData, vUsu, vVin, vAnx);
    else if (campo.value === 'valor' && opValue === 'between')
        mostrarSomente(vEntre, vTexto, vData, vCat, vUsu, vVin, vAnx);
    else if (campo.value === 'usuario')
        mostrarSomente(vUsu, vTexto, vEntre, vData, vCat, vVin, vAnx);
    else if (campo.value === 'vinculo')
        mostrarSomente(vVin, vTexto, vEntre, vData, vCat, vUsu, vAnx);
    else if (campo.value === 'anexos')
        mostrarSomente(vAnx, vTexto, vEntre, vData, vCat, vUsu, vVin);
    else
        mostrarSomente(vTexto, vEntre, vData, vCat, vUsu, vVin, vAnx);
}

document.addEventListener('change', e => {
    const item = e.target.closest('.filtro-item');
    if (!item) return;
    if (e.target.classList.contains('campo') || e.target.classList.contains('operador')) {
        atualizarLinha(item);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.filtro-item').forEach(atualizarLinha);
});

document.getElementById('add-filtro').addEventListener('click', () => {
    const container = document.getElementById('filtros-container');
    const original  = container.querySelector('.filtro-item');
    const clone     = original.cloneNode(true);

    // limpa somente inputs e reabilita
    clone.querySelectorAll('input').forEach(i => {
        i.value = '';
        i.disabled = false;
    });

    // reseta apenas o select "campo"
    const campoSelect = clone.querySelector('.campo');
    if (campoSelect) campoSelect.selectedIndex = 0;

    // evita reaproveitar operador/tipo do filtro original
    clone.querySelectorAll('select').forEach(s => {
        if (s !== campoSelect) s.dataset.old = "";
    });

    const botoes = clone.querySelector('.botoes-principais');
    if (botoes) botoes.remove();

    const rm = clone.querySelector('.remover-filtro');
    rm.classList.remove('hidden');
    rm.onclick = () => clone.remove();

    clone.dataset.iniciado = false;
    atualizarLinha(clone);

    container.appendChild(clone);
});

// seleção e botões CRUD
let idSelecionado = null, linhaSelecionada = null;

const btnEditar  = document.getElementById('btnEditar');
const btnVer     = document.getElementById('btnVer');
const btnExcluir = document.getElementById('btnExcluir');

document.querySelectorAll('.linha-gasto').forEach(linha => {
    linha.addEventListener('click', () => {

        if (linhaSelecionada)
            linhaSelecionada.classList.remove('bg-blue-100');

        linhaSelecionada = linha;
        linha.classList.add('bg-blue-100');

        idSelecionado = linha.getAttribute('data-id');

        // 👁️ Ver sempre habilita
        btnVer.disabled = false;

        // 🔐 Se NÃO for consulta pública, habilita os outros
        @if(!$somentePublico)
            btnEditar.disabled = false;
            btnExcluir.disabled = false;
        @endif
    });
});

// 👁️ Visualizar (sempre permitido)
btnVer.addEventListener('click', () => {
    if (idSelecionado)
        window.location.href = `{{ route('gastos.show', ':id') }}`.replace(':id', idSelecionado);
});

// ✏️ Editar (somente se NÃO for público)
@if(!$somentePublico)
btnEditar.addEventListener('click', () => {
    if (idSelecionado)
        window.location.href = `{{ route('gastos.edit', ':id') }}`.replace(':id', idSelecionado);
});
@endif

// 🗑️ Excluir (somente se NÃO for público)
@if(!$somentePublico)
btnExcluir.addEventListener('click', () => {
    if (!idSelecionado) return;

    if (confirm('Tem certeza que deseja excluir este gasto?')) {
        fetch(`{{ route('gastos.destroy', ':id') }}`.replace(':id', idSelecionado), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).then(r => r.ok ? location.reload() : alert('Erro ao excluir gasto.'));
    }
});
@endif
</script>
@endsection