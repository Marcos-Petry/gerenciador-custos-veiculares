{{-- resources/views/gastos/linha-tempo.blade.php --}}
@extends('layouts.app')

@section('content')
@php
// total para numerar de baixo para cima (1 embaixo)
$total = $gastos->count();
@endphp

<div class="py-10 px-4 sm:px-8">
    <!-- Voltar -->
    <a href="{{ url()->previous() }}"
        class="inline-flex items-center gap-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition mb-8">
        ← Voltar
    </a>

    <!-- Título -->
    <h2 class="text-2xl font-bold text-white text-center mb-12">
        🕒 Linha do Tempo de Gastos
        @if(isset($veiculo))
        — {{ $veiculo->modelo }} ({{ $veiculo->placa }})
        @elseif(isset($frota))
        — {{ $frota->nome }}
        @endif
    </h2>

    @if($gastos->isEmpty())
    <p class="text-center text-gray-300 text-lg">Nenhum gasto registrado.</p>
    @else
    <div class="timeline max-w-6xl mx-auto">
        <!-- Linha central -->
        <div class="timeline__line"></div>

        <!-- Itens -->
        @foreach($gastos as $idx => $gasto)
        @php
        // número mostrado (1 embaixo, crescendo pra cima)
        $numero = $total - $loop->index;

        // cor por categoria
        $categoria = strtolower($gasto->categoriaTexto());
        $corCategoria = match(true) {
        str_contains($categoria, 'combust') => '#16a34a', // verde
        str_contains($categoria, 'impost') => '#f97316', // laranja
        str_contains($categoria, 'manuten') => '#eab308', // amarelo
        str_contains($categoria, 'segur') => '#9333ea', // roxo
        default => '#2563eb', // azul padrão
        };
        @endphp

        <!-- Definimos a cor como CSS variable (sem avisos no VS Code) -->
        <div class="timeline__item" style="--cat: {{ $corCategoria }};">
            <!-- ponto/dot -->
            <div class="timeline__dot">
                {{ $numero }}
            </div>

            <!-- card -->
            <div class="timeline__card rounded-2xl shadow-md p-5 hover:shadow-lg transition">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold timeline__cat">
                        {{ $gasto->categoriaTexto() }}
                    </span>
                    <span class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}
                    </span>
                </div>

                <p class="text-gray-800 mb-2">{{ $gasto->descricao ?? '—' }}</p>

                <p class="text-gray-700 font-bold mb-1">
                    💰 R$ {{ number_format($gasto->valor, 2, ',', '.') }}
                </p>

                @if(isset($gasto->veiculo))
                <p class="text-gray-600 text-sm">
                    🚗 {{ $gasto->veiculo->modelo }} ({{ $gasto->veiculo->placa }})
                </p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
    /* ===== CONTAINER EM GRID ===== */
    .timeline {
        position: relative;
        display: grid;
        row-gap: 3rem;
    }

    /* ===== LINHA CENTRAL ===== */
    .timeline__line {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 4px;
        transform: translateX(-50%);
        background: #2563eb;
        border-radius: 2px;
    }

    /* ===== ITEM ===== */
    .timeline__item {
        display: grid;
        grid-template-columns: 1fr 40px 1fr;
        position: relative;
        /* cor da categoria vem daqui (inline) */
        /* ex: style="--cat:#16a34a;" */
    }

    /* ===== CARD ===== */
    .timeline__card {
        width: 20rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 1.25rem;
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid var(--cat);
        /* <— usa a variável */
    }

    .timeline__card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* título da categoria colorido */
    .timeline__cat {
        color: var(--cat);
    }

    /* ===== PONTO CENTRAL ===== */
    .timeline__dot {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        height: 36px;
        width: 36px;
        border-radius: 999px;
        color: #fff;
        border: 4px solid #ffffff;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        transition: all 0.3s ease;
        background-color: var(--cat);
        /* <— usa a variável */
    }

    /* ===== ALTERNÂNCIA DE BAIXO PRA CIMA ===== */
    .timeline__item:nth-last-child(odd) .timeline__card {
        grid-column: 1;
        justify-self: end;
        text-align: right;
        margin-right: 2.5rem;
    }

    .timeline__item:nth-last-child(even) .timeline__card {
        grid-column: 3;
        justify-self: start;
        text-align: left;
        margin-left: 2.5rem;
    }

    /* ===== LINHAS CONECTANDO DOT AO CARD ===== */
    .timeline__item::before {
        content: "";
        position: absolute;
        top: 50%;
        width: 40px;
        height: 2px;
        background: #2563eb33;
    }

    .timeline__item:nth-last-child(odd)::before {
        right: calc(50% + 18px);
    }

    .timeline__item:nth-last-child(even)::before {
        left: calc(50% + 18px);
    }

    /* ===== ANIMAÇÃO ===== */
    .timeline__item {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

@media (max-width: 768px) {

    .timeline__line {
        left: 50%;
        transform: translateX(-50%);
        width: 3px;
    }

    .timeline__item {
        grid-template-columns: 1fr 40px 1fr;
        row-gap: 4.5rem !important; /* mais espaço vertical */
        position: relative;
    }

    .timeline__dot {
        position: absolute !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;

        height: 22px !important;
        width: 22px !important;
        font-size: 0.68rem !important;

        z-index: 9999;
    }

    /* ======== CARD REDUZIDO AINDA MAIS ======== */
    .timeline__card {
        width: 8.8rem !important;     /* menor */
        padding: 0.48rem !important;  /* mais compacto */
        font-size: 0.72rem !important;
        border-radius: 0.60rem !important;

        min-height: 85px !important;
    }

    .timeline__card span,
    .timeline__card p {
        font-size: 0.70rem !important;
        line-height: 1rem !important;
    }

    /* ======== MAIS AFASTADO DA LINHA ======== */
    
    /* Esquerda */
    .timeline__item:nth-last-child(odd) .timeline__card {
        grid-column: 1;
        margin-right: 3.4rem !important; /* ERA 2.9 → agora 3.4 */
    }

    /* Direita */
    .timeline__item:nth-last-child(even) .timeline__card {
        grid-column: 3;
        margin-left: 3.4rem !important; /* idem */
    }

    .timeline__item::before {
        display: none !important;
    }
}



</style>
@endsection