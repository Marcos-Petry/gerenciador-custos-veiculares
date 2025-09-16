<section class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <header class="mb-4">
        <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
            <span>🔒</span> {{ __('Alterar Senha') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Defina uma nova senha segura para proteger sua conta.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Senha Atual -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('Senha Atual')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Nova Senha -->
        <div>
            <x-input-label for="update_password_password" :value="__('Nova Senha')" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Senha -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Nova Senha')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botão Salvar -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('Salvar') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600">
                    {{ __('Senha alterada com sucesso!') }}
                </p>
            @endif
        </div>
    </form>
</section>
