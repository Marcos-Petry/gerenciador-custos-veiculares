<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-gradient-to-b from-blue-900 to-white">
        
        <!-- Header -->
        @include('layouts.navigation')

        <!-- Conteúdo -->
        <main class="flex-grow pt-20 px-6">
            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6 mt-8">
            <div class="container mx-auto text-center text-sm text-gray-600">
                <p>© {{ date('Y') }} GestoCar</p>
                <div class="mt-2 flex justify-center gap-4">
                    <a href="{{ route('privacidade') }}" class="hover:text-blue-600">Política de Privacidade</a>
                    <a href="{{ route('termos') }}" class="hover:text-blue-600">Termos de Uso</a>
                    <a href="{{ route('contato') }}" class="hover:text-blue-600">Contato</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
