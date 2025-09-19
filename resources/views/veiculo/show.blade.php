@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👁 Detalhes do Veículo</h1>

    <!-- Foto -->
    @if($veiculo->foto)
        <img src="{{ asset('storage/' . $veiculo->foto) }}" alt="Foto do veículo"
             class="w-full h-60 object-cover rounded-lg mb-6">
    @endif

    <p><strong>Modelo:</strong> {{ $veiculo->modelo }}</p>
    <p><strong>Placa:</strong> {{ $veiculo->placa }}</p>
    <p><strong>Ano:</strong> {{ $veiculo->ano }}</p>
    <p><strong>Frota:</strong> {{ $veiculo->frota?->nome ?? '—' }}</p>

    <!-- Visibilidade -->
    <p>
        <strong>Visibilidade:</strong>
        <span class="px-2 py-1 rounded-full text-white text-xs font-medium
            {{ $veiculo->getVisibilidade() == 'Público' ? 'bg-green-500' : 'bg-red-500' }}">
            {{ $veiculo->getVisibilidade() }}
        </span>
    </p>

    <div class="flex justify-start mt-6">
        <a href="{{ route('veiculo.index') }}" 
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            ← Voltar
        </a>
    </div>
</div>
@endsection
