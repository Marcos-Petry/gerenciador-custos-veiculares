<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Veiculo;
use App\Models\Gasto;

class PublicoController extends Controller
{
    /**
     * Exibe a consulta pública (frotas e veículos públicos).
     */
    public function index(Request $request)
    {
        $usuarioId = Auth::id();
        $campo    = $request->input('campo');
        $operador = $request->input('operador', 'like');
        $valor    = $request->input('valor');
        $valorDe  = $request->input('valor_de');
        $valorAte = $request->input('valor_ate');
        $tipoFiltro = $request->input('tipoFiltro');
        $relFiltro  = $request->input('relacionamentoFiltro');
        $origem   = $request->input('origemCampoExterno');


        // ====== Consulta base de veículos ======
        $veiculosQ = DB::table('veiculo as v')
            ->leftJoin('frota as f', 'f.frota_id', '=', 'v.frota_id')
            ->selectRaw("
                v.veiculo_id as id,
                'veiculo' as tipo,
                v.modelo as titulo,
                v.placa,
                v.ano,
                v.foto,
                v.visibilidade,
                v.created_at,
                f.nome as frota_nome
            ")
            ->when($origem, function ($q) use ($usuarioId) {
                // Se veio de um campo externo (ex: comparação)
                $q->where(function ($sub) use ($usuarioId) {
                    $sub->where('v.visibilidade', 1)
                        ->orWhere('v.usuario_dono_id', $usuarioId)
                        ->orWhereIn('v.frota_id', function ($sub2) use ($usuarioId) {
                            $sub2->select('frota_id')
                                ->from('responsavelfrota')
                                ->where('usucodigo', $usuarioId);
                        });
                });
            }, function ($q) {
                // Consulta pública (somente visíveis)
                $q->where('v.visibilidade', 1);
            });

        // ====== Inclui frotas públicas no modo normal ======
        if (!$origem) {
            $frotasQ = DB::table('frota as f')
                ->where('f.visibilidade', 1)
                ->selectRaw("
                    f.frota_id as id,
                    'frota' as tipo,
                    f.nome as titulo,
                    NULL as placa,
                    NULL as ano,
                    f.foto as foto,  -- ✅ antes era NULL, agora puxa a foto real
                    f.visibilidade,
                    f.created_at,
                    NULL as frota_nome
                ");
            $base = $veiculosQ->unionAll($frotasQ);
        } else {
            $base = $veiculosQ;
        }

        $q = DB::query()->fromSub($base, 'pub');

        // ====== Filtros de pesquisa ======
        if ($campo) {
            switch ($campo) {
                case 'titulo':
                case 'placa':
                case 'frota_nome':
                    if ($valor !== null && $valor !== '') {
                        if ($operador === 'like') {
                            $q->where($campo, 'ILIKE', "%{$valor}%");
                        } elseif (in_array($operador, ['=', '>', '<'])) {
                            $q->where($campo, $operador, $valor);
                        }
                    }
                    break;

                case 'ano':
                    if ($operador === 'between' && $valorDe !== null && $valorAte !== null) {
                        $q->whereBetween('ano', [$valorDe, $valorAte]);
                    } elseif ($valor !== null && $valor !== '' && in_array($operador, ['=', '>', '<'])) {
                        $q->where('ano', $operador, $valor);
                    }
                    break;

                case 'tipoFiltro':
                    if ($tipoFiltro === 'veiculo' || $tipoFiltro === 'frota') {
                        $q->where('tipo', $tipoFiltro);
                    }
                    break;

                case 'relacionamentoFiltro':
                    if ($relFiltro === 'com_frota') {
                        $q->whereNotNull('frota_nome');
                    } elseif ($relFiltro === 'sem_frota') {
                        $q->whereNull('frota_nome');
                    }
                    break;
            }
        }


        // ====== Paginação ======
        $itens = $q->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        // ====== Retorno à comparação ======
        if ($origem && $request->filled('selecionado')) {
            $selecionado = $request->input('selecionado');

            // Mantém os IDs já selecionados e adiciona o novo
            $query = [
                'veiculoA' => $request->input('veiculoA'),
                'veiculoB' => $request->input('veiculoB'),
            ];

            // Atualiza apenas o campo que veio da origem
            $query[$origem] = $selecionado;

            // Volta à tela de comparação sem executar a comparação ainda
            return redirect()->route('publico.comparar', array_merge($query, ['comparar' => 'nao']));
        }

        return view('publico.index', compact('itens', 'campo', 'operador', 'valor', 'origem'));
    }

    /**
     * Página de comparação entre dois veículos públicos.
     */
    public function comparar(Request $request)
    {
        $veiculoA = $request->query('veiculoA');
        $veiculoB = $request->query('veiculoB');
        $comparar = $request->query('comparar', 'nao');
        $vencedor = null;

        $gastosA = 0;
        $gastosB = 0;
        $categoriasA = collect();
        $categoriasB = collect();

        $dadosA = $veiculoA ? Veiculo::find($veiculoA) : null;
        $dadosB = $veiculoB ? Veiculo::find($veiculoB) : null;

        if ($comparar !== 'sim' || !$dadosA || !$dadosB) {
            return view('publico.comparar', compact(
                'dadosA',
                'dadosB',
                'veiculoA',
                'veiculoB',
                'gastosA',
                'gastosB',
                'categoriasA',
                'categoriasB',
                'vencedor'
            ));
        }

        // ====== Cálculo dos gastos ======
        $gastosA = DB::table('gasto')
            ->where('veiculo_id', $dadosA->veiculo_id)
            ->sum('valor');

        $gastosB = DB::table('gasto')
            ->where('veiculo_id', $dadosB->veiculo_id)
            ->sum('valor');

        // ====== Totais por categoria ======
        $categoriasA = DB::table('gasto')
            ->select('categoria', DB::raw('SUM(valor) as total'))
            ->where('veiculo_id', $dadosA->veiculo_id)
            ->groupBy('categoria')
            ->get()
            ->map(function ($item) {
                $item->categoria_nome = Gasto::CATEGORIAS[$item->categoria] ?? 'Outro';
                return $item;
            });

        $categoriasB = DB::table('gasto')
            ->select('categoria', DB::raw('SUM(valor) as total'))
            ->where('veiculo_id', $dadosB->veiculo_id)
            ->groupBy('categoria')
            ->get()
            ->map(function ($item) {
                $item->categoria_nome = Gasto::CATEGORIAS[$item->categoria] ?? 'Outro';
                return $item;
            });

        // ====== Determina o vencedor ======
        if ($gastosA > $gastosB) $vencedor = 'A';
        elseif ($gastosB > $gastosA) $vencedor = 'B';

        return view('publico.comparar', compact(
            'dadosA',
            'dadosB',
            'veiculoA',
            'veiculoB',
            'gastosA',
            'gastosB',
            'categoriasA',
            'categoriasB',
            'vencedor'
        ));
    }

}