@extends('layouts.app')

@section('content')
<div class="py-8 px-6 max-w-2xl mx-auto bg-white rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">➕ Novo Gasto</h2>

    <form action="{{ route('gastos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Veículo -->
        @isset($veiculo)
        <input type="hidden" name="veiculo_id" value="{{ $veiculo->veiculo_id }}">
        <p class="text-gray-700">
            🚗 <strong>{{ $veiculo->modelo }} ({{ $veiculo->placa }})</strong>
        </p>
        @else
        <div>
            <label for="veiculo_id" class="block font-semibold text-gray-700 mb-1">Veículo</label>
            <select name="veiculo_id" id="veiculo_id" required
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Selecione um veículo</option>
                @foreach($veiculos as $v)
                <option value="{{ $v->veiculo_id }}">{{ $v->modelo }} ({{ $v->placa }})</option>
                @endforeach
            </select>
        </div>
        @endisset

        <!-- Categoria -->
        <div>
            <label for="categoria" class="block font-semibold text-gray-700 mb-1">Categoria</label>
            <select name="categoria" id="categoria" required
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Selecione</option>
                @foreach(\App\Models\Gasto::CATEGORIAS as $key => $nome)
                <option value="{{ $key }}" {{ isset($gasto) && $gasto->categoria == $key ? 'selected' : '' }}>
                    {{ $nome }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Descrição -->
        <div>
            <label for="descricao" class="block font-semibold text-gray-700 mb-1">Descrição</label>
            <input type="text" name="descricao" id="descricao"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Valor -->
        <div>
            <label for="valor" class="block font-semibold text-gray-700 mb-1">Valor (R$)</label>
            <input type="number" step="0.01" name="valor" id="valor" required
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Data -->
        <div>
            <label for="data_gasto" class="block font-semibold text-gray-700 mb-1">Data do Gasto</label>
            <input type="date" name="data_gasto" id="data_gasto" required
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Anexos -->
        <div>
            <label for="anexos" class="block font-semibold text-gray-700 mb-1">Anexos (opcional)</label>
            <div class="flex items-center gap-3">
                <label
                    for="anexos"
                    class="cursor-pointer bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full hover:bg-blue-200 transition font-semibold shadow-sm">
                    Escolher ficheiros
                </label>
                <span id="file-chosen" class="text-gray-600 text-sm">Nenhum ficheiro selecionado</span>
            </div>

            <input type="file" name="anexos[]" id="anexos" multiple class="hidden" onchange="mostrarNomesArquivos(this)">
            <p class="text-sm text-gray-500 mt-1">
                Você pode selecionar mais de um arquivo (máx. 5 MB cada).
            </p>
        </div>

        <!-- Botões -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ url()->previous() }}"
                class="px-5 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition text-gray-800 font-medium">
                ← Voltar
            </a>

            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2">
                💾 <span>Salvar Gasto</span>
            </button>
        </div>
    </form>
</div>

<!-- Script: mostra nome dos arquivos selecionados -->
<script>
    function mostrarNomesArquivos(input) {
        const span = document.getElementById('file-chosen');
        if (input.files.length === 0) {
            span.textContent = 'Nenhum ficheiro selecionado';
        } else {
            const nomes = Array.from(input.files).map(f => f.name).join(', ');
            span.textContent = nomes;
        }
    }
</script>
@endsection