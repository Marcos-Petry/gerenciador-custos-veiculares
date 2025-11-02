@extends('layouts.app')

@section('content')
<div class="py-8 px-6 max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-center">👁️ Detalhes do Gasto</h2>

    <div class="bg-white rounded-xl shadow p-6 space-y-6">

        {{-- 🔹 Linha superior: Veículo e Frota --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- 🟢 Card do veículo --}}
            <div class="flex flex-col items-center border border-gray-200 rounded-lg p-3 bg-gray-50">
                @if($gasto->veiculo->foto)
                <img src="{{ asset('storage/' . $gasto->veiculo->foto) }}" 
                     alt="Foto do veículo" 
                     class="w-48 h-32 object-cover rounded-md mb-3 shadow-sm">
                @endif
                <p class="text-sm text-gray-600 font-semibold">Veículo</p>
                <p class="text-base font-medium text-gray-900 text-center">
                    {{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})
                </p>
            </div>

            {{-- 🟦 Card da frota (ou placeholder) --}}
            @if($gasto->veiculo->frota)
            <div class="flex flex-col items-center border border-gray-200 rounded-lg p-3 bg-gray-50">
                @if($gasto->veiculo->frota->foto)
                <img src="{{ asset('storage/' . $gasto->veiculo->frota->foto) }}" 
                     alt="Foto da frota" 
                     class="w-48 h-32 object-cover rounded-md mb-3 shadow-sm">
                @endif
                <p class="text-sm text-gray-600 font-semibold">Frota</p>
                <p class="text-base font-medium text-gray-900 text-center">
                    {{ $gasto->veiculo->frota->nome }}
                </p>
            </div>
            @else
            {{-- Card vazio caso não tenha frota --}}
            <div class="flex flex-col items-center justify-center border border-dashed border-gray-300 rounded-lg p-3 bg-gray-50 text-gray-500">
                <div class="w-48 h-32 flex items-center justify-center bg-gray-100 rounded-md mb-3">
                    <span class="text-sm italic text-gray-400">Sem imagem</span>
                </div>
                <p class="text-sm text-gray-600 font-semibold">Frota</p>
                <p class="text-base font-medium italic text-gray-500">Sem frota associada</p>
            </div>
            @endif
        </div>

        {{-- 🔹 Informações do gasto --}}
        <div class="space-y-2 text-gray-800 text-sm leading-relaxed">
            <p><strong>Categoria:</strong> {{ $gasto->categoria_nome }}</p>
            <p><strong>Descrição:</strong> {{ $gasto->descricao ?? '—' }}</p>
            <p><strong>Valor:</strong> 
                <span class="text-green-600 font-semibold">
                    R$ {{ number_format($gasto->valor, 2, ',', '.') }}
                </span>
            </p>
            <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</p>
            <p>
                <strong>Lançado por:</strong> {{ $gasto->usuario->name }}
                <span class="text-gray-500 text-xs ml-1">&lt;{{ $gasto->usuario->email }}&gt;</span>
            </p>
        </div>

        {{-- 🔹 Anexos --}}
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

    {{-- 🔹 Botão Voltar (com fallback) --}}
    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ url()->previous() ?? route('gasto.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium">
            ← Voltar
        </a>
    </div>
</div>
@endsection