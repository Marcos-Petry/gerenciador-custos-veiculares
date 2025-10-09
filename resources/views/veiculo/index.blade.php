@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">🚗 Meus Veículos</h1>

    <!-- 🔹 Filtros -->
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-3">
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Campo</label>
            <select name="campo" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="modelo" {{ request('campo') == 'modelo' ? 'selected' : '' }}>Modelo</option>
                <option value="placa" {{ request('campo') == 'placa' ? 'selected' : '' }}>Placa</option>
                <option value="ano" {{ request('campo') == 'ano' ? 'selected' : '' }}>Ano</option>
            </select>
        </div>

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Operador</label>
            <select name="operador" class="rounded-lg border-gray-300 px-3 py-1.5 w-48">
                <option value="=" {{ request('operador') == '=' ? 'selected' : '' }}>Igual a (=)</option>
                <option value=">" {{ request('operador') == '>' ? 'selected' : '' }}>Maior que (>)</option>
                <option value="<" {{ request('operador') == '<' ? 'selected' : '' }}>Menor que (<)< /option>
                <option value="like" {{ request('operador') == 'like' ? 'selected' : '' }}>Contém</option>
            </select>
        </div>

        <div>
            <label class="block text-white text-sm font-semibold mb-1">Valor</label>
            <input type="text" name="valor" value="{{ request('valor') }}" placeholder="Digite o valor"
                class="rounded-lg border-gray-300 px-3 py-1.5 w-80">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Filtrar
            </button>
            <a href="{{ route('veiculo.index') }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Limpar
            </a>
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
            ➕ Novo Veículo
        </a>
        @endif
    </div>

    <!-- 🔹 Se for seleção, abre o form -->
    @if($origemCampoExterno)
    <form id="form-selecao-veiculos" method="GET" action="{{ route('frota.create') }}">
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

        <!-- 🔹 Grid de Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($veiculos as $veiculo)
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">

                <!-- Foto -->
                @if($veiculo->foto)
                <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $veiculo->foto) }}"
                        alt="Foto do veículo"
                        class="h-full w-auto object-cover">
                </div>
                @else
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">
                    🚗
                </div>
                @endif

                <!-- Conteúdo -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $veiculo->modelo }}</h2>
                    <p class="text-sm text-gray-600">Placa: <strong>{{ $veiculo->placa }}</strong></p>
                    <p class="text-sm text-gray-600">Ano: {{ $veiculo->ano }}</p>
                    <p class="text-sm text-gray-600">Frota: {{ $veiculo->frota?->nome ?? '—' }}</p>

                    <!-- Badge de Visibilidade -->
                    <p class="text-sm mt-2">
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                        {{ $veiculo->getVisibilidade() == 'Público' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $veiculo->getVisibilidade() }}
                        </span>
                    </p>

                    @if($origemCampoExterno)
                    <!-- Checkbox para seleção -->
                    <div class="mt-4 flex items-center">
                        <input type="checkbox" name="veiculos[]" value="{{ $veiculo->veiculo_id }}"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Selecionar</span>
                    </div>
                    @else
                    <!-- 🔹 Ações -->
                    <div class="flex justify-center mt-5 gap-6 border-t pt-3 text-sm font-medium">
                        <!-- Ver -->
                        <a href="{{ route('veiculo.show', $veiculo) }}"
                            class="flex items-center text-blue-600 hover:text-blue-800 transition gap-1">
                            👁 <span>Ver</span>
                        </a>

                        <!-- Apenas o dono pode editar/excluir -->
                        @if($veiculo->usuario_dono_id === Auth::id())
                        <a href="{{ route('veiculo.edit', $veiculo) }}"
                            class="flex items-center text-yellow-600 hover:text-yellow-800 transition gap-1">
                            ✏ <span>Editar</span>
                        </a>

                        <form action="{{ route('veiculo.destroy', $veiculo) }}" method="POST"
                            onsubmit="return confirm('Excluir veículo?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center text-red-600 hover:text-red-800 transition gap-1">
                                🗑 <span>Excluir</span>
                            </button>
                        </form>
                        @endif

                        <!-- Gastos -->
                        <a href="{{ route('veiculo.gastos.index', $veiculo->veiculo_id) }}"
                            class="flex items-center text-green-600 hover:text-green-800 transition gap-1">
                            💰 <span>Gastos</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="col-span-3 text-center text-gray-500">Nenhum veículo cadastrado.</p>
            @endforelse
        </div>

    </form>

    <!-- Paginação-->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">

        <!-- Total de registros -->
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $veiculos->count() }}</strong> de <strong>{{ $veiculos->total() }}</strong> registros
        </div>

        <!-- Links de paginação -->
        <div>
            {{ $veiculos->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection