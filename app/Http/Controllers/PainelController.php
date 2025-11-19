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
        $usuarioCodigo = Auth::user()->id;

        // ============================================================
        // 🔹 BUSCA TODOS OS VEÍCULOS DO USUÁRIO (DONO + RESPONSÁVEL)
        // ============================================================
        $veiculosIds = Veiculo::where('usuario_dono_id', $usuarioId)
            ->orWhereHas('responsavel', fn($q) => $q->where('usucodigo', $usuarioCodigo))
            ->pluck('veiculo_id');

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
        // 🔹 TOTAL DE GASTOS (ÚLTIMOS 5 MESES)
        // ============================================================
        $gastosMes = Gasto::whereIn('veiculo_id', $veiculosIds)
            ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth())
            ->sum('valor');

        // ============================================================
        // 🔹 GASTOS MENSAIS (CORRIGIDO: USA ANO + MÊS)
        // ============================================================
        $gastosMensaisBrutos = Gasto::select(
            DB::raw("TO_CHAR(data_gasto, 'YYYY-MM') as ano_mes"),
            DB::raw("SUM(valor) as total")
        )
        ->whereIn('veiculo_id', $veiculosIds)
        ->where('data_gasto', '>=', Carbon::now()->subMonths(4)->startOfMonth())
        ->groupBy('ano_mes')
        ->orderBy('ano_mes')
        ->get()
        ->keyBy('ano_mes'); // evita conflitos (ex: Outubro 2024 vs Outubro 2025)

        // ============================================================
        // 🔹 MONTA OS ÚLTIMOS 5 MESES (MESMO SEM GASTO)
        // ============================================================
        $labels = [];
        $valores = [];

        for ($i = 4; $i >= 0; $i--) {

            $data = Carbon::now()->subMonths($i);
            $chave = $data->format('Y-m'); // exemplo: 2025-10

            // Nome do mês para o gráfico
            $labels[] = ucfirst($data->translatedFormat('M'));

            // Se existir gasto naquele mês, usa; senão, 0
            $valores[] = isset($gastosMensaisBrutos[$chave])
                ? floatval($gastosMensaisBrutos[$chave]->total)
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