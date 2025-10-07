@extends('layouts.app')

@section('content')
<div class="py-4 px-6">
    <h2 class="text-2xl font-bold text-white mb-4">💰 Gastos do Veículo</h2>

    {{-- 🔹 Card do Veículo --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6 flex items-center gap-4">
        @if($veiculo->foto)
        <img src="{{ asset('storage/' . $veiculo->foto) }}"
            alt="{{ $veiculo->modelo }}"
            class="w-32 h-24 object-cover rounded-lg">
        @else
        <div class="w-32 h-24 flex items-center justify-center bg-gray-200 rounded-lg">🚗</div>
        @endif

        <div>
            <p class="font-bold text-lg">{{ $veiculo->modelo }}</p>
            <p>Placa: <strong>{{ $veiculo->placa }}</strong></p>
            <p>Ano: {{ $veiculo->ano }}</p>
        </div>
    </div>

    {{-- 🔹 Filtros --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-3">
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Campo</label>
            <select name="campo" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="descricao" {{ request('campo') == 'descricao' ? 'selected' : '' }}>Descrição</option>
                <option value="valor" {{ request('campo') == 'valor' ? 'selected' : '' }}>Valor</option>
                <option value="data_gasto" {{ request('campo') == 'data_gasto' ? 'selected' : '' }}>Data</option>
            </select>
        </div>

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Operador</label>
            <select name="operador" class="rounded-lg border-gray-300 px-3 py-1.5 w-48">
                <option value="=">Igual a (=)</option>
                <option value=">">Maior que (>)</option>
                <option value="<">Menor que (<)< /option>
                <option value="like">Contém</option>
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-white text-sm font-semibold mb-1">Valor</label>
            <input type="text" name="valor" value="{{ request('valor') }}" placeholder="Digite o valor"
                class="rounded-lg border-gray-300 px-3 py-1.5 w-full">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>
            <a href="{{ route('veiculo.gastos.index', $veiculo->veiculo_id) }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
        </div>
    </form>

    {{-- 🔹 Ações --}}
    <div class="flex gap-2 mb-2">
        <a href="{{ route('veiculo.gastos.create', $veiculo->veiculo_id) }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            ➕ Adicionar
        </a>
        <button id="btnEditar" class="px-4 py-2 bg-yellow-500 text-white rounded-lg disabled:opacity-50" disabled>
            ✏️ Editar
        </button>
        <button id="btnVer" class="px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50" disabled>
            👁️ Visualizar
        </button>
        <button id="btnExcluir" class="px-4 py-2 bg-red-600 text-white rounded-lg disabled:opacity-50" disabled>
            🗑️ Excluir
        </button>
    </div>

    {{-- 🔹 Tabela --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="min-w-full border-collapse" id="tabelaGastos">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left font-semibold">Categoria</th>
                    <th class="px-6 py-3 text-left font-semibold">Descrição</th>
                    <th class="px-6 py-3 text-left font-semibold">Valor</th>
                    <th class="px-6 py-3 text-left font-semibold">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer"
                    data-id="{{ $gasto->gasto_id }}">
                    <td class="px-6 py-3">{{ $gasto->categoriaTexto() }}</td>
                    <td class="px-6 py-3">{{ $gasto->descricao ?? '—' }}</td>
                    <td class="px-6 py-3">R$ {{ number_format($gasto->valor, 2, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-500">Nenhum gasto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 🔹 Script --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const linhas = document.querySelectorAll(".linha-gasto");
        const btnVer = document.getElementById("btnVer");
        const btnEditar = document.getElementById("btnEditar");
        const btnExcluir = document.getElementById("btnExcluir");
        let selecionado = null;
        const veiculoId = "{{ $veiculo->veiculo_id }}";

        function atualizarBotoes(ativo) {
            [btnVer, btnEditar, btnExcluir].forEach(btn => {
                btn.disabled = !ativo;
                btn.classList.toggle("opacity-50", !ativo);
                btn.classList.toggle("cursor-not-allowed", !ativo);
            });
        }

        atualizarBotoes(false);

        linhas.forEach(linha => {
            linha.addEventListener("click", () => {
                const id = linha.dataset.id;

                if (selecionado === id) {
                    linha.classList.remove("bg-blue-100");
                    selecionado = null;
                    atualizarBotoes(false);
                    return;
                }

                linhas.forEach(l => l.classList.remove("bg-blue-100"));
                linha.classList.add("bg-blue-100");
                selecionado = id;
                atualizarBotoes(true);

                // Ações mantendo o contexto do veículo
                btnVer.onclick = () => window.location.href = `{{ url('/gastos') }}/${id}`;
                btnEditar.onclick = () => window.location.href = `{{ url('/gastos') }}/${id}/edit?origem=veiculo`;
                btnExcluir.onclick = () => {
                    if (confirm("Deseja realmente excluir este gasto?")) {
                        fetch(`{{ url('/gastos') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        }).then(resp => {
                            if (resp.ok) location.reload();
                            else alert("Erro ao excluir gasto.");
                        });
                    }
                };
            });
        });
    });
</script>
@endsection