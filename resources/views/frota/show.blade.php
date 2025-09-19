@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👁 Detalhes da Frota</h1>

    <!-- Foto -->
    @if($frota->foto)
    <img src="{{ asset('storage/' . $frota->foto) }}" alt="Foto da frota"
        class="w-full h-60 object-cover rounded-lg mb-6">
    @endif

    <p><strong>Nome:</strong> {{ $frota->nome }}</p>
    <p><strong>Descrição:</strong> {{ $frota->descricao ?? '—' }}</p>
    <p><strong>Dono:</strong> {{ $frota->dono->name ?? '—' }}</p>

    <!-- Visibilidade -->
    <p>
        <strong>Visibilidade:</strong>
        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
            {{ $frota->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
            {{ $frota->visibilidade == 1 ? 'Público' : 'Privado' }}
        </span>
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">🚗 Veículos dessa frota</h2>
    <ul class="list-disc ml-6">
        @forelse($frota->veiculos as $veiculo)
        <li>{{ $veiculo->modelo }} ({{ $veiculo->placa }})</li>
        @empty
        <li>Nenhum veículo vinculado.</li>
        @endforelse
    </ul>

    <div class="flex justify-start mt-6">
        <a href="{{ route('frota.index') }}"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            ← Voltar
        </a>
    </div>
</div>
@endsection