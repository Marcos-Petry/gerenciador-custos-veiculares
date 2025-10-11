@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h1 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        ⚖️ Comparar Veículos Públicos
    </h1>

    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 max-w-6xl mx-auto">
        {{-- Formulário principal --}}
        <form method="GET" action="{{ route('publico.comparar') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="hidden" name="comparar" value="sim">


            {{-- Veículo A --}}
            <div>
                <label class="block text-white font-semibold mb-2">Veículo A</label>
                <div class="flex gap-2">
                    <input type="text"
                        value="{{ $dadosA ? $dadosA->modelo . ' (' . $dadosA->placa . ')' : '' }}"
                        placeholder="Selecione o veículo A"
                        class="w-full rounded-lg border-gray-300 text-gray-700 px-3 py-2 bg-gray-100" readonly>
                    <a href="{{ route('publico.index', [
                        'origemCampoExterno' => 'veiculoA',
                        'veiculoB' => $veiculoB
                    ]) }}"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">🔍</a>
                </div>
                <input type="hidden" name="veiculoA" value="{{ $veiculoA ?? ($dadosA->veiculo_id ?? '') }}">

                @if($dadosA)
                <div
                    class="mt-4 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30 p-4 text-white flex gap-4 items-center">
                    @if($dadosA->foto)
                    <img src="{{ asset('storage/' . $dadosA->foto) }}"
                        class="w-24 h-20 object-cover rounded-lg border border-white/40">
                    @else
                    <div class="w-24 h-20 flex items-center justify-center bg-white/20 rounded-lg text-3xl">🚗</div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-lg">{{ $dadosA->modelo }}</h3>
                        <p class="text-sm text-white/90">Placa: {{ $dadosA->placa }}</p>
                        <p class="text-sm text-white/90">Ano: {{ $dadosA->ano }}</p>
                        <p class="text-sm text-white/90">Frota: {{ $dadosA->frota->nome ?? '—' }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Veículo B --}}
            <div>
                <label class="block text-white font-semibold mb-2">Veículo B</label>
                <div class="flex gap-2">
                    <input type="text"
                        value="{{ $dadosB ? $dadosB->modelo . ' (' . $dadosB->placa . ')' : '' }}"
                        placeholder="Selecione o veículo B"
                        class="w-full rounded-lg border-gray-300 text-gray-700 px-3 py-2 bg-gray-100" readonly>
                    <a href="{{ route('publico.index', [
                        'origemCampoExterno' => 'veiculoB',
                        'veiculoA' => $veiculoA
                    ]) }}"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">🔍</a>
                </div>
                <input type="hidden" name="veiculoB" value="{{ $veiculoB ?? ($dadosB->veiculo_id ?? '') }}">

                @if($dadosB)
                <div
                    class="mt-4 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30 p-4 text-white flex gap-4 items-center">
                    @if($dadosB->foto)
                    <img src="{{ asset('storage/' . $dadosB->foto) }}"
                        class="w-24 h-20 object-cover rounded-lg border border-white/40">
                    @else
                    <div class="w-24 h-20 flex items-center justify-center bg-white/20 rounded-lg text-3xl">🚗</div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-lg">{{ $dadosB->modelo }}</h3>
                        <p class="text-sm text-white/90">Placa: {{ $dadosB->placa }}</p>
                        <p class="text-sm text-white/90">Ano: {{ $dadosB->ano }}</p>
                        <p class="text-sm text-white/90">Frota: {{ $dadosB->frota->nome ?? '—' }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Botão comparar --}}
            <div class="col-span-full flex justify-center mt-4">
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    Comparar
                </button>
            </div>
        </form>

        {{-- Resultado da comparação --}}
        @if($dadosA && $dadosB && request('comparar') === 'sim')
        <div class="mt-10 bg-white/30 backdrop-blur-md rounded-2xl border border-white/40 p-6 text-gray-900 shadow-lg">
            <h2 class="text-xl font-semibold mb-5 flex items-center gap-2 text-gray-800">
                Resultado da Comparação
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- 🚘 Veículo A --}}
                <div class="rounded-xl p-5 transition shadow
                    {{ $vencedor === 'A' ? 'bg-green-100 border-2 border-green-500' : 'bg-white/70 border border-gray-200' }}">
                    <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
                        <span>{{ $dadosA->modelo }}</span>
                    </h3>
                    <p class="text-gray-700 text-sm mb-1">Total de Gastos:
                        <strong class="text-gray-900">R$ {{ number_format($gastosA, 2, ',', '.') }}</strong>
                    </p>

                    <p class="text-gray-700 text-sm mt-2 mb-1 font-semibold">Categorias:</p>
                    <ul class="text-sm text-gray-700 space-y-1">
                        @forelse($categoriasA as $cat)
                        <li>• {{ $cat->categoria_nome }} —
                            <span class="font-semibold">R$ {{ number_format($cat->total, 2, ',', '.') }}</span>
                        </li>
                        @empty
                        <li>— Nenhum gasto registrado</li>
                        @endforelse
                    </ul>
                </div>

                {{--  Veículo B --}}
                <div class="rounded-xl p-5 transition shadow
                    {{ $vencedor === 'B' ? 'bg-green-100 border-2 border-green-500' : 'bg-white/70 border border-gray-200' }}">
                    <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
                        <span>{{ $dadosB->modelo }}</span>
                    </h3>
                    <p class="text-gray-700 text-sm mb-1">Total de Gastos:
                        <strong class="text-gray-900">R$ {{ number_format($gastosB, 2, ',', '.') }}</strong>
                    </p>

                    <p class="text-gray-700 text-sm mt-2 mb-1 font-semibold">Categorias:</p>
                    <ul class="text-sm text-gray-700 space-y-1">
                        @forelse($categoriasB as $cat)
                        <li>• {{ $cat->categoria_nome }} —
                            <span class="font-semibold">R$ {{ number_format($cat->total, 2, ',', '.') }}</span>
                        </li>
                        @empty
                        <li>— Nenhum gasto registrado</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Resultado final --}}
            <div class="mt-8 text-center text-lg font-semibold">
                @if($vencedor === 'A')
                O veículo <span class="text-green-600">{{ $dadosA->modelo }}</span> teve mais gastos totais.
                @elseif($vencedor === 'B')
                O veículo <span class="text-green-600">{{ $dadosB->modelo }}</span> teve mais gastos totais.
                @else
                ⚖️ Ambos os veículos possuem gastos iguais.
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection