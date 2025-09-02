<x-guest-layout>
    <!-- Card branco -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
            <p class="text-gray-600 text-sm mt-2">
                Acesse sua conta e continue gerenciando seus
                <span class="text-indigo-600 font-semibold">gastos veiculares</span>
            </p>
        </div>

        @if ($errors->any())
        <div class="mb-4 p-4 rounded-md bg-red-100 border border-red-300 text-red-700 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        <!-- Formulário -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Senha -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Lembrar -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
                </label>

                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    Esqueceu sua senha?
                </a>
                @endif
            </div>

            <!-- Botão -->
            <div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow font-medium">
                    Entrar
                </button>
            </div>
        </form>

        <!-- Criar conta -->
        <p class="mt-6 text-sm text-center text-gray-600">
            Não tem conta?
            <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Crie agora</a>
        </p>
    </div>
</x-guest-layout>