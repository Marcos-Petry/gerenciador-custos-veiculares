<div class="p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3
    @if($notif->status == 1) bg-green-50
    @elseif($notif->status == 2) bg-red-50
    @elseif($notif->status == 3) bg-yellow-50
    @else bg-white
    @endif rounded-lg shadow-sm border border-gray-200">

    <!-- Bloco de texto -->
    <div class="text-gray-800 leading-relaxed text-sm sm:text-base flex-1">

        {{-- Convite de veículo --}}
        @if($notif->tipo == \App\Models\Notificacao::TIPO_CONVITE_VEICULO)
        🚗 <span class="font-semibold">Convite de veículo</span><br>
        Você foi convidado para ser responsável pelo veículo:
        <span class="font-medium text-blue-800">{{ $notif->veiculo->modelo ?? 'Desconhecido' }}</span>

        {{-- Convite de frota --}}
        @elseif($notif->tipo == \App\Models\Notificacao::TIPO_CONVITE_FROTA)
        🚛 <span class="font-semibold">Convite de frota</span><br>
        Você foi convidado para participar da frota:
        <span class="font-medium text-blue-800">{{ $notif->frota->nome ?? 'Desconhecida' }}</span>

        {{-- Avisos de sistema (tipo 3) - inclui frota e veículo removido --}}
        @elseif($notif->tipo == 3)
        ⚠️ <span class="font-semibold text-yellow-700">Aviso de sistema</span><br>

        @php
            $remetente = $notif->remetente ? $notif->remetente->name : 'Um usuário';
            $frotaNome = $notif->frota?->nome;
            $veiculoNome = $notif->veiculo?->modelo;
        @endphp

        {{-- Se veio de frota --}}
        @if($frotaNome)
        O usuário <span class="font-semibold text-blue-800">{{ $remetente }}</span>
        deixou a frota <span class="font-semibold text-blue-800">{{ $frotaNome }}</span>.

        {{-- Se veio de veículo --}}
        @elseif($veiculoNome)
        Você foi removido como responsável do veículo
        <span class="font-semibold text-blue-800">{{ $veiculoNome }}</span>
        por <span class="font-semibold text-blue-800">{{ $remetente }}</span>.

        {{-- Caso genérico --}}
        @else
        Nova notificação recebida.
        @endif

        {{-- Outros tipos genéricos --}}
        @else
        🔔 <span class="font-semibold">Notificação</span><br>
        Nova notificação recebida.
        @endif


        {{-- Dados do remetente --}}
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
    <div class="flex flex-wrap sm:flex-nowrap justify-end gap-2 w-full sm:w-auto">

        {{-- Convites de veículo/frota --}}
        @if($notif->status == 0 && in_array($notif->tipo, [1, 2]))
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

        {{-- Avisos de sistema (tipo 3) --}}
        @elseif($notif->status == 0 && $notif->tipo == 3)
        <form method="POST" action="{{ route('notificacao.lida', $notif->notcodigo) }}">
            @csrf
            <button
                class="px-4 py-1.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
                Marcar como lido
            </button>
        </form>

        {{-- Status já definidos --}}
        @elseif($notif->status == 1)
        <span class="text-green-600 font-semibold flex items-center gap-1">✅ Aceito</span>

        @elseif($notif->status == 2)
        <span class="text-red-600 font-semibold flex items-center gap-1">❌ Recusado</span>

        @elseif($notif->status == 3)
        <span class="text-yellow-700 font-semibold flex items-center gap-1">✅ Lido</span>
        @endif
    </div>
</div>
