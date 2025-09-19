@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏ Editar Frota</h1>

    <form id="form-frota" action="{{ route('frota.update', $frota) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Nome -->
        <div>
            <label for="nome" class="block text-sm font-semibold text-gray-700">Nome</label>
            <input type="text" id="nome" name="nome"
                value="{{ old('nome', $frota->nome) }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                required>
            @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Descrição -->
        <div>
            <label for="descricao" class="block text-sm font-semibold text-gray-700">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('descricao', $frota->descricao) }}</textarea>
            @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Foto -->
        <div>
            <label for="foto" class="block text-sm font-semibold text-gray-700">Foto da Frota</label>
            <input type="file" id="foto" name="foto"
                class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0 file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            @if($frota->foto)
            <p class="text-sm text-gray-500 mt-1">Foto atual:</p>
            <img src="{{ asset('storage/' . $frota->foto) }}" alt="Foto da frota"
                class="w-40 h-28 object-cover rounded-lg mt-2">
            @endif
            @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Visibilidade -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Visibilidade</label>
            <div class="flex gap-4">
                <label class="flex-1">
                    <input type="radio" name="visibilidade" value="1"
                        class="hidden peer"
                        {{ old('visibilidade', $frota->visibilidade) == 1 ? 'checked' : '' }}>
                    <div class="cursor-pointer px-4 py-2 text-center rounded-xl border
                        peer-checked:bg-green-600 peer-checked:text-white
                        peer-checked:border-green-600
                        hover:bg-green-50 hover:border-green-400 transition">
                        🌍 Público
                    </div>
                </label>

                <label class="flex-1">
                    <input type="radio" name="visibilidade" value="0"
                        class="hidden peer"
                        {{ old('visibilidade', $frota->visibilidade) == 0 ? 'checked' : '' }}>
                    <div class="cursor-pointer px-4 py-2 text-center rounded-xl border
                        peer-checked:bg-red-600 peer-checked:text-white
                        peer-checked:border-red-600
                        hover:bg-red-50 hover:border-red-400 transition">
                        🔒 Privado
                    </div>
                </label>
            </div>
            @error('visibilidade') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Botões -->
        <div class="flex justify-between">
            <a href="{{ route('frota.index') }}"
                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                ← Voltar
            </a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection