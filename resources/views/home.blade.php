@extends('layouts.public')

@section('title', 'GestoCar')

@push('head')
{{-- SEO básico + scroll suave + offset para header fixo --}}
<meta name="description" content="Gerencie gastos de veículos e frotas: cadastre, lance despesas por categoria, visualize linha do tempo, convide responsáveis e compare desempenho (R$/km, km/L).">
<style>
  html {
    scroll-behavior: smooth;
  }

  /* ajuste a altura do header fixo aqui, se necessário */
  :root {
    --header-h: 64px;
  }

  [id] {
    scroll-margin-top: calc(var(--header-h) + 12px);
  }
</style>
@endpush

@section('content')
{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-b from-blue-900 to-white border-b">
  <div class="min-h-[calc(100vh-72px)] flex items-center
              max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">
        Controle completo dos <span class="text-indigo-600">gastos veiculares</span>
      </h1>
      <p class="mt-4 text-lg text-gray-700">
        Simplifique o controle financeiro e promova uma gestão colaborativa eficiente.
      </p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('register') }}"
          class="inline-flex items-center px-5 py-3 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700">
          Começar agora
        </a>
        <a href="{{ route('login') }}"
          class="inline-flex items-center px-5 py-3 rounded-md border border-gray-300 font-medium hover:bg-gray-100">
          Já tenho conta
        </a>
      </div>

      {{-- Nav de âncoras --}}
      <nav class="mt-6 flex flex-wrap gap-4 text-sm text-gray-600">
        <a href="#como-funciona" class="hover:text-gray-900">Como funciona</a>
        <span class="text-gray-400">•</span>
        <a href="#sobre-nos" class="hover:text-gray-900">Sobre nós</a>
      </nav>
    </div>

    {{-- Cards à direita --}}
    <div class="bg-white border rounded-2xl p-6 shadow-sm">
      <dl class="grid sm:grid-cols-2 gap-6">
        <div>
          <dt class="text-sm text-gray-500">Gerencie</dt>
          <dd class="mt-1 font-semibold">Veículos e Frotas</dd>
          <p class="mt-2 text-sm text-gray-600">
            Cadastre e organize veículos e frotas em um painel intuitivo, mantendo todas as informações centralizadas.
          </p>
        </div>

        <div>
          <dt class="text-sm text-gray-500">Registre e Compare</dt>
          <dd class="mt-1 font-semibold">Gastos e Desempenho</dd>
          <p class="mt-2 text-sm text-gray-600">
            Lance despesas por categoria e acompanhe comparativos de desempenho entre veículos.
          </p>
        </div>

        <div>
          <dt class="text-sm text-gray-500">Visualize</dt>
          <dd class="mt-1 font-semibold">Linha do tempo</dd>
          <p class="mt-2 text-sm text-gray-600">
            Acompanhe o histórico cronológico de gastos e eventos de cada veículo de forma visual e detalhada.
          </p>
        </div>

        <div>
          <dt class="text-sm text-gray-500">Colabore</dt>
          <dd class="mt-1 font-semibold">Gestão colaborativa</dd>
          <p class="mt-2 text-sm text-gray-600">
            Convide responsáveis e gerencie frotas de forma compartilhada, com permissões e controle de acesso.
          </p>
        </div>
      </dl>
    </div>
  </div>
</section>

{{-- COMO FUNCIONA --}}
<section id="como-funciona" class="bg-white border-t">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <h2 class="text-2xl font-bold">Como funciona</h2>

    @php
    $how = [
      [
        'title' => 'Gestão de veículos e frotas',
        'desc' => 'Cadastre veículos e frotas, adicione fotos e organize-os por categoria. Acompanhe todas as informações de forma centralizada e acessível.',
        'img' => asset('images/veiculo-frota-home.png'),
        'alt' => 'Painel de gestão de veículos e frotas',
      ],
      [
        'title' => 'Linha do tempo de gastos',
        'desc' => 'Visualize os gastos de cada veículo em uma linha do tempo cronológica, facilitando a análise financeira e a identificação de períodos críticos.',
        'img' => asset('images/linha-tempo-home.png'),
        'alt' => 'Linha do tempo de despesas de veículos',
      ],
      [
        'title' => 'Comparativo de desempenho',
        'desc' => 'Compare veículos com base no custo por quilômetro (R$/km) e consumo médio (km/L), identificando aqueles com melhor desempenho e economia.',
        'img' => asset('images/coomparativo-home.png'),
        'alt' => 'Gráfico comparativo de desempenho entre veículos',
      ],
      [
        'title' => 'Gestão colaborativa',
        'desc' => 'Convide responsáveis e compartilhe a administração das frotas, permitindo o trabalho conjunto de forma simples e segura.',
        'img' => asset('images/cooperacao-home.png'),
        'alt' => 'Colaboração entre responsáveis',
      ],
    ];
    @endphp

    <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-2 gap-6">
      @foreach($how as $item)
      <article class="group h-full flex flex-col overflow-hidden rounded-2xl border bg-white shadow-sm
               transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5
               hover:ring-2 hover:ring-indigo-300 focus-within:ring-2 focus-within:ring-indigo-400">
        <div class="h-48 sm:h-52 bg-gray-100 overflow-hidden">
          <img src="{{ $item['img'] }}" alt="{{ $item['alt'] }}"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            loading="lazy" decoding="async" width="1280" height="720">
        </div>
        <div class="p-6 flex-1">
          <h3 class="text-indigo-700 font-extrabold uppercase tracking-wide text-sm">
            {{ $item['title'] }}
          </h3>
          <p class="mt-3 text-gray-700 leading-relaxed text-sm">{{ $item['desc'] }}</p>
        </div>
      </article>
      @endforeach
    </div>
  </div>
</section>

{{-- SOBRE NÓS --}}
<section id="sobre-nos" class="bg-white py-16">
  <div class="max-w-6xl mx-auto px-6 lg:px-8">
    <div class="text-center mb-8">
      <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
        Sobre Nós
      </h2>
      <p class="mt-4 text-lg leading-8 text-gray-600 max-w-2xl mx-auto">
        Nossa empresa é especializada no desenvolvimento de soluções para o gerenciamento de gastos veiculares.
        Criamos ferramentas modernas, seguras e intuitivas que ajudam motoristas e empresas a controlarem
        despesas, otimizarem a gestão de frotas e aumentarem a eficiência operacional.
      </p>
    </div>

    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-8">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Missão</h3>
          <p class="mt-2 text-gray-600">
            Oferecer soluções digitais práticas e seguras para auxiliar no controle e
            organização de despesas veiculares.
          </p>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-gray-900">Visão</h3>
          <p class="mt-2 text-gray-600">
            Tornar-se referência no Brasil em tecnologia de gestão veicular, ajudando
            pessoas e empresas a economizarem tempo e dinheiro.
          </p>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-gray-900">Valores</h3>
          <p class="mt-2 text-gray-600">
            Compromisso com a qualidade, transparência nas informações e foco total na
            experiência do usuário.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
