@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">🚛 Minhas Frotas</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" id="form-filtros" class="flex flex-col gap-2 mb-3">
        <div id="filtros-container" class="flex flex-col gap-3">

            <!-- Primeira linha -->
            <div class="filtro-item flex flex-wrap items-end gap-3">

                <!-- Campo -->
                <div>
                    <label class="block text-white text-sm font-semibold mb-1">Campo</label>
                    <select name="campo[]" class="campo rounded-lg border-gray-300 px-3 py-1.5 w-44">
                        <option value="nome">Nome</option>
                        <option value="descricao">Descrição</option>
                        <option value="visibilidade">Visibilidade</option>
                        <option value="vinculo">Vínculo</option>
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
                        <input type="text" name="valor[]" placeholder="Digite o valor"
                               class="rounded-lg border-gray-300 px-3 py-1.5 w-80">
                    </div>

                    <!-- Visibilidade -->
                    <div class="valor-visibilidade hidden">
                        <label class="block text-white text-sm font-semibold mb-1">Visibilidade</label>
                        <select name="valor[]" class="rounded-lg border-gray-300 px-3 py-1.5 w-44" disabled>
                            <option value="">Selecione</option>
                            <option value="1">Pública</option>
                            <option value="0">Privada</option>
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

                <!-- Botões (só na primeira linha) -->
                <div class="flex items-end gap-2 botoes-principais">
                    <button type="button" id="add-filtro"
                            class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full hover:bg-green-700 transition text-lg font-bold">+</button>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>

                    <a href="{{ route('frota.index') }}"
                       class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
                </div>

                <!-- Remover (oculto na primeira) -->
                <button type="button"
                        class="remover-filtro hidden text-red-500 hover:text-red-700 text-lg font-bold">×</button>
            </div>
        </div>
    </form>

    <!-- 🔹 Botão Nova Frota -->
    <div class="mb-6">
        <a href="{{ route('frota.create') }}"
           class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-semibold">
            ➕ Nova Frota
        </a>
    </div>

    <!-- 🔹 Grid de Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($frotas as $frota)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
            @if($frota->foto)
            <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/' . $frota->foto) }}" alt="Foto da frota" class="h-full w-auto object-cover">
            </div>
            @else
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">🚛</div>
            @endif

            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $frota->nome }}</h2>
                <p class="text-sm text-gray-600">Descrição: {{ $frota->descricao ?? '—' }}</p>
                <p class="text-sm text-gray-600">Veículos: {{ $frota->veiculos->count() }}</p>

                <p class="text-sm mt-2">
                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium {{ $frota->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $frota->visibilidade == 1 ? 'Público' : 'Privado' }}
                    </span>
                </p>

                <div class="flex justify-center gap-5 mt-5 text-sm font-medium">
                    <a href="{{ route('frota.show', $frota) }}" class="text-blue-600 hover:underline flex items-center gap-1">👁 Ver</a>

                    @if(isset($frota->ehDono) && $frota->ehDono)
                    <a href="{{ route('frota.edit', $frota) }}" class="text-yellow-600 hover:underline flex items-center gap-1">✏ Editar</a>

                    <form action="{{ route('frota.destroy', $frota) }}" method="POST" onsubmit="return confirm('Excluir frota?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline flex items-center gap-1">🗑 Excluir</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-3 text-center text-gray-500">Nenhuma frota cadastrada.</p>
        @endforelse
    </div>

    <!-- 🔹 Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Página <strong>{{ $frotas->currentPage() }}</strong> de <strong>{{ $frotas->lastPage() }}</strong> — <strong>{{ $frotas->total() }}</strong> registros
        </div>
        <div>{{ $frotas->onEachSide(1)->links() }}</div>
    </div>

</div>

<!-- 🔸 Script -->
<script>
const operadoresPorCampo = {
    nome: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
        { valor: 'starts', texto: 'Começa com' },
        { valor: 'ends', texto: 'Termina com' },
    ],
    descricao: [
        { valor: '=', texto: 'Igual a (=)' },
        { valor: 'like', texto: 'Contém' },
    ],
    visibilidade: [
        { valor: '=', texto: 'Igual a (=)' },
    ],
    vinculo: [
        { valor: '=', texto: 'Igual a (=)' },
    ],
};

function mostrarSomente(elMostra, ...elEsconde) {
    elMostra.classList.remove('hidden');
    const inputMostra = elMostra.querySelector('input, select');
    if (inputMostra) inputMostra.disabled = false;

    elEsconde.forEach(el => {
        el.classList.add('hidden');
        const inp = el.querySelector('input, select');
        if (inp) inp.disabled = true;
    });
}

function atualizarLinha(item) {
    // selects
    const campoSel = item.querySelector('.campo');
    const opSel = item.querySelector('.operador');

    // value containers
    const vTexto = item.querySelector('.valor-texto');
    const vVisib = item.querySelector('.valor-visibilidade');
    const vVinc  = item.querySelector('.valor-vinculo');

    // 1) operadores por campo
    opSel.innerHTML = '';
    (operadoresPorCampo[campoSel.value] || []).forEach(op => {
        const o = document.createElement('option');
        o.value = op.valor;
        o.textContent = op.texto;
        opSel.appendChild(o);
    });

    // 2) tipo de valor por campo
    if (campoSel.value === 'visibilidade') {
        mostrarSomente(vVisib, vTexto, vVinc);
    } else if (campoSel.value === 'vinculo') {
        mostrarSomente(vVinc, vTexto, vVisib);
    } else {
        mostrarSomente(vTexto, vVisib, vVinc);
    }
}

// init da primeira linha
document.querySelectorAll('.filtro-item').forEach(atualizarLinha);

// reage mudança de campo
document.addEventListener('change', (e) => {
    if (e.target.classList.contains('campo')) {
        atualizarLinha(e.target.closest('.filtro-item'));
    }
});

// adicionar nova linha (sem botões principais)
document.getElementById('add-filtro').addEventListener('click', () => {
    const container = document.getElementById('filtros-container');
    const base = container.querySelector('.filtro-item');
    const clone = base.cloneNode(true);

    // limpar valores
    clone.querySelectorAll('input').forEach(i => { i.value = ''; });
    clone.querySelectorAll('select').forEach(s => { s.selectedIndex = 0; });

    // remover botões principais
    const botoes = clone.querySelector('.botoes-principais');
    if (botoes) botoes.remove();

    // habilitar botão remover na linha clonada
    const remover = clone.querySelector('.remover-filtro');
    remover.classList.remove('hidden');
    remover.onclick = () => clone.remove();

    // atualizar UI da linha recém-criada
    atualizarLinha(clone);

    container.appendChild(clone);
});
</script>
@endsection
