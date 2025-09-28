<div class="p-4 flex justify-between items-center
    @if($notif->status == 1) bg-green-50
    @elseif($notif->status == 2) bg-red-50
    @endif">

    <div>
        <p class="text-gray-800">{{ $notif->mensagem ?? 'Nova notificação' }}</p>
        <p class="text-sm text-gray-500">{{ $notif->created_at->diffForHumans() }}</p>
    </div>

    @if($notif->status == 0 && in_array($notif->tipo, [1,2]))
    <div class="flex gap-2">
        <form method="POST" action="{{ route('notificacao.aceitar', $notif->notcodigo) }}">
            @csrf
            <button class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">Aceitar</button>
        </form>
        <form method="POST" action="{{ route('notificacao.recusar', $notif->notcodigo) }}">
            @csrf
            <button class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700">Recusar</button>
        </form>
    </div>
    @elseif($notif->status == 1)
    <span class="text-green-600 font-semibold">✅ Aceito</span>
    @elseif($notif->status == 2)
    <span class="text-red-600 font-semibold">❌ Recusado</span>
    @endif
</div>