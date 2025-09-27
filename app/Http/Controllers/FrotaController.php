<?php

namespace App\Http\Controllers;

use App\Models\Frota;
use App\Models\Veiculo;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'responsaveis' => 'array',
            'responsaveis.*' => 'exists:users,id',
            'visibilidade' => 'required|in:0,1',
        ]);

        $data['usuario_dono_id'] = Auth::id();

        // Upload da foto
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('frotas/fotos', 'public');
        }

        $frota = Frota::create($data);

        // Relaciona veículos
        if (!empty($data['veiculos'])) {
            Veiculo::whereIn('veiculo_id', $data['veiculos'])
                ->update(['frota_id' => $frota->frota_id]);
        }

        // Convites para responsáveis
        if ($request->filled('responsaveis')) {
            $this->criarNotificacoesResponsaveis($frota, $request->responsaveis);
        }

        return redirect()->route('frota.index')
            ->with('success', 'Frota criada com sucesso!');
    }

    public function show(Frota $frota)
    {
        $frota->load(['dono', 'veiculos', 'responsavel']);

        // Convites pendentes
        $convitesPendentes = Notificacao::with('destinatario')
            ->where('frota_id', $frota->frota_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_FROTA)
            ->where('status', Notificacao::STATUS_PENDENTE)
            ->orderByDesc('data_envio')
            ->get();

        // Convites respondidos
        $convitesRespondidos = Notificacao::with('destinatario')
            ->where('frota_id', $frota->frota_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_FROTA)
            ->whereIn('status', [Notificacao::STATUS_ACEITO, Notificacao::STATUS_RECUSADO])
            ->orderByDesc('data_resposta')
            ->get();

        return view('frota.show', compact('frota', 'convitesPendentes', 'convitesRespondidos'));
    }

    public function edit(Frota $frota)
    {
        // Convites pendentes para exibir e poder cancelar
        $convitesPendentes = Notificacao::with('destinatario')
            ->where('frota_id', $frota->frota_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_FROTA)
            ->where('status', Notificacao::STATUS_PENDENTE)
            ->orderByDesc('data_envio')
            ->get();

        return view('frota.edit', compact('frota', 'convitesPendentes'));
    }

    public function update(Request $request, Frota $frota)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:300',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'veiculos' => 'array',
            'veiculos.*' => 'exists:veiculo,veiculo_id',
            'responsaveis' => 'array',
            'responsaveis.*' => 'exists:users,id',
            'visibilidade' => 'required|in:0,1',
        ]);

        // Atualiza foto
        if ($request->hasFile('foto')) {
            if ($frota->foto && Storage::disk('public')->exists($frota->foto)) {
                Storage::disk('public')->delete($frota->foto);
            }
            $data['foto'] = $request->file('foto')->store('frotas/fotos', 'public');
        }

        $frota->update($data);

        // Atualiza veículos
        Veiculo::where('frota_id', $frota->frota_id)
            ->whereNotIn('veiculo_id', $data['veiculos'] ?? [])
            ->update(['frota_id' => null]);

        if (!empty($data['veiculos'])) {
            Veiculo::whereIn('veiculo_id', $data['veiculos'])
                ->update(['frota_id' => $frota->frota_id]);
        }

        // Atualiza convites
        if ($request->filled('responsaveis')) {
            $this->atualizarNotificacoesResponsaveis($frota, $request->responsaveis);
        }

        return redirect()->route('frota.index')
            ->with('success', 'Frota atualizada com sucesso!');
    }

    public function destroy(Frota $frota)
    {
        // Desvincula veículos
        Veiculo::where('frota_id', $frota->frota_id)
            ->update(['frota_id' => null]);

        if ($frota->foto && Storage::disk('public')->exists($frota->foto)) {
            Storage::disk('public')->delete($frota->foto);
        }

        $frota->delete();

        return redirect()->route('frota.index')
            ->with('success', 'Frota excluída com sucesso!');
    }

    /**
     * Cria convites de responsáveis de frota
     */
    private function criarNotificacoesResponsaveis(Frota $frota, array $responsaveis): void
    {
        // evita criar convite pra quem já é responsável ativo
        $ativos = $frota->responsavel()->pluck('users.id')->toArray();

        foreach ($responsaveis as $userId) {
            if (in_array($userId, $ativos)) {
                continue;
            }

            Notificacao::create([
                'usuario_remetente_id' => Auth::id(),
                'usuario_destinatario_id' => $userId,
                'veiculo_id' => null,
                'frota_id' => $frota->frota_id,
                'tipo' => Notificacao::TIPO_CONVITE_FROTA,
                'status' => Notificacao::STATUS_PENDENTE,
            ]);
        }
    }

    /**
     * Atualiza convites de responsáveis de frota
     */
    private function atualizarNotificacoesResponsaveis(Frota $frota, array $novosResponsaveis): void
    {
        // Remove convites que não estão mais na lista
        Notificacao::where('frota_id', $frota->frota_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_FROTA)
            ->whereNotIn('usuario_destinatario_id', $novosResponsaveis)
            ->delete();

        // Já existentes (convites pendentes)
        $existentes = Notificacao::where('frota_id', $frota->frota_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_FROTA)
            ->pluck('usuario_destinatario_id')
            ->toArray();

        // Responsáveis ativos não devem receber convite
        $ativos = $frota->responsavel()->pluck('users.id')->toArray();

        $novos = array_diff($novosResponsaveis, $existentes, $ativos);

        $this->criarNotificacoesResponsaveis($frota, $novos);
    }
}