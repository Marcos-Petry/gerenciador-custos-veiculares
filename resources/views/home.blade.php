@extends('layouts.public')

@section('title', 'GestoCar')

@push('head')
  {{-- SEO básico + scroll suave + offset para header fixo --}}
  <meta name="description" content="Gerencie gastos de veículos e frotas: cadastre, lance despesas por categoria, visualize linha do tempo, convide responsáveis e compare desempenho (R$/km, km/L).">
  <style>
    html { scroll-behavior: smooth; }
    /* ajuste a altura do header fixo aqui, se necessário */
    :root { --header-h: 64px; }
    [id] { scroll-margin-top: calc(var(--header-h) + 12px); }
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
            Cadastre veículos e frotas, lance gastos por categoria, veja a linha do tempo,
            convide responsáveis e compare desempenho (R$/km, km/L).
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
            <a href="#planos" class="hover:text-gray-900">Planos e preços</a>
            <span class="text-gray-400">•</span>
            <a href="#beneficios" class="hover:text-gray-900">Benefícios</a>
          </nav>
        </div>

        <div class="bg-white border rounded-2xl p-6 shadow-sm">
          <dl class="grid sm:grid-cols-2 gap-6">
            <div>
              <dt class="text-sm text-gray-500">Gerencie</dt>
              <dd class="mt-1 font-semibold">Veículos e Frotas</dd>
              <p class="mt-2 text-sm text-gray-600">Cards, filtros e CRUD completo.</p>
            </div>
            <div>
              <dt class="text-sm text-gray-500">Registre</dt>
              <dd class="mt-1 font-semibold">Gastos por categoria</dd>
              <p class="mt-2 text-sm text-gray-600">Abastecimento, manutenção, encargos, imprevistos.</p>
            </div>
            <div>
              <dt class="text-sm text-gray-500">Visualize</dt>
              <dd class="mt-1 font-semibold">Linha do tempo</dd>
              <p class="mt-2 text-sm text-gray-600">Histórico cronológico por veículo.</p>
            </div>
            <div>
              <dt class="text-sm text-gray-500">Colabore</dt>
              <dd class="mt-1 font-semibold">Responsáveis & amizades</dd>
              <p class="mt-2 text-sm text-gray-600">Convide pessoas e gerencie permissões.</p>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </section>

 {{-- COMO FUNCIONA (cards com imagem no topo) --}}
<section id="como-funciona" class="bg-white border-t">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <h2 class="text-2xl font-bold">Como funciona</h2>

    @php
      // Troque os src das imagens para os seus arquivos reais (em public/img/…)
      $how = [
        [
          'title' => 'Monitoramento e análise de modo de condução',
          'desc'  => 'Acelerações, frenagens, curvas acentuadas e excessos de velocidade. Veja quem está dirigindo bem ou mal, compare comportamento por período e melhore a eficiência da frota.',
          'img'   => asset('img/como-analise-conducao.jpg'),
          'alt'   => 'Gráfico de pontuação de motoristas',
        ],
        [
          'title' => 'Rastreamento veicular e relatório de trajeto',
          'desc'  => 'Acompanhe rotas em tempo real, valide paradas/entregas e gere relatórios de trajeto. Verifique se o percurso corresponde ao planejado e otimize o dia a dia da operação.',
          'img'   => asset('img/como-rastreamento.jpg'),
          'alt'   => 'Mapa com rotas e marcadores',
        ],
        [
          'title' => 'Linha do tempo de gastos por veículo',
          'desc'  => 'Centralize abastecimentos, manutenções e encargos em uma linha do tempo cronológica. Filtre por categoria e identifique picos de custo rapidamente.',
          'img'   => asset('img/como-linha-tempo.jpg'),
          'alt'   => 'Linha do tempo de gastos',
        ],
        [
          'title' => 'Linha do tempo de gastos por veículo',
          'desc'  => 'Centralize abastecimentos, manutenções e encargos em uma linha do tempo cronológica. Filtre por categoria e identifique picos de custo rapidamente.',
          'img'   => asset('img/como-linha-tempo.jpg'),
          'alt'   => 'Linha do tempo de gastos',
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


  {{-- PLANOS E PREÇOS (com divisão em cima e embaixo) --}}
  <section id="planos" class="bg-gray-50 border-y">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
      <div class="text-center">
        <h2 class="text-2xl font-bold">Planos e preços</h2>
        <p class="mt-2 text-gray-600">Escolha o plano que faz sentido para agora. Você pode mudar a qualquer momento.</p>
      </div>

      @php
        $plans = [
          [
            'name' => 'Gratuito',
            'subtitle' => 'Para começar e testar o sistema.',
            'price' => 'R$ 0', 'suffix' => '/mês',
            'bullets' => [
              'Até <b>3 veículos</b> e <b>1 frota</b>',
              '<b>1 responsável</b> convidado',
              'Gastos básicos (sem anexos)',
              'Linha do tempo por veículo',
              'Suporte por e-mail em <b>72h</b>',
            ],
            'cta' => ['text' => 'Começar grátis', 'href' => route('register'), 'primary' => false],
            'highlight' => false,
            'badge' => null,
          ],
          [
            'name' => 'Pro',
            'subtitle' => 'Para quem gerencia frota pequena/média.',
            'price' => 'R$ 29', 'suffix' => '/mês',
            'bullets' => [
              'Até <b>50 veículos</b> e <b>10 frotas</b>',
              '<b>Responsáveis ilimitados</b>',
              'Anexos em gastos (PDF/imagem)',
              'Exportação CSV/PDF e <b>comparativos</b>',
              'Suporte por e-mail em <b>48h</b>',
            ],
            'cta' => ['text' => 'Assinar Pro', 'href' => route('register'), 'primary' => true],
            'highlight' => true,
            'badge' => 'Mais popular',
          ],
          [
            'name' => 'Empresarial',
            'subtitle' => 'Para equipes e múltiplas unidades.',
            'price' => 'Sob consulta', 'suffix' => null,
            'bullets' => [
              '<b>Veículos, frotas e usuários ilimitados</b>',
              'SSO/LDAP (opcional) e auditoria',
              'Backups dedicados',
              'epa',
              'Suporte prioritário / SLA',
            ],
            'cta' => ['text' => 'Falar com vendas', 'href' => 'mailto:contato@empresa.com?subject=Plano%20Empresarial%20-%20Gastos%20Veiculares', 'primary' => false],
            'highlight' => false,
            'badge' => null,
          ],
        ];
      @endphp

      <div class="mt-10 grid md:grid-cols-3 gap-6">
        @foreach ($plans as $p)
          @php
            $cardBase = 'rounded-2xl border bg-white p-6 relative flex flex-col transition-all duration-200 cursor-default hover:shadow-lg hover:-translate-y-0.5 focus-within:ring-2 focus-within:ring-indigo-400';
            $cardClass = $p['highlight']
              ? $cardBase.' ring-2 ring-indigo-600 hover:ring-indigo-700'
              : $cardBase.' hover:ring-2 hover:ring-indigo-300';
          @endphp

          <div class="{{ $cardClass }}">
            @if (!empty($p['badge']))
              <span class="absolute -top-3 right-6 rounded-full bg-indigo-600 text-white text-xs px-3 py-1">{{ $p['badge'] }}</span>
            @endif

            <h3 class="text-lg font-semibold">{{ $p['name'] }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ $p['subtitle'] }}</p>

            <div class="mt-4">
              <span class="text-3xl font-extrabold">{!! $p['price'] !!}</span>
              @if($p['suffix']) <span class="text-gray-500">{{ $p['suffix'] }}</span> @endif
            </div>

            <ul class="mt-6 space-y-2 text-sm text-gray-700">
              @foreach ($p['bullets'] as $b)
                <li class="flex items-start gap-2">
                  <span class="mt-2 h-1.5 w-1.5 rounded-full bg-indigo-600"></span>
                  <span>{!! $b !!}</span>
                </li>
              @endforeach
            </ul>

            <a href="{{ $p['cta']['href'] }}"
               aria-label="Selecionar plano {{ $p['name'] }}"
               class="mt-6 inline-flex justify-center px-5 py-3 rounded-md font-medium
                      {{ $p['cta']['primary']
                          ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                          : 'border hover:bg-gray-100' }}">
              {{ $p['cta']['text'] }}
            </a>
          </div>
        @endforeach
      </div>

    </div>
  </section>

<section class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <!-- Texto introdutório -->
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

        <!-- Card único com 3 colunas -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <!-- Missão -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Missão</h3>
                    <p class="mt-2 text-gray-600">
                        Oferecer soluções digitais práticas e seguras para auxiliar no controle e 
                        organização de despesas veiculares.
                    </p>
                </div>

                <!-- Visão -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Visão</h3>
                    <p class="mt-2 text-gray-600">
                        Tornar-se referência no Brasil em tecnologia de gestão veicular, ajudando 
                        pessoas e empresas a economizarem tempo e dinheiro.
                    </p>
                </div>

                <!-- Valores -->
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