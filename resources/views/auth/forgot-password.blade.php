<x-guest-layout>
    <!-- Card branco -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Esqueceu sua senha?</h1>
            <p class="text-gray-600 text-sm mt-2">
                Sem problemas! Informe seu e-mail e enviaremos um link para redefinir sua senha.
            </p>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Botão -->
            <div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow font-medium">
                    Enviar link de redefinição
                </button>
            </div>
        </form>

        <!-- Voltar -->
        <p class="mt-6 text-sm text-center text-gray-600">
            Lembrou sua senha?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">Entrar</a>
        </p>
    </div>
</x-guest-layout>
