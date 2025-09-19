<?php

namespace App\Http\Controllers;

use App\Models\Frota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrotaController extends Controller
{
    public function index(Request $request)
    {
        $frotas = Frota::with('veiculos')->paginate(6);
        $origemCampoExterno = $request->boolean('origemCampoExterno', false);

        return view('frota.index', compact('frotas', 'origemCampoExterno'));
    }


    public function create(Request $request)
    {
        return view('frota.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:300',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'veiculos' => 'array',
            'veiculos.*' => 'exists:veiculo,veiculo_id',
            'visibilidade' => 'required|in:0,1',
        ]);

        // Dono da frota
        $data['usuario_dono_id'] = \Illuminate\Support\Facades\Auth::id();

        // Upload da foto (se enviada)
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('frotas/fotos', 'public');
        }

        // Cria a frota
        $frota = Frota::create($data);

        // Se houver veículos selecionados, relaciona-os com a frota
        if (!empty($data['veiculos'])) {
            \App\Models\Veiculo::whereIn('veiculo_id', $data['veiculos'])
                ->update(['frota_id' => $frota->frota_id]);
        }

        return redirect()->route('frota.index')
            ->with('success', 'Frota criada com sucesso!');
    }

    public function show($id)
    {
        $frota = \App\Models\Frota::with(['veiculos', 'dono'])->findOrFail($id);

        return view('frota.show', compact('frota'));
    }


    public function edit(Frota $frota)
    {
        return view('frota.edit', compact('frota'));
    }

    public function update(Request $request, Frota $frota)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:300',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'veiculos' => 'array',
            'veiculos.*' => 'exists:veiculo,veiculo_id',
            'visibilidade' => 'required|in:0,1',
        ]);

        // Upload da foto (se enviada)
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('frotas/fotos', 'public');
        }

        // Atualiza os dados da frota
        $frota->update($data);

        // 🔹 Primeiro, "desassocia" os veículos que estavam ligados à frota mas não foram selecionados
        \App\Models\Veiculo::where('frota_id', $frota->frota_id)
            ->whereNotIn('veiculo_id', $data['veiculos'] ?? [])
            ->update(['frota_id' => null]);

        // 🔹 Agora associa os veículos selecionados
        if (!empty($data['veiculos'])) {
            \App\Models\Veiculo::whereIn('veiculo_id', $data['veiculos'])
                ->update(['frota_id' => $frota->frota_id]);
        }

        return redirect()->route('frota.index')
            ->with('success', 'Frota atualizada com sucesso!');
    }


    public function destroy(Frota $frota)
    {
        // 🔹 Primeiro, desassocia todos os veículos ligados a essa frota
        \App\Models\Veiculo::where('frota_id', $frota->frota_id)
            ->update(['frota_id' => null]);

        // 🔹 Se houver foto associada, remove do storage
        if ($frota->foto && Storage::disk('public')->exists($frota->foto)) {
            Storage::disk('public')->delete($frota->foto);
        }

        // 🔹 Agora exclui a frota
        $frota->delete();

        return redirect()->route('frota.index')
            ->with('success', 'Frota excluída com sucesso!');
    }


    public function select() {}
}
