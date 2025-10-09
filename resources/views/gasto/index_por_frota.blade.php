@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <!-- 🔙 Botão Voltar -->
    <div class="mb-4">
        <a href="{{ route('frota.show', $frota->frota_id) }}"
            class="inline-flex items-center gap-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
            ← Voltar à Frota
        </a>
    </div>

    <!-- Título -->
    <h2 class="text-2xl font-bold text-white mb-6">💰 Gastos da Frota: {{ $frota->nome }}</h2>


    <!-- 🔹 Filtros -->
    <form method="GET" action="{{ route('frota.gasto.index', $frota->frota_id) }}" class="flex flex-wrap items-end gap-3 mb-6">
        <!-- Categoria -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Categoria</label>
            <select name="categoria" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="">Todas</option>
                <option value="1" {{ request('categoria') == 1 ? 'selected' : '' }}>Combustível</option>
                <option value="2" {{ request('categoria') == 2 ? 'selected' : '' }}>Manutenção</option>
                <option value="3" {{ request('categoria') == 3 ? 'selected' : '' }}>Seguro</option>
                <option value="4" {{ request('categoria') == 4 ? 'selected' : '' }}>Imposto</option>
                <option value="5" {{ request('categoria') == 5 ? 'selected' : '' }}>Outros</option>
            </select>
        </div>

        <!-- Campo -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Campo</label>
            <select name="campo" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="descricao" {{ request('campo') == 'descricao' ? 'selected' : '' }}>Descrição</option>
                <option value="valor" {{ request('campo') == 'valor' ? 'selected' : '' }}>Valor</option>
                <option value="data_gasto" {{ request('campo') == 'data_gasto' ? 'selected' : '' }}>Data</option>
            </select>
        </div>

        <!-- Operador -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Operador</label>
            <select name="operador" class="rounded-lg border-gray-300 px-3 py-1.5 w-48">
                <option value="=" {{ request('operador') == '=' ? 'selected' : '' }}>Igual a (=)</option>
                <option value=">" {{ request('operador') == '>' ? 'selected' : '' }}>Maior que (>)</option>
                <option value="<" {{ request('operador') == '<' ? 'selected' : '' }}>Menor que (<)< /option>
                <option value="like" {{ request('operador') == 'like' ? 'selected' : '' }}>Contém</option>
            </select>
        </div>

        <!-- Valor -->
        <div class="flex-1 max-w-md">
            <label class="block text-white text-sm font-semibold mb-1">Valor</label>
            <input type="text" name="valor" value="{{ request('valor') }}" placeholder="Digite o valor"
                class="rounded-lg border-gray-300 px-3 py-1.5 w-full">
        </div>

        <!-- Botões -->
        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Filtrar
            </button>
            <a href="{{ route('frota.gasto.index', $frota->frota_id) }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Limpar
            </a>
        </div>
    </form>

    <!-- 🔹 Tabela de Gastos -->
    <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left font-semibold">Veículo</th>
                    <th class="px-6 py-3 text-left font-semibold">Categoria</th>
                    <th class="px-6 py-3 text-left font-semibold">Descrição</th>
                    <th class="px-6 py-3 text-left font-semibold">Valor</th>
                    <th class="px-6 py-3 text-left font-semibold">Data</th>
                    <th class="px-4 py-3 text-center font-semibold w-16">Anexos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr class="linha-gasto border-t hover:bg-gray-50 transition cursor-pointer"
                    data-id="{{ $gasto->gasto_id }}">
                    <td class="px-6 py-3">{{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})</td>
                    <td class="px-6 py-3">{{ $gasto->categoriaTexto() }}</td>
                    <td class="px-6 py-3">{{ $gasto->descricao ?? '—' }}</td>
                    <td class="px-6 py-3">R$ {{ number_format($gasto->valor, 2, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>

                    {{-- Indicativo de anexo --}}
                    <td class="px-4 py-3 text-center">
                        @if($gasto->anexos->count() > 0)
                        📎
                        @else
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">Nenhum gasto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">
        <!-- Total de registros -->
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $gastos->count() }}</strong> de <strong>{{ $gastos->total() }}</strong> registros
        </div>

        <!-- Links de paginação -->
        <div>
            {{ $gastos->onEachSide(1)->links() }}
        </div>
    </div>

</div>
@endsection