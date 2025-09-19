@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <!-- Filtros e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <input type="text" placeholder="🔎 Buscar por nome da frota"
                class="px-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        @if($origemCampoExterno)
            <!-- Botão confirmar aparece só no modo seleção -->
            <button type="submit" form="form-selecao-frotas"
                class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                ✅ Confirmar Seleção
            </button>
        @else
            <a href="{{ route('frota.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                ➕ Nova Frota
            </a>
        @endif
    </div>

    <!-- Se for seleção, abre o form -->
    @if($origemCampoExterno)
    <form id="form-selecao-frotas" 
          method="GET" 
          action="{{ request()->has('veiculo_id') ? route('veiculo.edit', request('veiculo_id')) : route('veiculo.create') }}">

        <!-- Mantém os dados que vieram da tela de create/edit -->
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
            @forelse($frotas as $frota)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105">
                <!-- Foto -->
                @if($frota->foto)
                <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $frota->foto) }}"
                        alt="Foto da frota"
                        class="h-full w-auto object-cover">
                </div>
                @else
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">
                    🚛
                </div>
                @endif

                <!-- Conteúdo -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $frota->nome }}</h2>
                    <p class="text-sm text-gray-600">Descrição: {{ $frota->descricao ?? '—' }}</p>
                    <p class="text-sm text-gray-600">Veículos: {{ $frota->veiculos->count() }}</p>

                    <!-- Badge de Visibilidade -->
                    <p class="text-sm mt-2">
                        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                            {{ $frota->getVisibilidadeTextoAttribute() == 'Público' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $frota->getVisibilidadeTextoAttribute() }}
                        </span>
                    </p>

                    @if($origemCampoExterno)
                        <!-- Radio: só pode escolher 1 frota -->
                        <div class="mt-4 flex items-center">
                            <input type="radio" name="frota_id" value="{{ $frota->frota_id }}"
                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Selecionar</span>
                        </div>
                    @else
                        <!-- Ações normais -->
                        <div class="flex justify-between mt-4">
                            <a href="{{ route('frota.show', $frota) }}" class="text-blue-600 hover:underline">👁 Ver</a>
                            <a href="{{ route('frota.edit', $frota) }}" class="text-yellow-600 hover:underline">✏ Editar</a>
                            <form action="{{ route('frota.destroy', $frota) }}" method="POST"
                                onsubmit="return confirm('Excluir frota?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑 Excluir</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="col-span-3 text-center text-gray-500">Nenhuma frota cadastrada.</p>
            @endforelse
        </div>
    </form>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        {{ $frotas->onEachSide(1)->links() }}
    </div>

</div>
@endsection
