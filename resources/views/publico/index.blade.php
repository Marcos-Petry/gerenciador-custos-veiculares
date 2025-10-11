@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h1 class="text-2xl font-bold text-white mb-6">🔎 Consulta Pública</h1>

    {{-- 🔹 Filtros --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        @if(isset($origem))
        <button type="button" id="confirmarSelecao"
            class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition flex items-center gap-2">
            ✅ Confirmar Seleção
        </button>
        @endif

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Campo</label>
            <select name="campo" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="">Selecione</option>
                <option value="titulo" {{ request('campo') == 'titulo' ? 'selected' : '' }}>Nome/Modelo</option>
                <option value="placa" {{ request('campo') == 'placa' ? 'selected' : '' }}>Placa</option>
                <option value="ano" {{ request('campo') == 'ano' ? 'selected' : '' }}>Ano</option>
            </select>
        </div>

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Operador</label>
            <select name="operador" class="rounded-lg border-gray-300 px-3 py-1.5 w-48">
                <option value="=" {{ request('operador') == '=' ? 'selected' : '' }}>Igual a (=)</option>
                <option value="like" {{ request('operador') == 'like' ? 'selected' : '' }}>Contém</option>
                <option value=">" {{ request('operador') == '>' ? 'selected' : '' }}>Maior que (>)</option>
                <option value="<" {{ request('operador') == '<' ? 'selected' : '' }}>Menor que (<)< /option>
            </select>
        </div>

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Valor</label>
            <input type="text" name="valor" value="{{ request('valor') }}" placeholder="Digite o valor"
                class="rounded-lg border-gray-300 px-3 py-1.5 w-80">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>
            <a href="{{ route('publico.index') }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Limpar</a>
        </div>
    </form>

    {{-- 🔹 Cards --}}
    <form id="form-selecao-veiculos" method="GET" action="{{ route('publico.index') }}">
        {{-- 🔸 parâmetros que o controller precisa --}}
        <input type="hidden" name="origemCampoExterno" value="{{ $origem ?? '' }}">
        <input type="hidden" name="selecionado" id="selecionadoInput">

        {{-- 🔸 mantém os veículos já selecionados --}}
        <input type="hidden" name="veiculoA" value="{{ request('veiculoA') }}">
        <input type="hidden" name="veiculoB" value="{{ request('veiculoB') }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($itens as $item)
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">

                {{-- Imagem --}}
                @if($item->foto)
                <div class="w-full h-40 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Imagem" class="h-full w-auto object-cover">
                </div>
                @else
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">
                    {{ $item->tipo === 'frota' ? '🚛' : '🚗' }}
                </div>
                @endif

                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $item->titulo }}</h2>
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                            {{ $item->tipo === 'veiculo' ? 'bg-blue-500' : 'bg-orange-500' }}">
                            {{ ucfirst($item->tipo) }}
                        </span>
                    </div>

                    @if($item->tipo === 'veiculo')
                    <p class="text-sm text-gray-600">Placa: <strong>{{ $item->placa }}</strong></p>
                    <p class="text-sm text-gray-600">Ano: {{ $item->ano ?? '—' }}</p>
                    <p class="text-sm text-gray-600">Frota: {{ $item->frota_nome ?? '—' }}</p>
                    @else
                    <p class="text-sm text-gray-600 mb-1">
                        Criada em: {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                    </p>
                    @endif

                    <div class="flex justify-between items-center mt-4">
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                            {{ $item->visibilidade ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $item->visibilidade ? 'Público' : 'Privado' }}
                        </span>

                        {{-- 🔸 Checkbox de seleção no modo externo --}}
                        @if(isset($origem) && $item->tipo === 'veiculo')
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox"
                                class="checkbox-selecao w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                value="{{ $item->id }}">
                            <span>Selecionar</span>
                        </label>
                        @else
                        {{-- 🔸 Ações normais --}}
                        <div class="flex gap-2">
                            <a href="{{ $item->tipo === 'veiculo'
                                        ? route('veiculo.show', $item->id)
                                        : route('frota.show', $item->id) }}"
                                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                👁️ Ver
                            </a>

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

    <!-- 🔹 Paginação -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">

        <!-- Total de registros -->
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $itens->count() }}</strong> de <strong>{{ $itens->total() }}</strong> registros
        </div>

        <!-- Links de paginação -->
        <div>
            {{ $itens->onEachSide(1)->links() }}
        </div>
    </div>

</div>

<script>
    // Permite selecionar apenas um checkbox por vez
    document.querySelectorAll('.checkbox-selecao').forEach(cb => {
        cb.addEventListener('change', function() {
            document.querySelectorAll('.checkbox-selecao').forEach(o => {
                if (o !== this) o.checked = false;
            });
        });
    });

    // Envia o formulário ao confirmar seleção
    document.getElementById('confirmarSelecao')?.addEventListener('click', function() {
        const selecionado = document.querySelector('.checkbox-selecao:checked');
        if (!selecionado) {
            alert('Selecione um veículo antes de confirmar.');
            return;
        }
        document.getElementById('selecionadoInput').value = selecionado.value;
        document.getElementById('form-selecao-veiculos').submit();
    });
</script>
@endsection