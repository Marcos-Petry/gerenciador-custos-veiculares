@extends('layouts.app')

@section('content')
<div class="py-8 px-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">👁️ Detalhes do Gasto</h2>

    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <p><strong>Veículo:</strong> {{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})</p>

        <!-- Aqui usamos o accessor -->
        <p><strong>Categoria:</strong> {{ $gasto->categoria_nome }}</p>

        <p><strong>Descrição:</strong> {{ $gasto->descricao }}</p>
        <p><strong>Valor:</strong> R$ {{ number_format($gasto->valor, 2, ',', '.') }}</p>
        <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</p>
        <p><strong>Lançado por:</strong> {{ $gasto->usuario->name }}</p>
    </div>

    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-300 rounded-lg">Voltar</a>
        <a href="{{ route('gastos.edit', $gasto->gasto_id) }}"
            class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
            ✏️ Editar
        </a>
    </div>
</div>
@endsection