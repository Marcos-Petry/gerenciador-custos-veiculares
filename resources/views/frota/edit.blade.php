@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h1 class="text-2xl font-bold text-white mb-6">✏ Editar Frota</h1>

    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl mx-auto border border-gray-200">
        <form id="form-frota"
            action="{{ route('frota.update', $frota->frota_id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label for="nome" class="block text-sm font-semibold text-gray-700">Nome</label>
                <input type="text" id="nome" name="nome"
                    value="{{ old('nome', $frota->nome) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block text-sm font-semibold text-gray-700">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descricao', $frota->descricao) }}</textarea>
                @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Veículos --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Veículos (opcional)</label>
                <div class="mt-2 flex rounded-lg border border-gray-300 shadow-sm overflow-hidden">
                    <div class="flex-grow px-3 py-2 text-sm text-gray-600 bg-white">
                        @php
                        $veiculosSelecionados = (array) request('veiculos', old('veiculos', $frota->veiculos->pluck('veiculo_id')->all()));
                        $nomesVeiculos = \App\Models\Veiculo::whereIn('veiculo_id', $veiculosSelecionados)
                        ->pluck('modelo')->toArray();
                        @endphp

                        @if(count($nomesVeiculos))
                        {{ implode(', ', $nomesVeiculos) }}
                        @foreach($veiculosSelecionados as $vId)
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

            {{-- Responsáveis --}}
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

                <ul id="responsaveis_lista" class="mt-3 space-y-2">
                    {{-- Se veio da query string ou do old() --}}
                    @if(request()->has('responsaveis') || old('responsaveis'))
                    @foreach((array) request('responsaveis', old('responsaveis', [])) as $respId)
                    @php $user = \App\Models\User::find($respId); @endphp
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
                    {{-- Se não veio nada, carrega responsáveis vinculados na frota --}}
                    @elseif(($frota->responsavel ?? collect())->count())
                    @foreach($frota->responsavel as $user)
                    <li id="responsavel_{{ $user->id }}" class="flex justify-between items-center bg-gray-100 p-2 rounded-lg">
                        <span>✅ {{ $user->name }} ({{ $user->email }})</span>
                        <button type="button"
                            onclick="removerResponsavel(parseInt('{{ $user->id }}'))"
                            class="text-red-600 font-bold">✖</button>
                        <input type="hidden" name="responsaveis[]" value="{{ $user->id }}">
                    </li>
                    @endforeach
                    @endif
                </ul>

            </div>

            {{-- Convites pendentes --}}
            @if(!empty($convitesPendentes) && count($convitesPendentes))
            <h3 class="text-lg font-semibold mt-6 mb-2 flex items-center gap-2">
                ✉️ Convites pendentes
            </h3>
            <ul class="space-y-2" id="lista_convites_pendentes">
                @foreach($convitesPendentes as $n)
                <li id="convite_{{ $n->notcodigo }}"
                    class="flex items-start justify-between rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-2">
                    <div class="text-sm">
                        <div class="font-medium">
                            {{ $n->destinatario->name ?? 'Usuário' }}
                            <span class="text-gray-600">({{ $n->destinatario->email ?? '—' }})</span>
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">Pendente</span>
                            @if($n->data_envio)
                            <span class="text-xs text-gray-500">
                                enviado em {{ \Carbon\Carbon::parse($n->data_envio)->format('d/m/Y H:i') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <button type="button"
                        class="text-red-600 hover:text-red-700 font-medium btn-cancelar-convite"
                        data-id="{{ $n->notcodigo }}"
                        data-url="{{ route('notificacao.cancelar', $n) }}">
                        Cancelar
                    </button>
                </li>
                @endforeach
            </ul>
            @endif

            {{-- Foto --}}
            <div>
                <label for="foto" class="block text-sm font-semibold text-gray-700">Foto da Frota</label>
                @if($frota->foto)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $frota->foto) }}" alt="Foto atual" class="h-32 rounded-lg">
                </div>
                @endif
                <input type="file" id="foto" name="foto"
                    class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                           file:rounded-full file:border-0 file:text-sm file:font-semibold
                           file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Visibilidade --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Visibilidade</label>
                <div class="flex gap-4">
                    <label class="flex-1">
                        <input type="radio" name="visibilidade" value="1"
                            class="hidden peer"
                            {{ (string) old('visibilidade', $frota->visibilidade) === '1' ? 'checked' : '' }}>
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
                            {{ (string) old('visibilidade', $frota->visibilidade) === '0' ? 'checked' : '' }}>
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

            {{-- Botões --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('frota.index') }}"
                    class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
                    ← Voltar
                </a>
                <button type="submit"
                    class="px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">
                    💾 Atualizar Frota
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('click', (ev) => {
        const btn = ev.target.closest('.btn-cancelar-convite');
        if (!btn) return;

        const id = Number(btn.dataset.id);
        const url = btn.dataset.url;
        cancelarConvite(id, url);
    });

    function abrirSelecaoVeiculos() {
        const params = new URLSearchParams();
        params.set('origemCampoExterno', '1');
        const nome = document.querySelector('[name="nome"]')?.value || '';
        const descricao = document.querySelector('[name="descricao"]')?.value || '';
        const visibilidade = document.querySelector('[name="visibilidade"]:checked')?.value || '';
        const frotaId = "{{ $frota->frota_id }}";

        if (nome) params.set('nome', nome);
        if (descricao) params.set('descricao', descricao);
        if (visibilidade) params.set('visibilidade', visibilidade);
        if (frotaId) params.set('frota_id', frotaId);

        document.querySelectorAll('[name="veiculos[]"]').forEach(input => {
            params.append('veiculos[]', input.value);
        });
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
            const resp = await fetch(`{{ route('usuario.buscar') }}?email=${encodeURIComponent(email)}`);
            const data = await resp.json();

            if (!data.success) {
                feedback.textContent = data.message || "Usuário não encontrado.";
                feedback.className = "text-red-600 text-sm mt-2";
                return;
            }

            if (document.getElementById(`responsavel_${data.user.id}`)) {
                feedback.textContent = "⚠️ Esse responsável já foi adicionado.";
                feedback.className = "text-yellow-600 text-sm mt-2";
                return;
            }

            const li = document.createElement('li');
            li.className = 'flex justify-between items-center bg-gray-100 p-2 rounded-lg';
            li.id = `responsavel_${data.user.id}`;
            li.innerHTML = `
                <span>✅ ${data.user.name} (${data.user.email})</span>
                <button type="button" class="text-red-600 font-bold"
                        onclick="removerResponsavel(${data.user.id})">✖</button>
                <input type="hidden" name="responsaveis[]" value="${data.user.id}">
            `;
            lista.appendChild(li);

            feedback.textContent = '';
            emailInput.value = '';
        } catch (err) {
            feedback.textContent = "⚠️ Erro na busca. Verifique a conexão.";
            feedback.className = "text-red-600 text-sm mt-2";
        }
    }

    function removerResponsavel(id) {
        const li = document.getElementById(`responsavel_${id}`);
        if (li) li.remove();
    }

    async function cancelarConvite(id, url) {
        if (!confirm('Cancelar este convite?')) return;

        try {
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            });

            if (!resp.ok) {
                const txt = await resp.text();
                alert('Falha ao cancelar o convite.\n\n' + txt);
                return;
            }

            const data = await resp.json().catch(() => ({}));
            if (data.success === false) {
                alert(data.message || 'Não foi possível cancelar.');
                return;
            }

            const li = document.getElementById('convite_' + id);
            if (li) li.remove();
        } catch (e) {
            alert('Erro ao cancelar o convite.');
        }
    }
</script>
@endsection