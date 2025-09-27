<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VeiculoController extends Controller
{
    public function index(Request $request)
    {
        $veiculos = Veiculo::with('frota')->paginate(6);
        $origemCampoExterno = $request->boolean('origemCampoExterno', false);

        return view('veiculo.index', compact('veiculos', 'origemCampoExterno'));
    }

    public function create(Request $request)
    {
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
            'responsaveis' => 'array',
            'responsaveis.*' => 'exists:users,id',
        ]);

        // Dono do veículo
        $data['usuario_dono_id'] = Auth::id();

        // Upload da foto
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('veiculos/fotos', 'public');
        }

        $veiculo = Veiculo::create($data);

        // Responsáveis + notificações
        if ($request->filled('responsaveis')) {
            $this->criarNotificacoesResponsaveis($veiculo, $request->responsaveis);
        }

        return redirect()->route('veiculo.index')
            ->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function show(Veiculo $veiculo)
    {
        // Dono/frota e responsáveis já ativos (belongsToMany)
        $veiculo->load(['frota', 'responsavel']);

        // Convites pendentes para este veículo
        $convitesPendentes = Notificacao::with('destinatario')
            ->where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->where('status', Notificacao::STATUS_PENDENTE)
            ->orderByDesc('data_envio')
            ->get();

        // Convites respondidos (aceito / recusado)
        $convitesRespondidos = Notificacao::with('destinatario')
            ->where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->whereIn('status', [Notificacao::STATUS_ACEITO, Notificacao::STATUS_RECUSADO])
            ->orderByDesc('data_resposta')
            ->get();

        return view('veiculo.show', compact('veiculo', 'convitesPendentes', 'convitesRespondidos'));
    }

    public function edit(Request $request, Veiculo $veiculo)
    {
        if ($request->has('frota_id')) {
            $veiculo->frota_id = $request->frota_id;
        }

        // pendentes deste veículo
        $convitesPendentes = Notificacao::with('destinatario')
            ->where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->where('status', Notificacao::STATUS_PENDENTE)
            ->orderByDesc('data_envio')
            ->get();

        return view('veiculo.edit', compact('veiculo', 'convitesPendentes'));
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
            'responsaveis' => 'array',
            'responsaveis.*' => 'exists:users,id',
        ]);

        // Upload da foto (substitui se necessário)
        if ($request->hasFile('foto')) {
            if ($veiculo->foto && Storage::disk('public')->exists($veiculo->foto)) {
                Storage::disk('public')->delete($veiculo->foto);
            }
            $data['foto'] = $request->file('foto')->store('veiculos/fotos', 'public');
        }

        $veiculo->update($data);

        // Atualizar notificações (remove pendentes antigos e recria)
        if ($request->filled('responsaveis')) {
            $this->atualizarNotificacoesResponsaveis($veiculo, $request->responsaveis);
        }

        return redirect()->route('veiculo.index')
            ->with('success', 'Veículo atualizado com sucesso!');
    }

    public function destroy(Veiculo $veiculo)
    {
        if ($veiculo->foto && Storage::disk('public')->exists($veiculo->foto)) {
            Storage::disk('public')->delete($veiculo->foto);
        }

        $veiculo->delete();

        return redirect()->route('veiculo.index')->with('success', 'Veículo excluído!');
    }

    /**
     * Cria notificações de convite para os responsáveis informados
     */
    private function criarNotificacoesResponsaveis(Veiculo $veiculo, array $responsaveis): void
    {
        foreach ($responsaveis as $userId) {
            Notificacao::create([
                'usuario_remetente_id' => Auth::id(),
                'usuario_destinatario_id' => $userId,
                'veiculo_id' => $veiculo->veiculo_id,
                'frota_id' => null,
                'tipo' => Notificacao::TIPO_CONVITE_VEICULO,
                'status' => Notificacao::STATUS_PENDENTE,
            ]);
        }
    }

    /**
     * Atualiza as notificações ao editar responsáveis
     */
    private function atualizarNotificacoesResponsaveis(Veiculo $veiculo, array $novosResponsaveis): void
    {
        // IDs dos responsáveis já ativos
        $responsaveisAtivos = $veiculo->responsavel()->pluck('users.id')->toArray();

        // Remove convites pendentes antigos que não estão mais na lista
        Notificacao::where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->whereNotIn('usuario_destinatario_id', $novosResponsaveis)
            ->delete();

        // Convites já existentes (pendentes ou respondidos)
        $existentes = Notificacao::where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->pluck('usuario_destinatario_id')
            ->toArray();

        // Filtra apenas novos que não são responsáveis ativos nem já convidados
        $novos = array_diff($novosResponsaveis, $existentes, $responsaveisAtivos);

        // Cria convites apenas para os novos filtrados
        $this->criarNotificacoesResponsaveis($veiculo, $novos);
    }
}