@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <!-- Filtros e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <input type="text" placeholder="🔎 Buscar por modelo ou placa"
                class="px-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Filtrar por Frota</option>
                <option value="1">Frota A</option>
                <option value="2">Frota B</option>
            </select>
        </div>

        @if($origemCampoExterno)
        <!-- Botão confirmar aparece só no modo seleção -->
        <button type="submit" form="form-selecao-veiculos"
            class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
            ✅ Confirmar Seleção
        </button>
        @else
        <a href="{{ route('veiculo.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Novo Veículo
        </a>
        @endif
    </div>

    <!-- Se for seleção, abre o form -->
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

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($veiculos as $veiculo)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105">
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
                    <!-- Checkbox para selecionar múltiplos veículos -->
                    <div class="mt-4 flex items-center">
                        <input type="checkbox" name="veiculos[]" value="{{ $veiculo->veiculo_id }}"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Selecionar</span>
                    </div>
                    @else
                    <!-- Ações normais -->
                    <div class="flex justify-between mt-4">
                        <a href="{{ route('veiculo.show', $veiculo) }}" class="text-blue-600 hover:underline">👁 Ver</a>
                        <a href="{{ route('veiculo.edit', $veiculo) }}" class="text-yellow-600 hover:underline">✏ Editar</a>
                        <form action="{{ route('veiculo.destroy', $veiculo) }}" method="POST"
                            onsubmit="return confirm('Excluir veículo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">🗑 Excluir</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="col-span-3 text-center text-gray-500">Nenhum veículo cadastrado.</p>
            @endforelse
        </div>

    </form>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        {{ $veiculos->onEachSide(1)->links() }}
    </div>

</div>
@endsection