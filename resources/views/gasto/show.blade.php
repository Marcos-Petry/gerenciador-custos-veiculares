@extends('layouts.app')

@section('content')
<div class="py-8 px-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-center">👁️ Detalhes do Gasto</h2>

    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <p><strong>Veículo:</strong> {{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})</p>
        <p><strong>Categoria:</strong> {{ $gasto->categoria_nome }}</p>
        <p><strong>Descrição:</strong> {{ $gasto->descricao ?? '—' }}</p>
        <p><strong>Valor:</strong> R$ {{ number_format($gasto->valor, 2, ',', '.') }}</p>
        <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</p>
        <p><strong>Lançado por:</strong> {{ $gasto->usuario->name }}</p>

        <!-- Anexos -->
        @if($gasto->anexos->count() > 0)
        <div class="mt-6">
            <p class="font-semibold text-gray-800 mb-2">📁 Anexos:</p>
            <ul class="space-y-2">
                @foreach($gasto->anexos as $anexo)
                <li class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                    <a href="{{ asset('storage/' . $anexo->caminho) }}" target="_blank"
                        class="text-blue-700 hover:underline truncate">
                        {{ $anexo->nome_original ?? basename($anexo->caminho) }}
                    </a>
                    <a href="{{ asset('storage/' . $anexo->caminho) }}" target="_blank"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Abrir
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <p class="text-gray-500 italic mt-4">Sem anexos cadastrados para este gasto.</p>
        @endif
    </div>

    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ url()->previous() }}"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium">
            ← Voltar
        </a>
    </div>
</div>
@endsection