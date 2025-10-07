@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h2 class="text-2xl font-bold mb-6">🚗 Veículos da Frota: {{ $frota->nome }}</h2>

    <!-- Card resumo -->
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <p><strong>Dono da frota:</strong> {{ $frota->dono->name ?? '—' }}</p>
        <p><strong>Visibilidade:</strong>
            <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                {{ $frota->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $frota->visibilidade == 1 ? 'Público' : 'Privado' }}
            </span>
        </p>
    </div>

    <!-- Grid de cards de veículos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($veiculos as $veiculo)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2">

            <!-- Foto -->
            @if($veiculo->foto)
            <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/' . $veiculo->foto) }}" alt="Foto do veículo" class="h-full w-auto object-cover">
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
                <p class="text-sm text-gray-600">Visibilidade:
                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                        {{ $veiculo->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $veiculo->getVisibilidade() }}
                    </span>
                </p>

                <!-- Apenas ação de visualizar -->
                <div class="flex justify-center mt-4">
                    <a href="{{ route('veiculo.show', ['veiculo' => $veiculo->veiculo_id, 'from_frota' => $frota->frota_id]) }}"
                        class="text-blue-600 font-bold hover:underline">
                        👁️ Ver Detalhes
                    </a>
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-3 text-center text-gray-500">Nenhum veículo encontrado para esta frota.</p>
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        {{ $veiculos->onEachSide(1)->links() }}
    </div>

    <!-- Botão voltar -->
    <div class="mt-6">
        <a href="{{ route('frota.show', $frota->frota_id) }}"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
            ← Voltar à Frota
        </a>
    </div>
</div>
@endsection