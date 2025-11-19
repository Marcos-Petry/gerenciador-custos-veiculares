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
        $usuarioCodigo = Auth::user()->id; // pivot usa ID da tabela users

        // ============================================================
        // 🔹 BUSCA TODOS OS VEÍCULOS DO USUÁRIO (DONO + RESPONSÁVEL)
        // ============================================================
        $veiculosIds = Veiculo::where('usuario_dono_id', $usuarioId)
            ->orWhereHas('responsavel', fn($q) => $q->where('usucodigo', $usuarioCodigo))
            ->pluck('veiculo_id');   // ✔ PK REAL do veículo

        // ============================================================
        // 🔹 CONTAGEM DE VEÍCULOS E FROTAS
        // ============================================================
        $veiculosComoDono = Veiculo::where('usuario_dono_id', $usuarioId)->count();

        $veiculosComoResponsavel = Veiculo::whereHas('responsavel', function ($q) use ($usuarioCodigo) {
            $q->where('usucodigo', $usuarioCodigo);
        })->count();

        $frotasComoDono = Frota::where('usuario_dono_id', $usuarioId)->count();

        $frotasComoResponsavel = Frota::whereHas('responsavel', function ($q) use ($usuarioCodigo) {
            $q->where('usucodigo', $usuarioCodigo);
        })->count();

        // ============================================================
        // 🔹 TOTAL GASTO (ÚLTIMOS 5 MESES)
        // ============================================================
        $gastosMes = Gasto::whereIn('veiculo_id', $veiculosIds)
            ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth())
            ->sum('valor');

        // ============================================================
        // 🔹 GASTOS MENSAIS → GRÁFICO DE COLUNAS
        // ============================================================
        $gastosMensaisBrutos = Gasto::select(
            DB::raw('EXTRACT(MONTH FROM data_gasto) as mes'),
            DB::raw('SUM(valor) as total')
        )
        ->whereIn('veiculo_id', $veiculosIds)
        ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth())
        ->groupBy('mes')
        ->orderBy('mes')
        ->get()
        ->keyBy('mes');

        // Montar último 5 meses mesmo sem gasto
        $labels = [];
        $valores = [];

        for ($i = 4; $i >= 0; $i--) {
            $mesCarbon = Carbon::now()->subMonths($i);
            $mesNumero = intval($mesCarbon->format('m'));

            $labels[] = ucfirst($mesCarbon->translatedFormat('M'));

            $valores[] = isset($gastosMensaisBrutos[$mesNumero])
                ? floatval($gastosMensaisBrutos[$mesNumero]->total)
                : 0;
        }


        // ============================================================
        // 🔹 DISTRIBUIÇÃO POR CATEGORIA (GRÁFICO HORIZONTAL)
        // ============================================================
        if (Schema::hasColumn('gasto', 'categoria')) {
            $gastosPorCategoria = Gasto::select('categoria', DB::raw('SUM(valor) as total'))
                ->whereIn('veiculo_id', $veiculosIds)
                ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth())
                ->groupBy('categoria')
                ->orderBy('categoria')
                ->get()
                ->mapWithKeys(fn($item) => [
                    $item->categoriaTexto() => floatval($item->total)
                ]);
        } else {
            $gastosPorCategoria = collect();
        }

        // ============================================================
        // 🔹 RETORNA PARA A VIEW
        // ============================================================
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
