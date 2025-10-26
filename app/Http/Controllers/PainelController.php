<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Frota;
use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PainelController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();

        // 🔹 Total de veículos (como dono)
        $veiculosComoDono = Veiculo::where('usuario_dono_id', $usuarioId)->count();

        // 🔹 Total de veículos (como responsável)
        $veiculosComoResponsavel = Veiculo::whereHas('responsavel', function ($q) use ($usuarioId) {
            $q->where('users.id', $usuarioId);
        })->count();

        // 🔹 Total de frotas (como dono)
        $frotasComoDono = Frota::where('usuario_dono_id', $usuarioId)->count();

        // 🔹 Total de frotas (como responsável)
        $frotasComoResponsavel = Frota::whereHas('responsavel', function ($q) use ($usuarioId) {
            $q->where('users.id', $usuarioId);
        })->count();

        // 🔹 Total de gastos do mês atual
        // 🔹 Total de gastos (últimos 5 meses)
        $gastosMes = Gasto::whereHas('veiculo', function ($q) use ($usuarioId) {
            $q->where('usuario_dono_id', $usuarioId);
        })
            ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth()) // últimos 5 meses
            ->sum('valor');


        // 🔹 Gastos mensais (últimos 5 meses)
        $gastosMensais = Gasto::select(
            DB::raw('EXTRACT(MONTH FROM data_gasto) as mes'),
            DB::raw('SUM(valor) as total')
        )
            ->whereHas('veiculo', function ($q) use ($usuarioId) {
                $q->where('usuario_dono_id', $usuarioId);
            })
            ->where('data_gasto', '>=', Carbon::now()->subMonths(5))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $labels = [];
        $valores = [];

        foreach ($gastosMensais as $gasto) {
            $mesNumero = intval($gasto->mes);
            $labels[] = ucfirst(Carbon::create(null, $mesNumero)->translatedFormat('M'));
            $valores[] = floatval($gasto->total);
        }

        // 🔹 Distribuição dos gastos por categoria
        // 🔹 Distribuição dos gastos por categoria (últimos 5 meses)
        if (Schema::hasColumn('gasto', 'categoria')) {
            $gastosPorCategoria = Gasto::select('categoria', DB::raw('SUM(valor) as total'))
                ->whereHas('veiculo', function ($q) use ($usuarioId) {
                    $q->where('usuario_dono_id', $usuarioId);
                })
                ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth()) // últimos 5 meses
                ->groupBy('categoria')
                ->orderBy('categoria')
                ->get()
                ->mapWithKeys(fn($item) => [$item->categoriaTexto() => floatval($item->total)]);
        } else {
            $gastosPorCategoria = collect();
        }


        // 🔹 Retorna tudo para a view
        return view('dashboard', compact(
            'veiculosComoDono',
            'veiculosComoResponsavel',
            'frotasComoDono',
            'frotasComoResponsavel',
            'gastosMes',
            'labels',
            'valores',
            'gastosPorCategoria'
        ));
    }
}
