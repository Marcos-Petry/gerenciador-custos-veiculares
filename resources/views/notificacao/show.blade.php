@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-xl font-bold mb-6">Detalhes da Notificação</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <p class="mb-4 text-gray-800">{{ $notificacao->mensagem ?? 'Sem mensagem' }}</p>
        <p class="text-sm text-gray-500">
            {{ $notif->created_at->diffForHumans() }}
        </p>

        <p class="text-sm text-gray-500">Status:
            @if($notificacao->status == 0)
            Pendente
            @elseif($notificacao->status == 1)
            Aceita
            @else
            Recusada
            @endif
        </p>
    </div>

    <div class="mt-4">
        <a href="{{ route('notificacao.index') }}" class="text-blue-600 hover:underline">← Voltar para lista</a>
    </div>
</div>
@endsection