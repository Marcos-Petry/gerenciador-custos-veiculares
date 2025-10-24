<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Grid com dois cards lado a lado -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informações do Perfil -->
                <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Alterar Senha -->
                <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Excluir Conta -->
            <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
