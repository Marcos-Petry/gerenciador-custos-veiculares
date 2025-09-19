@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Cadastrar Frota</h1>

    <form id="form-frota" action="{{ route('frota.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Nome -->
        <div>
            <label for="nome" class="block text-sm font-semibold text-gray-700">Nome</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Frota Regional Sul"
                value="{{ old('nome', request('nome')) }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                required>
            @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Veículos -->
        <div>
            <label class="block text-sm font-semibold text-gray-700">Veículos (opcional)</label>
            <div class="mt-2 flex rounded-lg border border-gray-300 shadow-sm overflow-hidden">
                <div class="flex-grow px-3 py-2 text-sm text-gray-600 bg-white">
                    @php
                    $veiculosSelecionados = request('veiculos', []);
                    $nomesVeiculos = \App\Models\Veiculo::whereIn('veiculo_id', (array) $veiculosSelecionados)
                    ->pluck('modelo')
                    ->toArray();
                    @endphp

                    @if(count($nomesVeiculos) > 0)
                    {{ implode(', ', $nomesVeiculos) }}
                    @foreach((array) $veiculosSelecionados as $vId)
                    <input type="hidden" name="veiculos[]" value="{{ $vId }}">
                    @endforeach
                    @else
                    Nenhum veículo selecionado
                    @endif
                </div>

                <!-- BOTÃO COM SCRIPT -->
                <button type="button"
                    onclick="abrirSelecaoVeiculos()"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                    🔎 Buscar
                </button>
            </div>
        </div>

        <!-- Responsáveis -->
        <div>
            <label class="block text-sm font-semibold text-gray-700">Responsáveis</label>
            <div class="mt-2 flex rounded-lg border border-gray-300 shadow-sm overflow-hidden">
                <div class="flex-grow px-3 py-2 text-sm text-gray-600 bg-white">
                    Nenhum responsável adicionado
                </div>
                <a href="#"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                    🔎 Buscar
                </a>
            </div>
        </div>

        <!-- Descrição -->
        <div>
            <label for="descricao" class="block text-sm font-semibold text-gray-700">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('descricao', request('descricao')) }}</textarea>
            @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Foto -->
        <div>
            <label for="foto" class="block text-sm font-semibold text-gray-700">Foto da Frota</label>
            <input type="file" id="foto" name="foto"
                class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0 file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Visibilidade -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Visibilidade</label>
            <div class="flex gap-4">
                <label class="flex-1">
                    <input type="radio" name="visibilidade" value="1"
                        class="hidden peer"
                        {{ old('visibilidade', request('visibilidade')) == 1 ? 'checked' : '' }}>
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
                        {{ old('visibilidade', request('visibilidade')) == 0 ? 'checked' : '' }}>
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
                Salvar Frota
            </button>
        </div>
    </form>
</div>

<!-- SCRIPT para levar os dados digitados até a seleção de veículos -->
<script>
    function abrirSelecaoVeiculos() {
        const params = new URLSearchParams();
        params.set('origemCampoExterno', '1');

        const nome = document.querySelector('[name="nome"]')?.value || '';
        const descricao = document.querySelector('[name="descricao"]')?.value || '';

        if (nome) params.set('nome', nome);
        if (descricao) params.set('descricao', descricao);

        // mantém veiculos já selecionados
        document.querySelectorAll('[name="veiculos[]"]').forEach(input => {
            params.append('veiculos[]', input.value);
        });

        window.location.href = "{{ route('veiculo.index') }}" + "?" + params.toString();
    }
</script>
@endsection