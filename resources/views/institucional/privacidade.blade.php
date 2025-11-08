@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">📄 Política de Privacidade</h1>
    <p class="text-gray-700 leading-relaxed mb-4">
        Esta política descreve como o sistema <strong>GestoCar</strong> coleta, utiliza e protege as informações dos usuários.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">1. Coleta de Informações</h2>
    <p class="text-gray-700 mb-3">
        O sistema armazena apenas os dados necessários para o funcionamento, como nome, e-mail, veículos e gastos cadastrados.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">2. Uso das Informações</h2>
    <p class="text-gray-700 mb-3">
        As informações são utilizadas exclusivamente para gerenciamento de veículos e frotas, não sendo compartilhadas com terceiros.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">3. Segurança</h2>
    <p class="text-gray-700 mb-3">
        São adotadas boas práticas de segurança e autenticação para garantir a integridade e confidencialidade dos dados.
    </p>

    <p class="text-gray-700 mt-6">
        Em caso de dúvidas, entre em contato através da página <a href="{{ route('contato') }}" class="text-blue-600 underline">Contato</a>.
    </p>
</div>
@endsection