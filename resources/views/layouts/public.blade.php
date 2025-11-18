<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <title>@yield('title', 'Gestão de Gastos Veiculares')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900">

  <!-- Header fixo -->
  <header
    x-data="{ scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 0 })"
    :class="scrolled 
        ? 'bg-white/80 backdrop-blur border-b text-gray-900' 
        : 'bg-blue-900 text-white'"
    class="sticky top-0 z-50 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="{{ route('landing') }}" class="flex items-center gap-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo GestoCar" class="h-14 w-auto sm:h-16">
        <span class="font-semibold text-lg">GestoCar</span>
      </a>

      <nav class="flex items-center gap-3">
        <a href="{{ route('login') }}"
          :class="scrolled 
                ? 'hover:bg-gray-100 text-gray-900' 
                : 'hover:bg-blue-800 text-white'"
          class="px-3 py-2 rounded-md text-sm font-medium">
          Entrar
        </a>
        <a href="{{ route('register') }}"
          class="px-3 py-2 rounded-md text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700">
          Criar conta
        </a>
      </nav>
    </div>
  </header>

  <!-- Conteúdo -->
  <main class="flex-1">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="border-t bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
      <p>© {{ date('Y') }} GestoCar — Todos os direitos reservados.</p>
      <div class="flex gap-4">
        <a href="#" class="hover:text-gray-800">Termos</a>
        <a href="#" class="hover:text-gray-800">Privacidade</a>
        <a href="#" class="hover:text-gray-800">Contato</a>
      </div>
    </div>
  </footer>
</body>

</html>