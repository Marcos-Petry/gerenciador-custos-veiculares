@extends('layouts.app')

@section('content')
<div class="py-8 px-6">
    <h2 class="text-2xl font-bold mb-6">💰 Gastos da Frota: {{ $frota->nome }}</h2>

    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <p><strong>Total de Veículos:</strong> {{ $frota->veiculos->count() }}</p>
        <p><strong>Dono:</strong> {{ $frota->dono->name ?? '—' }}</p>
    </div>

    <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Veículo</th>
                    <th class="px-4 py-2">Categoria</th>
                    <th class="px-4 py-2">Descrição</th>
                    <th class="px-4 py-2">Valor</th>
                    <th class="px-4 py-2">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $gasto->veiculo->modelo ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $gasto->categoria }}</td>
                    <td class="px-4 py-2">{{ $gasto->descricao }}</td>
                    <td class="px-4 py-2 font-semibold">R$ {{ number_format($gasto->valor, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Nenhum gasto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $gastos->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('frota.show', $frota->frota_id) }}"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
            ← Voltar à Frota
        </a>
    </div>
</div>
@endsection