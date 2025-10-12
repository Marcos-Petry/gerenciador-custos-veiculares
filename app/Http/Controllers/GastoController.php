<?php

namespace App\Http\Controllers;

use App\Models\AnexoGasto;
use App\Models\Gasto;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $gastos = Gasto::with(['veiculo.frota', 'usuario'])
            ->whereHas('veiculo', function ($q) use ($user) {
                $q->where('usuario_dono_id', $user->id)
                    ->orWhereHas('responsavel', function ($r) use ($user) {
                        $r->where('users.id', $user->id);
                    })
                    ->orWhereHas('frota.responsavel', function ($r) use ($user) {
                        $r->where('usucodigo', $user->id);
                    });
            })
            ->when($request->filled('categoria'), function ($q) use ($request) {
                $q->where('categoria', $request->categoria);
            })
            ->when($request->filled('campo') && $request->filled('valor'), function ($q) use ($request) {
                $campo = $request->campo;
                $operador = $request->operador ?? '=';
                $valor = $request->valor;

                if ($operador === 'like') {
                    $q->where($campo, 'like', '%' . $valor . '%');
                } else {
                    $q->where($campo, $operador, $valor);
                }
            })
            ->orderBy('data_gasto', 'desc')
            ->paginate(10);

        return view('gasto.index', compact('gastos'));
    }

    public function indexPorVeiculo(Veiculo $veiculo)
    {
        $gastos = $veiculo->gastos()
            ->with('usuario')
            ->orderBy('data_gasto', 'desc')
            ->paginate(10);

        return view('gasto.index_por_veiculo', compact('veiculo', 'gastos'));
    }

    public function create()
    {
        $user = Auth::user();

        $veiculos = Veiculo::where(function ($q) use ($user) {
            $q->where('usuario_dono_id', $user->id)
                ->orWhereHas('responsavel', function ($r) use ($user) {
                    $r->where('users.id', $user->id);
                })
                ->orWhereHas('frota.responsavel', function ($r) use ($user) {
                    $r->where('usucodigo', $user->id);
                });
        })
            ->orderBy('modelo')
            ->get();

        return view('gasto.create', compact('veiculos'));
    }

    public function createPorVeiculo(Veiculo $veiculo)
    {
        return view('gasto.create', compact('veiculo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,veiculo_id',
            'categoria'  => 'required|string|max:100',
            'descricao'  => 'nullable|string|max:255',
            'valor'      => 'required|numeric|min:0',
            'data_gasto' => 'required|date',
            'anexos.*'   => 'nullable|file|max:5120',
        ]);

        $data['usuario_id'] = Auth::id();

        $gasto = Gasto::create($data);

        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $caminho = $arquivo->store('anexos/gastos', 'public');
                AnexoGasto::create([
                    'gasto_id'      => $gasto->gasto_id,
                    'caminho'       => $caminho,
                    'nome_original' => $arquivo->getClientOriginalName(),
                ]);
            }
        }

        if ($request->has('veiculo_id')) {
            return redirect()
                ->route('veiculo.gastos.index', $gasto->veiculo_id)
                ->with('success', 'Gasto e anexos registrados com sucesso!');
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto e anexos registrados com sucesso!');
    }

    public function edit($id)
    {
        $gasto = Gasto::with(['veiculo', 'anexos'])->findOrFail($id);
        $origem = request()->query('origem', 'geral');

        return view('gasto.edit', compact('gasto', 'origem'));
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::findOrFail($id);

        $data = $request->validate([
            'descricao' => 'nullable|string|max:255',
            'valor' => 'required|numeric|min:0',
            'data_gasto' => 'required|date',
            'categoria' => 'required|integer',
            'anexos.*' => 'nullable|file|max:5120',
            'remover_anexos' => 'nullable|string',
        ]);

        $gasto->update($data);

        $remover = $request->input('remover_anexos', '');
        if ($remover !== '') {
            $ids = array_filter(explode(',', $remover));
            $anexos = AnexoGasto::where('gasto_id', $gasto->gasto_id)
                ->whereIn('anexo_id', $ids)
                ->get();

            foreach ($anexos as $anexo) {
                if (Storage::disk('public')->exists($anexo->caminho)) {
                    Storage::disk('public')->delete($anexo->caminho);
                }
                $anexo->delete();
            }
        }

        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $caminho = $arquivo->store('anexos/gastos', 'public');
                AnexoGasto::create([
                    'gasto_id'      => $gasto->gasto_id,
                    'caminho'       => $caminho,
                    'nome_original' => $arquivo->getClientOriginalName(),
                ]);
            }
        }

        $origem = $request->input('origem', 'geral');

        if ($origem === 'veiculo' && $gasto->veiculo_id) {
            return redirect()
                ->route('veiculo.gastos.index', $gasto->veiculo_id)
                ->with('success', 'Gasto atualizado com sucesso!');
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto atualizado com sucesso!');
    }

    public function show(Gasto $gasto)
    {
        $gasto->load(['veiculo', 'anexos']);
        return view('gasto.show', compact('gasto'));
    }

    public function destroy(Gasto $gasto)
    {
        $veiculoId = $gasto->veiculo_id;

        foreach ($gasto->anexos as $anexo) {
            if (Storage::disk('public')->exists($anexo->caminho)) {
                Storage::disk('public')->delete($anexo->caminho);
            }
            $anexo->delete();
        }

        $gasto->delete();

        if ($veiculoId) {
            return redirect()
                ->route('veiculo.gastos.index', $veiculoId)
                ->with('success', 'Gasto excluído com sucesso!');
        }

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto excluído com sucesso!');
    }

    public function indexPorFrota($frota_id)
    {
        $frota = \App\Models\Frota::with('veiculos.gastos')->findOrFail($frota_id);

        $gastos = Gasto::whereIn('veiculo_id', $frota->veiculos->pluck('veiculo_id'))
            ->orderBy('data_gasto', 'desc')
            ->paginate(10);

        return view('gasto.index_por_frota', compact('frota', 'gastos'));
    }

    public function createPorFrota($frotaId)
    {
        $frota = \App\Models\Frota::with('veiculos')->findOrFail($frotaId);
        $veiculos = $frota->veiculos; // apenas veículos dessa frota

        return view('gasto.create', compact('veiculos', 'frota'));
    }

    public function linhaTempoVeiculo($veiculoId)
    {
        $veiculo = \App\Models\Veiculo::findOrFail($veiculoId);

        // Pega os gastos do veículo, ordenados por data decrescente
        $gastos = \App\Models\Gasto::where('veiculo_id', $veiculoId)
            ->orderBy('data_gasto', 'desc')
            ->get();

        return view('gasto.linha-tempo', compact('veiculo', 'gastos'));
    }

    public function linhaTempoFrota($frotaId)
    {
        $frota = \App\Models\Frota::with('veiculos.gastos')->findOrFail($frotaId);

        // Junta todos os gastos da frota (de todos os veículos)
        $gastos = $frota->veiculos->flatMap->gastos
            ->sortByDesc('data_gasto');

        return view('gasto.linha-tempo', compact('frota', 'gastos'));
    }
}
