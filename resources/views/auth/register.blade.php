<x-guest-layout>
    <!-- Card branco -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Crie sua conta</h1>
            <p class="text-gray-600 text-sm mt-2">
                Comece agora a gerenciar seus <span class="text-indigo-600 font-semibold">gastos veiculares</span>
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
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Nome + Sobrenome na mesma linha -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <div>
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Sobrenome</label>
                    <input id="lastname" type="text" name="lastname" value="{{ old('lastname') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>
            </div>


            <!-- E-mail -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Senha -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Confirmar Senha -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Telefone (opcional) -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone (opcional)</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Botão -->
            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">
                    Já tem uma conta?
                </a>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow font-medium">
                    Registrar
                </button>
            </div>
        </form>

        <!-- Política de privacidade -->
        <p class="mt-6 text-xs text-gray-500 text-center">
            Ao criar uma conta, você concorda com nossa
            <a href="#" class="text-indigo-600 hover:underline">Política de Privacidade</a> e
            <a href="#" class="text-indigo-600 hover:underline">Termos de Uso</a>.
        </p>
    </div>
</x-guest-layout>