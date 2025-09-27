@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <h1 class="text-2xl font-bold text-white mb-6">🚗 Novo Veículo</h1>

    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl mx-auto border border-gray-200">
        <form id="form-veiculo" action="{{ route('veiculo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Modelo -->
            <div>
                <label for="modelo" class="block text-sm font-semibold text-gray-700">Modelo</label>
                <input type="text" id="modelo" name="modelo"
                    value="{{ old('modelo', request('modelo')) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ex: Corolla XEi" required>
                @error('modelo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Placa -->
            <div>
                <label for="placa" class="block text-sm font-semibold text-gray-700">Placa</label>
                <input type="text" id="placa" name="placa"
                    value="{{ old('placa', request('placa')) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="ABC-1234" required>
                @error('placa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Ano -->
            <div>
                <label for="ano" class="block text-sm font-semibold text-gray-700">Ano</label>
                <input type="number" id="ano" name="ano"
                    value="{{ old('ano', request('ano')) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="2024" required>
                @error('ano') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Frota -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">Frota (opcional)</label>
                <div class="mt-2 flex rounded-lg border border-gray-300 shadow-sm overflow-hidden">
                    <div class="flex-grow px-3 py-2 text-sm text-gray-600 bg-white">
                        @php
                        $frotaId = request('frota_id');
                        $nomeFrota = $frotaId ? \App\Models\Frota::find($frotaId)?->nome : null;
                        @endphp

                        @if($nomeFrota)
                        {{ $nomeFrota }}
                        <input type="hidden" name="frota_id" value="{{ $frotaId }}">
                        @else
                        Nenhuma frota selecionada
                        @endif
                    </div>

                    <button type="button"
                        onclick="abrirSelecaoFrota()"
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

            <!-- Foto -->
            <div>
                <label for="foto" class="block text-sm font-semibold text-gray-700">Foto do Veículo (opcional)</label>
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
            <div class="flex justify-between items-center">
                <a href="{{ route('veiculo.index') }}"
                    class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
                    ← Voltar
                </a>
                <button type="submit"
                    class="px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">
                    Salvar Veículo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirSelecaoFrota() {
        const params = new URLSearchParams();
        params.set('origemCampoExterno', '1');

        const modelo = document.querySelector('[name="modelo"]')?.value || '';
        const placa = document.querySelector('[name="placa"]')?.value || '';
        const ano = document.querySelector('[name="ano"]')?.value || '';
        const frotaIdAtual = document.querySelector('[name="frota_id"]')?.value || '';
        const visibilidade = document.querySelector('[name="visibilidade"]:checked')?.value || '';

        if (modelo) params.set('modelo', modelo);
        if (placa) params.set('placa', placa);
        if (ano) params.set('ano', ano);
        if (frotaIdAtual) params.set('frota_id', frotaIdAtual);
        if (visibilidade) params.set('visibilidade', visibilidade);

        // 🔑 aqui pega todos os responsáveis já adicionados
        document.querySelectorAll('[name="responsaveis[]"]').forEach(input => {
            params.append('responsaveis[]', input.value);
        });

        window.location.href = "{{ route('frota.index') }}" + "?" + params.toString();
    }


    async function buscarResponsavel() {
        const emailInput = document.getElementById('responsavel_email');
        const email = emailInput.value.trim();
        const feedback = document.getElementById('responsavel_feedback');
        const lista = document.getElementById('responsaveis_lista');

        // validação HTML5 (campo type=email já ajuda aqui)
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

            // evitar duplicados
            if (document.getElementById(`responsavel_${data.user.id}`)) {
                feedback.textContent = "⚠️ Esse responsável já foi adicionado.";
                feedback.className = "text-yellow-600 text-sm mt-2";
                return;
            }

            // adiciona na lista
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