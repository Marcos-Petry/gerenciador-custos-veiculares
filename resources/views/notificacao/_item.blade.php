<div class="p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3
    @if($notif->status == 1) bg-green-50
    @elseif($notif->status == 2) bg-red-50
    @endif rounded-lg">

    <!-- Bloco de texto -->
    <div class="text-gray-800 leading-relaxed text-sm sm:text-base flex-1">
        @if($notif->tipo == 1)
            <p>
                🚗 <span class="font-semibold">Convite de veículo</span><br>
                Você foi convidado para ser responsável pelo veículo:
                <span class="font-medium text-blue-800">{{ $notif->veiculo->modelo ?? 'Desconhecido' }}</span>
            </p>
        @elseif($notif->tipo == 2)
            <p>
                🚛 <span class="font-semibold">Convite de frota</span><br>
                Você foi convidado para participar da frota:
                <span class="font-medium text-blue-800">{{ $notif->frota->nome ?? 'Desconhecida' }}</span>
            </p>
        @else
            <p>{{ $notif->mensagem }}</p>
        @endif

        @if($notif->remetente)
            <p class="text-sm text-gray-600 mt-2">
                Enviado por
                <span class="font-semibold uppercase">{{ $notif->remetente->name }}</span>
                <span class="text-gray-500 lowercase">({{ $notif->remetente->email }})</span>
            </p>
        @endif

        <p class="text-xs text-gray-500 mt-1">
            {{ $notif->created_at->diffForHumans() }}
        </p>
    </div>

    <!-- Bloco de ações -->
    @if($notif->status == 0 && in_array($notif->tipo, [1, 2]))
        <div class="flex flex-wrap sm:flex-nowrap justify-end gap-2 w-full sm:w-auto">
            <form method="POST" action="{{ route('notificacao.aceitar', $notif->notcodigo) }}">
                @csrf
                <button
                    class="w-full sm:w-auto px-4 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                    Aceitar
                </button>
            </form>
            <form method="POST" action="{{ route('notificacao.recusar', $notif->notcodigo) }}">
                @csrf
                <button
                    class="w-full sm:w-auto px-4 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    Recusar
                </button>
            </form>
        </div>
    @elseif($notif->status == 1)
        <span class="text-green-600 font-semibold">✅ Aceito</span>
    @elseif($notif->status == 2)
        <span class="text-red-600 font-semibold">❌ Recusado</span>
    @endif
</div>
