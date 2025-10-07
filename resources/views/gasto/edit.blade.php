@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">✏️ Editar Gasto</h1>

    <form action="{{ route('gastos.update', $gasto->gasto_id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Origem (geral ou veículo) --}}
        <input type="hidden" name="origem" value="{{ request('origem') }}">
        {{-- Campo hidden que o JS preencherá com os IDs de anexos a remover --}}
        <input type="hidden" name="remover_anexos" id="remover_anexos" value="">

        <!-- Categoria -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Categoria</label>
            <select name="categoria" required
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="1" {{ $gasto->categoria == 1 ? 'selected' : '' }}>Combustível</option>
                <option value="2" {{ $gasto->categoria == 2 ? 'selected' : '' }}>Manutenção</option>
                <option value="3" {{ $gasto->categoria == 3 ? 'selected' : '' }}>Seguro</option>
                <option value="4" {{ $gasto->categoria == 4 ? 'selected' : '' }}>Imposto</option>
                <option value="5" {{ $gasto->categoria == 5 ? 'selected' : '' }}>Outros</option>
            </select>
        </div>

        <!-- Descrição -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Descrição</label>
            <input type="text" name="descricao" value="{{ old('descricao', $gasto->descricao) }}"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Ex: Troca de óleo">
        </div>

        <!-- Valor -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Valor (R$)</label>
            <input type="number" step="0.01" name="valor" value="{{ old('valor', $gasto->valor) }}"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Data -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Data do Gasto</label>
            <input type="date" name="data_gasto" value="{{ old('data_gasto', $gasto->data_gasto) }}"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Anexos -->
        <div>
            <label for="anexos" class="block font-semibold text-gray-700 mb-1">Substituir ou adicionar anexos (opcional)</label>

            <div class="flex items-center gap-3">
                <label
                    for="anexos"
                    class="cursor-pointer bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full hover:bg-blue-200 transition font-semibold shadow-sm">
                    📎 Escolher ficheiros
                </label>
                <span id="file-chosen" class="text-gray-600 text-sm">Nenhum ficheiro selecionado</span>
            </div>

            <input type="file" name="anexos[]" id="anexos" multiple class="hidden" onchange="mostrarNomesArquivos(this)">
            <p class="text-sm text-gray-500 mt-1">
                Você pode enviar mais de um arquivo (máx. 5 MB cada).
            </p>

            <!-- Mostrar anexos existentes -->
            @if($gasto->anexos->count() > 0)
            <div class="mt-4">
                <p class="text-gray-700 font-medium mb-2">📂 Anexos atuais:</p>
                <ul id="lista-anexos" class="space-y-2 text-sm">
                    @foreach($gasto->anexos as $anexo)
                    <li id="anexo-{{ $anexo->anexo_id }}"
                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 transition duration-200">
                        <a href="{{ asset('storage/' . $anexo->caminho) }}" target="_blank"
                            class="text-blue-700 hover:underline truncate w-full">
                            {{ $anexo->nome_original ?? basename($anexo->caminho) }}
                        </a>
                        <button
                            type="button"
                            class="ml-3 text-red-500 hover:text-red-700 font-bold text-lg"
                            data-anexo-id="{{ $anexo->anexo_id }}"
                            onclick="removerAnexo(this)">
                            ×
                        </button>
                    </li>
                    @endforeach
                </ul>
                <p class="text-xs text-gray-500 mt-1">Clique no ❌ para remover o anexo.</p>
            </div>
            @endif
        </div>

        <!-- Botões -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ request('origem') === 'veiculo'
                    ? route('veiculo.gastos.index', $gasto->veiculo_id)
                    : route('gastos.index') }}"
                class="px-5 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition text-gray-800 font-medium">
                ← Voltar
            </a>

            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2">
                💾 <span>Salvar Alterações</span>
            </button>
        </div>
    </form>
</div>

<!-- Script -->
<script>
    const idsParaRemover = new Set();

    function mostrarNomesArquivos(input) {
        const span = document.getElementById('file-chosen');
        if (input.files.length === 0) {
            span.textContent = 'Nenhum ficheiro selecionado';
        } else {
            const nomes = Array.from(input.files).map(f => f.name).join(', ');
            span.textContent = nomes;
        }
    }

    function removerAnexo(botao) {
        const id = botao.dataset.anexoId;
        const li = document.getElementById(`anexo-${id}`);

        if (li) {
            idsParaRemover.add(id);
            li.classList.add('opacity-50', 'scale-95');
            setTimeout(() => li.remove(), 250);

            document.getElementById('remover_anexos').value = Array.from(idsParaRemover).join(',');
        }
    }
</script>
@endsection