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

        $frotas = \App\Models\Frota::where('usuario_dono_id', $user->id)
            ->orWhereHas('responsavel', fn($q) => $q->where('usucodigo', $user->id))
            ->orderBy('nome')
            ->get();

        $gastos = Gasto::with(['veiculo.frota', 'usuario'])
            ->whereHas('veiculo', function ($q) use ($user) {
                $q->where('usuario_dono_id', $user->id)
                    ->orWhereHas('responsavel', fn($r) => $r->where('users.id', $user->id))
                    ->orWhereHas('frota.responsavel', fn($r) => $r->where('usucodigo', $user->id));
            });
        $gastos = $this->aplicarFiltros($gastos, $request, $user);
            
        $gastos = $gastos->orderBy('data_gasto', 'desc')->paginate(10);

        return view('gasto.index', compact('gastos', 'frotas'));
    }


public function indexPorVeiculo(Veiculo $veiculo, Request $request)
{
    // Base da query: todos os gastos do veículo
    $gastos = $veiculo->gastos()
        ->with(['usuario', 'veiculo.frota']);

    // Categoria (select simples)
    if ($request->filled('categoria')) {
        $gastos->where('categoria', $request->categoria);
    }

    // Filtro por anexos
    if ($request->filled('anexoFiltro')) {
        if ($request->anexoFiltro === 'com') {
            $gastos->whereHas('anexos');
        } elseif ($request->anexoFiltro === 'sem') {
            $gastos->whereDoesntHave('anexos');
        }
    }

    $this->aplicarFiltros($gastos, $request, Auth::user());

    $gastos = $gastos
        ->orderBy('data_gasto', 'desc')
        ->paginate(10)
        ->withQueryString();

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

    public function indexPorFrota($frota_id, Request $request)
{
    $user = Auth::user();

    // Carrega frota + veículos
    $frota = \App\Models\Frota::with('veiculos')->findOrFail($frota_id);

    // Base: todos os gastos dos veículos da frota
    $gastos = Gasto::with(['veiculo.frota', 'usuario'])
        ->whereIn('veiculo_id', $frota->veiculos->pluck('veiculo_id'));

    /**
     * ============================================================
     * 🔥 Filtros específicos da tela de gastos por frota
     * ============================================================
     */

    // ➤ Filtro direto por categoria (select normal)
    if ($request->filled('categoria')) {
        $gastos->where('categoria', $request->categoria);
    }

    // ➤ Filtro direto por vínculo (dono / responsável)
    if ($request->filled('vinculo')) {
        if ($request->vinculo === 'dono') {
            $gastos->whereHas('veiculo', fn($v) => 
                $v->where('usuario_dono_id', $user->id)
            );
        } elseif ($request->vinculo === 'responsavel') {
            $gastos->whereHas('veiculo.responsavel', fn($r) => 
                $r->where('users.id', $user->id)
            );
        }
    }

    /**
     * ============================================================
     * 🔥 Filtros dinâmicos padronizados (campo / operador / valor)
     * ============================================================
     */
    $this->aplicarFiltros($gastos, $request, $user);

    // Finaliza
    $gastos = $gastos
        ->orderBy('data_gasto', 'desc')
        ->paginate(10)
        ->withQueryString();

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

    private function aplicarFiltros($query, Request $request, $user = null)
{
    // ------------------------------
    // FILTRO — Valor (entre, >, <, =)
    // ------------------------------
    if ($request->campo === 'valor') {

        if ($request->operador === 'between' && $request->filled('valor_de') && $request->filled('valor_ate')) {
            $query->whereBetween('valor', [$request->valor_de, $request->valor_ate]);
        }
        elseif ($request->filled('valor')) {
            $query->where('valor', $request->operador ?? '=', $request->valor);
        }

        return $query;
    }

    // ------------------------------
    // FILTRO — Data do gasto
    // ------------------------------
    if ($request->campo === 'data_gasto') {

        if ($request->operador === 'between' && $request->filled('valor_de') && $request->filled('valor_ate')) {
            $query->whereBetween('data_gasto', [$request->valor_de, $request->valor_ate]);
        }
        elseif ($request->filled('valor')) {
            $query->where('data_gasto', $request->operador ?? '=', $request->valor);
        }

        return $query;
    }

    // ------------------------------
    // FILTRO — Descrição
    // ------------------------------
    if ($request->campo === 'descricao' && $request->filled('valor')) {
        $query->where('descricao', 'like', "%{$request->valor}%");
        return $query;
    }

    // ------------------------------
    // FILTRO — Veículo (nome modelo)
    // ------------------------------
    if ($request->campo === 'veiculo' && $request->filled('valor')) {
        $query->whereHas('veiculo', fn($v) => 
            $v->where('modelo', 'like', "%{$request->valor}%")
        );
        return $query;
    }

    // ------------------------------
    // FILTRO — Categoria
    // ------------------------------
    if ($request->campo === 'categoria' && $request->filled('categoriaFiltro')) {
        $query->where('categoria', $request->categoriaFiltro);
        return $query;
    }

    // ------------------------------
    // FILTRO — Usuário
    // ------------------------------
    if ($request->campo === 'usuario' && $request->filled('usuarioFiltro')) {
        $query->whereHas('usuario', fn($u) =>
            $u->where('name', 'like', "%{$request->usuarioFiltro}%")
        );
        return $query;
    }

    // ------------------------------
    // FILTRO — Vínculo (dono / responsável)
    // ------------------------------
    if ($request->campo === 'vinculo' && $request->filled('vinculoFiltro') && $user) {

        if ($request->vinculoFiltro === 'dono') {
            $query->whereHas('veiculo', fn($v) =>
                $v->where('usuario_dono_id', $user->id)
            );
        }

        if ($request->vinculoFiltro === 'responsavel') {
            $query->whereHas('veiculo.responsavel', fn($r) =>
                $r->where('users.id', $user->id)
            );
        }

        return $query;
    }

    // ------------------------------
    // FILTRO — Anexos
    // ------------------------------
    if ($request->campo === 'anexos' && $request->filled('anexoFiltro')) {

        if ($request->anexoFiltro === 'com') {
            $query->whereHas('anexos');
        } else {
            $query->whereDoesntHave('anexos');
        }

        return $query;
    }

    return $query;
}


}