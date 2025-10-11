<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Gasto;

class ComparacaoController extends Controller
{
    public function index(Request $request)
    {
        // IDs vindos da query string (após seleção externa)
        $veiculoA = $request->has('veiculo_a') ? Veiculo::find($request->veiculo_a) : null;
        $veiculoB = $request->has('veiculo_b') ? Veiculo::find($request->veiculo_b) : null;

        return view('publico.comparar', compact('veiculoA', 'veiculoB'));
    }

    public function comparar(Request $request)
    {
        $idA = $request->input('veiculo_a');
        $idB = $request->input('veiculo_b');

        if (!$idA || !$idB) {
            return redirect()->route('publico.comparar')
                ->with('error', 'Selecione dois veículos para comparar.');
        }

        $veiculoA = Veiculo::with('frota')->findOrFail($idA);
        $veiculoB = Veiculo::with('frota')->findOrFail($idB);

        $gastosA = Gasto::where('veiculo_id', $idA)->get();
        $gastosB = Gasto::where('veiculo_id', $idB)->get();

        $totaisA = $gastosA->groupBy('categoria')->map->sum('valor');
        $totaisB = $gastosB->groupBy('categoria')->map->sum('valor');

        return view('publico.comparar_resultado', compact(
            'veiculoA',
            'veiculoB',
            'totaisA',
            'totaisB'
        ));
    }
}
