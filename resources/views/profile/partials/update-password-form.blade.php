<section class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <header class="mb-4">
        <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
            Alterar Senha
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Defina uma nova senha segura para proteger sua conta.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Senha Atual -->
        <div>
            <x-input-label for="update_password_current_password" value="Senha Atual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            
            {{-- Exibe mensagem de erro personalizada --}}
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-sm text-red-600">
                    A senha atual informada está incorreta.
                </p>
            @endif
        </div>

        <!-- Nova Senha -->
        <div>
            <x-input-label for="update_password_password" value="Nova Senha" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password'))
                @foreach ($errors->updatePassword->get('password') as $error)
                    @php
                        $error = str_replace(
                            ['The password field must be at least 8 characters.', 'The password confirmation does not match.'],
                            ['A senha deve ter pelo menos 8 caracteres.', 'A confirmação da senha não confere.'],
                            $error
                        );
                    @endphp
                    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <!-- Confirmar Senha -->
        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar Nova Senha" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password_confirmation'))
                @foreach ($errors->updatePassword->get('password_confirmation') as $error)
                    @php
                        $error = str_replace(
                            ['The password confirmation does not match.'],
                            ['A confirmação da senha não confere.'],
                            $error
                        );
                    @endphp
                    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <!-- Botão Salvar + Mensagem -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                Salvar
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm text-green-600">
                    Senha alterada com sucesso!
                </p>
            @endif
        </div>
    </form>
</section>
