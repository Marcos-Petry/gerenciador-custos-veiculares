@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👁 Detalhes do Veículo</h1>

    {{-- Foto --}}
    @if($veiculo->foto)
    <img src="{{ asset('storage/' . $veiculo->foto) }}"
        alt="Foto do veículo"
        class="w-full h-60 object-cover rounded-lg mb-6">
    @endif

    {{-- Infos básicas --}}
    <div class="space-y-1">
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

    {{-- Responsáveis ativos --}}
    <h2 class="text-xl font-semibold mt-8 mb-2">👤 Responsáveis ativos</h2>
    <ul class="list-disc ml-6">
        @forelse($veiculo->responsavel ?? [] as $user)
        <li>
            {{ $user->name }} ({{ $user->email }})
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 align-middle">
                Ativo
            </span>
        </li>
        @empty
        <li>Nenhum responsável ainda.</li>
        @endforelse
    </ul>

    {{-- Convites pendentes --}}
    <h2 class="text-xl font-semibold mt-8 mb-2">✉️ Convites pendentes</h2>
    <ul class="list-disc ml-6">
        @forelse($convitesPendentes ?? [] as $n)
        <li>
            {{ $n->destinatario->name ?? 'Usuário' }} ({{ $n->destinatario->email ?? '—' }})
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700 align-middle">
                Pendente
            </span>
            @if($n->data_envio)
            <span class="ml-2 text-gray-500 text-xs align-middle">
                enviado em {{ \Carbon\Carbon::parse($n->data_envio)->format('d/m/Y H:i') }}
            </span>
            @endif
        </li>
        @empty
        <li>Sem convites pendentes.</li>
        @endforelse
    </ul>

    {{-- Convites respondidos --}}
    <h2 class="text-xl font-semibold mt-8 mb-2">🗂️ Convites respondidos</h2>
    <ul class="list-disc ml-6">
        @forelse($convitesRespondidos ?? [] as $n)
        <li>
            {{ $n->destinatario->name ?? 'Usuário' }} ({{ $n->destinatario->email ?? '—' }})
            @if($n->status === \App\Models\Notificacao::STATUS_ACEITO)
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 align-middle">
                Aceito
            </span>
            @elseif($n->status === \App\Models\Notificacao::STATUS_RECUSADO)
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 align-middle">
                Recusado
            </span>
            @endif
            @if($n->data_resposta)
            <span class="ml-2 text-gray-500 text-xs align-middle">
                em {{ \Carbon\Carbon::parse($n->data_resposta)->format('d/m/Y H:i') }}
            </span>
            @endif
        </li>
        @empty
        <li>Nenhum registro.</li>
        @endforelse
    </ul>

    {{-- Voltar --}}
    @php
    $fromFrota = request('from_frota');
    $backUrl = $fromFrota
    ? route('frota.veiculos.index', $fromFrota)
    : route('veiculo.index');
    $backLabel = $fromFrota ? '← Voltar à Veículos' : '← Voltar para Veículos';
    @endphp

    <div class="flex justify-start mt-8">
        <a href="{{ $backUrl }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
            {{ $backLabel }}
        </a>
    </div>
</div>
@endsection