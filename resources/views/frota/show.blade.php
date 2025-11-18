@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👁 Detalhes da Frota</h1>

    {{-- Foto --}}
    @if($frota->foto)
    <img src="{{ asset('storage/' . $frota->foto) }}"
        alt="Foto da frota"
        class="w-full h-52 sm:h-60 object-cover rounded-lg mb-6">
    @endif

    {{-- Infos básicas --}}
    <div class="space-y-1 mb-6 text-sm sm:text-base">
        <p><strong>Nome:</strong> {{ $frota->nome }}</p>
        <p><strong>Descrição:</strong> {{ $frota->descricao ?? '—' }}</p>
        <p>
            <strong>Visibilidade:</strong>
            <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                {{ $frota->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $frota->visibilidade == 1 ? 'Público' : 'Privado' }}
            </span>
        </p>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Dono da frota --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">👑 Dono da frota</h2>
            <div class="flex flex-col text-gray-800 text-sm">
                <span class="font-medium">{{ $frota->dono->name ?? '—' }}</span>
                <span class="text-gray-500 mb-1">({{ $frota->dono->email ?? '—' }})</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 w-fit">Proprietário</span>
            </div>
        </div>

        {{-- Responsáveis ativos --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">👥 Responsáveis ativos</h2>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                @forelse($frota->responsavel ?? [] as $user)
                <div class="flex flex-col justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full sm:w-[48%] shadow-sm">
                    <span class="font-medium text-sm leading-tight">{{ $user->name }}</span>
                    <span class="text-gray-500 text-xs truncate">{{ $user->email }}</span>
                    <span class="mt-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 w-fit">Ativo</span>
                </div>
                @empty
                <p class="text-gray-600 italic text-sm">Nenhum responsável ainda.</p>
                @endforelse
            </div>
        </div>

        {{-- Convites pendentes --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">✉️ Convites pendentes</h2>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                @forelse($convitesPendentes ?? [] as $n)
                <div class="flex flex-col justify-between bg-yellow-50 border border-yellow-100 rounded-lg px-3 py-2 w-full sm:w-[48%] shadow-sm">
                    <span class="font-medium text-sm">{{ $n->destinatario->name ?? 'Usuário' }}</span>
                    <span class="text-gray-500 text-xs truncate">{{ $n->destinatario->email ?? '—' }}</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">Pendente</span>
                        @if($n->data_envio)
                        <span class="text-gray-500 text-[10px] flex items-center gap-1">
                            📅 {{ \Carbon\Carbon::parse($n->data_envio)->format('d/m/Y H:i') }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-gray-600 italic text-sm">Sem convites pendentes.</p>
                @endforelse
            </div>
        </div>

        {{-- Convites respondidos --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">🗂️ Convites respondidos</h2>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                @forelse($convitesRespondidos ?? [] as $n)
                <div class="flex flex-col justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full sm:w-[48%] shadow-sm">
                    <span class="font-medium text-sm">{{ $n->destinatario->name ?? 'Usuário' }}</span>
                    <span class="text-gray-500 text-xs truncate">{{ $n->destinatario->email ?? '—' }}</span>
                    <div class="flex items-center justify-between mt-1">
                        @if($n->status === \App\Models\Notificacao::STATUS_ACEITO)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Aceito</span>
                        @elseif($n->status === \App\Models\Notificacao::STATUS_RECUSADO)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Recusado</span>
                        @endif
                        @if($n->data_resposta)
                        <span class="text-gray-500 text-[10px] flex items-center gap-1">
                            📅 {{ \Carbon\Carbon::parse($n->data_resposta)->format('d/m/Y H:i') }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-gray-600 italic text-sm">Nenhum registro de resposta.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 🚘 Veículos da frota --}}
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-3 flex items-center justify-center gap-2 text-center">
            🚘 Veículos dessa frota
            <span class="text-sm text-gray-500 font-normal">
                ({{ $frota->veiculos->count() }} {{ Str::plural('veículo', $frota->veiculos->count()) }})
            </span>
        </h2>

        {{-- Mensagem se não houver veículos --}}
        @if($frota->veiculos->isEmpty())
        <p class="text-gray-600 italic text-sm mb-6 text-center">Nenhum veículo vinculado.</p>
        @endif

        {{-- Botões centralizados --}}
        <div class="flex justify-center items-center gap-4 mt-6">
            <a href="{{ route('frota.veiculos.index', ['frota' => $frota->frota_id,'from_publico' => request('from_publico')]) }}"
                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition text-sm flex items-center gap-2">
                🚗 Ver Veículos
            </a>

<a href="{{ route('frota.gasto.index', [
        'frota' => $frota->frota_id,
        'from_publico' => request('from_publico')
    ]) }}"
    class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition text-sm flex items-center gap-2">
    💰 Ver Gastos
</a>

        </div>
    </div>

{{-- 🔙 Voltar --}}
@php
    $fromPublico = request()->boolean('from_publico');

    if ($fromPublico) {
        $backUrl = route('publico.index');
        $backLabel = '← Voltar para Consulta Pública';
    } else {
        $backUrl = route('frota.index');
        $backLabel = '← Voltar';
    }
@endphp

<div class="flex justify-start mt-10">
    <a href="{{ $backUrl }}"
        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm sm:text-base">
        {{ $backLabel }}
    </a>
</div>

</div>
@endsection
