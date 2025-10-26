@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👁 Detalhes do Veículo</h1>

    {{-- Foto --}}
    @if($veiculo->foto)
    <img src="{{ asset('storage/' . $veiculo->foto) }}"
        alt="Foto do veículo"
        class="w-full h-52 sm:h-60 object-cover rounded-lg mb-6">
    @endif

    {{-- Infos básicas --}}
    <div class="space-y-1 mb-6 text-sm sm:text-base">
        <p><strong>Modelo:</strong> {{ $veiculo->modelo }}</p>
        <p><strong>Placa:</strong> {{ $veiculo->placa }}</p>
        <p><strong>Ano:</strong> {{ $veiculo->ano }}</p>
        <p><strong>Frota:</strong> {{ $veiculo->frota?->nome ?? '—' }}</p>
        <p>
            <strong>Visibilidade:</strong>
            <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                {{ $veiculo->getVisibilidade() === 'Público' ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $veiculo->getVisibilidade() }}
            </span>
        </p>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Dono do veículo --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">👑 Dono do veículo</h2>
            <div class="flex flex-col text-gray-800 text-sm">
                <span class="font-medium">{{ $veiculo->dono->name ?? '—' }}</span>
                <span class="text-gray-500 mb-1">({{ $veiculo->dono->email ?? '—' }})</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 w-fit">Proprietário</span>
            </div>
        </div>

        {{-- Responsáveis ativos --}}
        <div class="border rounded-xl p-4 shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold mb-2 flex items-center gap-1">👥 Responsáveis ativos</h2>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                @forelse($veiculo->responsavel ?? [] as $user)
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

    {{-- Voltar --}}
    @php
        $fromFrota = request('from_frota');
        $backUrl = $fromFrota
            ? route('frota.veiculos.index', $fromFrota)
            : route('veiculo.index');
        $backLabel = $fromFrota ? '← Voltar à Veículos' : '← Voltar para Veículos';
    @endphp

    <div class="flex justify-start mt-8">
        <a href="{{ $backUrl }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm sm:text-base">
            {{ $backLabel }}
        </a>
    </div>
</div>
@endsection
