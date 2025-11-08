@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">📬 Contato</h1>

    <p class="text-gray-700 mb-6">
        Em caso de dúvidas, sugestões ou solicitações, entre em contato conosco preenchendo o formulário abaixo.
    </p>

    <form action="#" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="nome" class="block text-gray-700 font-medium mb-1">Nome</label>
            <input type="text" id="nome" name="nome" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">E-mail</label>
            <input type="email" id="email" name="email" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="mensagem" class="block text-gray-700 font-medium mb-1">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="5" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Enviar Mensagem
        </button>
    </form>
</div>
@endsection