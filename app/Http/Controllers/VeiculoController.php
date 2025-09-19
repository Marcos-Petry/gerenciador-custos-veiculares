<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Models\Frota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VeiculoController extends Controller
{
    public function index(Request $request)
    {
        $veiculos = \App\Models\Veiculo::with('frota')->paginate(6);
        $origemCampoExterno = $request->boolean('origemCampoExterno', false);

        return view('veiculo.index', compact('veiculos', 'origemCampoExterno'));
    }



    public function create(Request $request)
    {
        // Nada de session aqui. Só renderiza; a view lê via request('...')
        return view('veiculo.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'modelo' => 'required|string|max:150',
            'placa' => 'required|string|max:10|unique:veiculo,placa',
            'ano' => 'required|digits:4|integer',
            'frota_id' => 'nullable|exists:frota,frota_id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'visibilidade' => 'required|in:0,1',
        ]);

        // Dono do veículo
        $data['usuario_dono_id'] = \Illuminate\Support\Facades\Auth::id();

        // Upload da foto do veículo
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('veiculos/fotos', 'public');
        }


        Veiculo::create($data);

        return redirect()->route('veiculo.index')->with('success', 'Veículo cadastrado com sucesso!');
    }


    public function show(Veiculo $veiculo)
    {
        $veiculo->load('frota');
        return view('veiculo.show', compact('veiculo'));
    }

    public function edit(Request $request, Veiculo $veiculo)
    {
        // Se veio do externo, sobrescreve a frota
        if ($request->has('frota_id')) {
            $veiculo->frota_id = $request->frota_id;
        }

        return view('veiculo.edit', compact('veiculo'));
    }

    public function update(Request $request, Veiculo $veiculo)
    {
        $data = $request->validate([
            'modelo' => 'required|string|max:150',
            'placa' => [
                'required',
                'string',
                'max:10',
                Rule::unique('veiculo', 'placa')->ignore($veiculo->veiculo_id, 'veiculo_id'),
            ],
            'ano' => 'required|digits:4|integer',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'frota_id' => 'nullable|exists:frota,frota_id',
            'visibilidade' => 'required|in:0,1',
        ]);

        $veiculo->update($data);

        return redirect()->route('veiculo.index')->with('success', 'Veículo atualizado!');
    }

    public function destroy(Veiculo $veiculo)
    {

        // Se o veículo tiver uma foto salva, remover do storage
        if ($veiculo->foto && Storage::disk('public')->exists($veiculo->foto)) {
            Storage::disk('public')->delete($veiculo->foto);
        }
        $veiculo->delete();
        return redirect()->route('veiculo.index')->with('success', 'Veículo excluído!');
    }
}
