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

                <button type="button"
                    onclick="abrirSelecaoVeiculos()"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                    🔎 Buscar
                </button>
            </div>
        </div>

        <!-- Responsáveis -->
        <div>
            <label class="block text-sm font-semibold text-gray-700">Responsáveis (opcional)</label>
            <div class="flex gap-2">
                <input type="email" id="responsavel_email"
                    placeholder="Digite o e-mail do responsável"
                    class="flex-grow px-3 py-2 text-sm border rounded-lg focus:outline-none">
                <button type="button" onclick="buscarResponsavel()"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                    🔎 Buscar
                </button>
            </div>
            <p id="responsavel_feedback" class="text-sm mt-2"></p>

            <!-- Lista dinâmica -->
            <ul id="responsaveis_lista" class="mt-3 space-y-2">
                @if(request()->has('responsaveis'))
                @foreach(request('responsaveis') as $respId)
                @php
                $user = \App\Models\User::find($respId);
                @endphp
                @if($user)
                <li id="responsavel_{{ $user->id }}" class="flex justify-between items-center bg-gray-100 p-2 rounded-lg">
                    <span>✅ {{ $user->name }} ({{ $user->email }})</span>
                    <button type="button"
                        onclick="removerResponsavel(parseInt('{{ $user->id }}'))"
                        class="text-red-600 font-bold">✖</button>
                    <input type="hidden" name="responsaveis[]" value="{{ $user->id }}">
                </li>
                @endif
                @endforeach
                @endif
            </ul>
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

<!-- SCRIPT -->
<script>
    function abrirSelecaoVeiculos() {
        const params = new URLSearchParams();
        params.set('origemCampoExterno', '1');

        const nome = document.querySelector('[name="nome"]')?.value || '';
        const descricao = document.querySelector('[name="descricao"]')?.value || '';
        const visibilidade = document.querySelector('[name="visibilidade"]:checked')?.value || '';

        if (nome) params.set('nome', nome);
        if (descricao) params.set('descricao', descricao);
        if (visibilidade) params.set('visibilidade', visibilidade);

        // mantém veículos já selecionados
        document.querySelectorAll('[name="veiculos[]"]').forEach(input => {
            params.append('veiculos[]', input.value);
        });

        // mantém responsáveis já adicionados
        document.querySelectorAll('[name="responsaveis[]"]').forEach(input => {
            params.append('responsaveis[]', input.value);
        });

        window.location.href = "{{ route('veiculo.index') }}" + "?" + params.toString();
    }

    async function buscarResponsavel() {
        const emailInput = document.getElementById('responsavel_email');
        const email = emailInput.value.trim();
        const feedback = document.getElementById('responsavel_feedback');
        const lista = document.getElementById('responsaveis_lista');

        if (!emailInput.checkValidity()) {
            feedback.textContent = "⚠️ Informe um e-mail válido.";
            feedback.className = "text-red-600 text-sm mt-2";
            return;
        }

        try {
            const response = await fetch(`{{ route('usuario.buscar') }}?email=${encodeURIComponent(email)}`);
            const data = await response.json();

            if (!data.success) {
                feedback.textContent = data.message;
                feedback.className = "text-red-600 text-sm mt-2";
                return;
            }

            if (document.getElementById(`responsavel_${data.user.id}`)) {
                feedback.textContent = "⚠️ Esse responsável já foi adicionado.";
                feedback.className = "text-yellow-600 text-sm mt-2";
                return;
            }

            const li = document.createElement("li");
            li.className = "flex justify-between items-center bg-gray-100 p-2 rounded-lg";
            li.id = `responsavel_${data.user.id}`;
            li.innerHTML = `
                <span>✅ ${data.user.name} (${data.user.email})</span>
                <button type="button" onclick="removerResponsavel(${data.user.id})" class="text-red-600 font-bold">✖</button>
                <input type="hidden" name="responsaveis[]" value="${data.user.id}">
            `;
            lista.appendChild(li);

            feedback.textContent = "";
            emailInput.value = "";

        } catch (err) {
            feedback.textContent = "⚠️ Erro na busca. Verifique a conexão.";
            feedback.className = "text-red-600 text-sm mt-2";
        }
    }

    function removerResponsavel(id) {
        const li = document.getElementById(`responsavel_${id}`);
        if (li) li.remove();
    }
</script>
@endsection