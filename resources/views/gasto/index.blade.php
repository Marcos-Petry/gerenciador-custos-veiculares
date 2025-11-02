@extends('layouts.app')

@section('content')
<div class="py-4 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">💰 Meus Gastos</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-4">
        <div id="filtros-container" class="flex flex-col gap-3">
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo[]" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="veiculo">Veículo</option>
                        <option value="frota">Frota</option>
                        <option value="descricao">Descrição</option>
                        <option value="valor">Valor</option>
                        <option value="data_gasto">Data</option>
                        <option value="categoria">Categoria</option>
                        <option value="usuario">Incluído por</option>
                        <option value="vinculo">Vínculo</option>
                        <option value="anexos">Anexos</option>
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

                    <!-- Frota -->
                    <div class="valor-frota hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Frota</label>
                        <select class="sel-frota rounded-lg border-gray-300 px-3 py-1.5 w-64" disabled>
                            <option value="">Selecione a frota</option>
                            @foreach($frotas as $f)
                                <option value="{{ $f->frota_id }}">{{ $f->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Vínculo -->
                    <div class="valor-vinculo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Vínculo</label>
                        <select class="sel-vinculo rounded-lg border-gray-300 px-3 py-1.5 w-52" disabled>
                            <option value="">Selecione</option>
                            <option value="dono">Sou dono</option>
                            <option value="responsavel">Sou responsável</option>
                        </select>
                    </div>

                    <!-- Usuário -->
                    <div class="valor-usuario hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Incluído por</label>
                        <input type="text" class="inp-usuario rounded-lg border-gray-300 px-3 py-1.5 w-72"
                            placeholder="Nome do usuário" disabled>
                    </div>

                    <!-- Anexos -->
                    <div class="valor-anexo hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Anexos</label>
                        <select class="sel-anexo rounded-lg border-gray-300 px-3 py-1.5 w-52" disabled>
                            <option value="">Selecione</option>
                            <option value="com">Com anexos</option>
                            <option value="sem">Sem anexos</option>
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
    <div class="flex gap-2 mb-4">
        <a href="{{ route('gastos.create') }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">➕ Incluir</a>

        <button id="btnEditar" class="px-4 py-2 bg-yellow-500 text-white rounded-lg disabled:opacity-50" disabled>✏️ Editar</button>
        <button id="btnVer" class="px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50" disabled>👁️ Visualizar</button>
        <button id="btnExcluir" class="px-4 py-2 bg-red-600 text-white rounded-lg disabled:opacity-50" disabled>🗑️ Excluir</button>
    </div>

    <!-- 🔹 Tabela -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left font-semibold">Veículo</th>
                    <th class="px-6 py-3 text-left font-semibold">Frota</th>
                    <th class="px-6 py-3 text-left font-semibold">Categoria</th>
                    <th class="px-6 py-3 text-left font-semibold">Descrição</th>
                    <th class="px-6 py-3 text-left font-semibold">Valor</th>
                    <th class="px-6 py-3 text-left font-semibold">Data</th>
                    <th class="px-6 py-3 text-left font-semibold">Incluído por</th>
                    <th class="px-4 py-3 text-center font-semibold w-16">Anexos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer"
                    data-id="{{ $gasto->gasto_id }}">
                    <td class="px-6 py-3">{{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})</td>
                    <td class="px-6 py-3">{{ $gasto->veiculo->frota->nome ?? '—' }}</td>
                    <td class="px-6 py-3">{{ $gasto->categoriaTexto() }}</td>
                    <td class="px-6 py-3">{{ $gasto->descricao ?? '—' }}</td>
                    <td class="px-6 py-3">R$ {{ number_format($gasto->valor, 2, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3">{{ $gasto->usuario->name ?? 'Desconhecido' }}</td>
                    <td class="px-4 py-3 text-center">@if($gasto->anexos->count() > 0)📎@endif</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">Nenhum gasto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $gastos->count() }}</strong> de <strong>{{ $gastos->total() }}</strong> registros
        </div>
        <div>{{ $gastos->onEachSide(1)->links() }}</div>
    </div>
</div>

<script>
const operadoresPorCampo = {
    veiculo: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    frota: [{ valor: '=', texto: 'Igual a (=)' }],
    descricao: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    valor: [{ valor: '=', texto: 'Igual a (=)' }, { valor: '>', texto: 'Maior que (>)' }, { valor: '<', texto: 'Menor que (<)' }, { valor: 'between', texto: 'Entre' }],
    data_gasto: [{ valor: '=', texto: 'Igual a (=)' }, { valor: '>', texto: 'Após (>)' }, { valor: '<', texto: 'Antes (<)' }, { valor: 'between', texto: 'Entre' }],
    categoria: [{ valor: '=', texto: 'Igual a (=)' }],
    usuario: [{ valor: '=', texto: 'Igual a (=)' }, { valor: 'like', texto: 'Contém' }],
    vinculo: [{ valor: '=', texto: 'Igual a (=)' }],
    anexos: [{ valor: '=', texto: 'Igual a (=)' }],
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
    const vUsu = item.querySelector('.valor-usuario');
    const vVin = item.querySelector('.valor-vinculo');
    const vFro = item.querySelector('.valor-frota');
    const vAnx = item.querySelector('.valor-anexo');

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

    if (campo.value === 'frota') mostrarSomente(vFro, vTexto, vEntre, vData, vCat, vUsu, vVin, vAnx);
    else if (campo.value === 'anexos') mostrarSomente(vAnx, vTexto, vEntre, vData, vCat, vUsu, vVin, vFro);
    else if (campo.value === 'data_gasto' && op === 'between') mostrarSomente(vData, vTexto, vEntre, vCat, vUsu, vVin, vFro, vAnx);
    else if (campo.value === 'data_gasto') mostrarSomente(vData, vTexto, vEntre, vCat, vUsu, vVin, vFro, vAnx);
    else if (campo.value === 'categoria') mostrarSomente(vCat, vTexto, vEntre, vData, vUsu, vVin, vFro, vAnx);
    else if (campo.value === 'valor' && op === 'between') mostrarSomente(vEntre, vTexto, vData, vCat, vUsu, vVin, vFro, vAnx);
    else if (campo.value === 'usuario') mostrarSomente(vUsu, vTexto, vEntre, vData, vCat, vVin, vFro, vAnx);
    else if (campo.value === 'vinculo') mostrarSomente(vVin, vTexto, vEntre, vData, vCat, vUsu, vFro, vAnx);
    else mostrarSomente(vTexto, vEntre, vData, vCat, vUsu, vVin, vFro, vAnx);
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

// seleção + botões CRUD
let linhaSelecionada = null;
let idSelecionado = null;
document.querySelectorAll('.linha-gasto').forEach(linha => {
    linha.addEventListener('click', () => {
        if (linhaSelecionada) linhaSelecionada.classList.remove('bg-blue-100');
        linhaSelecionada = linha;
        linhaSelecionada.classList.add('bg-blue-100');
        idSelecionado = linha.getAttribute('data-id');
        document.getElementById('btnEditar').disabled = false;
        document.getElementById('btnVer').disabled = false;
        document.getElementById('btnExcluir').disabled = false;
    });
});

document.getElementById('btnEditar').addEventListener('click', () => {
    if (idSelecionado) window.location.href = @json(route('gastos.edit', ':id')).replace(':id', idSelecionado);
});
document.getElementById('btnVer').addEventListener('click', () => {
    if (idSelecionado) window.location.href = @json(route('gastos.show', ':id')).replace(':id', idSelecionado);
});
document.getElementById('btnExcluir').addEventListener('click', () => {
    if (!idSelecionado) return;
    if (confirm('Tem certeza que deseja excluir este gasto?')) {
        fetch(@json(route('gastos.destroy', ':id')).replace(':id', idSelecionado), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).then(r => r.ok ? location.reload() : alert('Erro ao excluir gasto.'));
    }
});
</script>
@endsection
