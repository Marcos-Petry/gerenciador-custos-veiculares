@extends('layouts.app')

@section('content')
<div class="py-8 px-6">

    <!-- Título -->
    <h1 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        🚛 Minhas Frotas
    </h1>

    <!-- 🔹 Filtros -->
    <form method="GET" action="{{ route('frota.index') }}" class="flex flex-wrap items-end gap-3 mb-4">

        <!-- Campo -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Campo</label>
            <select name="campo" class="rounded-lg border-gray-300 px-3 py-1.5 w-44">
                <option value="nome" {{ request('campo') == 'nome' ? 'selected' : '' }}>Nome</option>
                <option value="descricao" {{ request('campo') == 'descricao' ? 'selected' : '' }}>Descrição</option>
            </select>
        </div>

        <!-- Operador -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Operador</label>
            <select name="operador" class="rounded-lg border-gray-300 px-3 py-1.5 w-48">
                <option value="=" {{ request('operador') == '=' ? 'selected' : '' }}>Igual a (=)</option>
                <option value="like" {{ request('operador') == 'like' ? 'selected' : '' }}>Contém</option>
            </select>
        </div>

        <!-- Valor -->
        <div class="flex-1 max-w-md">
            <label class="block text-white text-sm font-semibold mb-1">Valor</label>
            <input type="text" name="valor" value="{{ request('valor') }}" placeholder="Digite o valor"
                class="rounded-lg border-gray-300 px-3 py-1.5 w-full">
        </div>

        <!-- Visibilidade -->
        <div>
            <label class="block text-white text-sm font-semibold mb-1">Visibilidade</label>
            <select name="visibilidade" class="rounded-lg border-gray-300 px-3 py-1.5 w-40">
                <option value="">Todas</option>
                <option value="1" {{ request('visibilidade') == '1' ? 'selected' : '' }}>Públicas</option>
                <option value="0" {{ request('visibilidade') == '0' ? 'selected' : '' }}>Privadas</option>
            </select>
        </div>

        <!-- Botões -->
        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Filtrar
            </button>
            <a href="{{ route('frota.index') }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Limpar
            </a>
        </div>
    </form>

    <!-- 🔹 Botão Nova Frota -->
    <div class="mb-6">
        <a href="{{ route('frota.create') }}"
            class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-semibold">
            ➕ Nova Frota
        </a>
    </div>

    <!-- 🔹 Grid de Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($frotas as $frota)
        <div
            class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition hover:shadow-2xl hover:-translate-y-2 hover:scale-105">

            <!-- Foto -->
            @if($frota->foto)
            <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/' . $frota->foto) }}" alt="Foto da frota"
                    class="h-full w-auto object-cover">
            </div>
            @else
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 text-gray-400 text-5xl">
                🚛
            </div>
            @endif

            <!-- Conteúdo -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $frota->nome }}</h2>
                <p class="text-sm text-gray-600">Descrição: {{ $frota->descricao ?? '—' }}</p>
                <p class="text-sm text-gray-600">Veículos: {{ $frota->veiculos->count() }}</p>

                <!-- Badge de Visibilidade -->
                <p class="text-sm mt-2">
                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium
                        {{ $frota->visibilidade == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $frota->visibilidade == 1 ? 'Público' : 'Privado' }}
                    </span>
                </p>

                <!-- Ações -->
                <div class="flex justify-center gap-5 mt-5 text-sm font-medium">
                    <a href="{{ route('frota.show', $frota) }}"
                        class="text-blue-600 hover:underline flex items-center gap-1">
                        👁 Ver
                    </a>

                    @if(isset($frota->ehDono) && $frota->ehDono)
                    <a href="{{ route('frota.edit', $frota) }}"
                        class="text-yellow-600 hover:underline flex items-center gap-1">
                        ✏ Editar
                    </a>

                    <form action="{{ route('frota.destroy', $frota) }}" method="POST"
                        onsubmit="return confirm('Excluir frota?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline flex items-center gap-1">
                            🗑 Excluir
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-3 text-center text-gray-500">Nenhuma frota cadastrada.</p>
        @endforelse
    </div>

    <!--Paginação com contador -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-700 gap-2">

        <!-- Total de registros -->
        <div class="bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/30 shadow-sm">
            Exibindo <strong>{{ $frotas->count() }}</strong> de <strong>{{ $frotas->total() }}</strong> registros
        </div>

        <!-- Links de paginação -->
        <div>
            {{ $frotas->onEachSide(1)->links() }}
        </div>

    </div>


</div>
@endsection