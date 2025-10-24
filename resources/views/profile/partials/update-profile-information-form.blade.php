<section class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
            Informações do Perfil
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Atualize suas informações de conta.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nome + Sobrenome lado a lado -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Nome')" />
                <x-text-input id="name" name="name" type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="lastname" :value="__('Sobrenome')" />
                <x-text-input id="lastname" name="lastname" type="text"
                    class="mt-1 block w-full"
                    :value="old('lastname', $user->lastname)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('lastname')" />
            </div>
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Telefone -->
        <div>
            <x-input-label for="phone" :value="__('Telefone')" />
            <x-text-input id="phone" name="phone" type="text"
                class="mt-1 block w-full"
                :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Botão -->
        <div class="flex items-center gap-4">
            <x-primary-button>Salvar</x-primary-button>
        </div>
    </form>
</section>
