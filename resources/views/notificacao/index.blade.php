@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">🔔 Minhas Notificações</h1>

    <!-- Filtros -->
    <div class="flex gap-2 mb-6">
        <a href="{{ route('notificacao.index', ['status' => 'pendentes']) }}"
            class="px-4 py-2 rounded-lg {{ $filtro == 'pendentes' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            Pendentes
        </a>
        <a href="{{ route('notificacao.index', ['status' => 'aceitas']) }}"
            class="px-4 py-2 rounded-lg {{ $filtro == 'aceitas' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            Aceitas
        </a>
        <a href="{{ route('notificacao.index', ['status' => 'recusadas']) }}"
            class="px-4 py-2 rounded-lg {{ $filtro == 'recusadas' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            Recusadas
        </a>
        <a href="{{ route('notificacao.avisos') }}"
            class="px-4 py-2 rounded-lg {{ $filtro == 'avisos' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            Avisos
        </a>
        <a href="{{ route('notificacao.index', ['status' => 'todas']) }}"
            class="px-4 py-2 rounded-lg {{ $filtro == 'todas' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
            Todas
        </a>
    </div>


    <!-- Lista -->
    <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
        @forelse($notificacoes as $notif)
        @include('notificacao._item', ['notif' => $notif])
        @empty
        <div class="p-4 text-gray-500">Nenhuma notificação encontrada.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notificacoes->links() }}
    </div>
</div>
@endsection