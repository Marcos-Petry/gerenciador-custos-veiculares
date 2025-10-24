<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 fixed top-0 left-0 w-full z-50 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <!-- Esquerda -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="Logo GestoCar"
                         class="h-12 w-auto mr-3 object-contain">
                </a>

                <div class="hidden sm:flex sm:space-x-8 sm:ml-8">
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-700 font-medium hover:text-blue-700 transition text-sm">
                        Painel Gerencial
                    </a>
                </div>
            </div>

            <!-- Direita -->
            <div class="hidden sm:flex items-center gap-4">
                <!-- Notificações -->
                <div class="relative">
                    <a href="{{ route('notificacao.index') }}"
                       class="relative inline-flex items-center text-gray-500 hover:text-gray-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>

                        @if(isset($notificacoesPendentes) && $notificacoesPendentes > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                {{ $notificacoesPendentes }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Menu do Usuário -->
                <div x-data="{ openUser: false }" class="relative">
                    <button @click="openUser = !openUser"
                            class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-full transition border border-gray-300">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-700 to-blue-500 text-white text-sm font-semibold uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden sm:block text-gray-700 font-medium text-sm">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="openUser" @click.outside="openUser = false" x-transition
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                        <div class="px-4 py-2.5 border-b bg-gray-50">
                            <p class="font-semibold text-gray-800 text-sm">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-blue-50 transition">
                                Perfil
                            </a>

                            <a href="{{ route('notificacao.index') }}"
                               class="flex justify-between items-center px-4 py-1.5 text-sm text-gray-700 hover:bg-blue-50 transition">
                                <span>Notificações</span>
                                @if(isset($notificacoesPendentes) && $notificacoesPendentes > 0)
                                    <span class="bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                        {{ $notificacoesPendentes }}
                                    </span>
                                @endif
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-1.5 text-sm text-gray-700 hover:bg-red-50 transition">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Mobile -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Responsivo -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="px-4 py-3 border-b">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>

        <div class="py-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm transition">
                Painel Gerencial
            </a>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm transition">
                Perfil
            </a>
            <a href="{{ route('notificacao.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm transition">
                Notificações
                @if(isset($notificacoesPendentes) && $notificacoesPendentes > 0)
                    <span class="ml-2 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $notificacoesPendentes }}
                    </span>
                @endif
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 text-sm transition">
                    Sair
                </button>
            </form>
        </div>
    </div>
</nav>
