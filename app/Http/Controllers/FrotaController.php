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
        $user = Auth::user();

        // 🔹 Consulta: frotas em que o usuário é dono ou responsável
        $frotasQuery = Frota::with('veiculos', 'responsavel')
            ->where('usuario_dono_id', $user->id)
            ->orWhereHas('responsavel', fn($q) => $q->where('usucodigo', $user->id));

        // 🔹 Paginação (mantém o ->paginate() pra usar onEachSide() na view)
        $frotas = $frotasQuery->paginate(6);

        // 🔹 Adiciona flags de controle para a view
        $frotas->getCollection()->transform(function ($frota) use ($user) {
            // Dono
            $frota->ehDono = $frota->usuario_dono_id === $user->id;

            // Responsável (segue o mesmo padrão do ehDono)
            $frota->ehResponsavel = $frota->responsavel
                ->contains('usucodigo', $user->usucodigo);

            return $frota;
        });

        // 🔹 Flag que indica se veio de seleção externa
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
                'usuario_remetente_id'    => Auth::id(),
                'usuario_destinatario_id' => $userId,
                'veiculo_id'              => null,
                'frota_id'                => $frota->frota_id,
                'tipo'                    => Notificacao::TIPO_CONVITE_FROTA,
                'status'                  => Notificacao::STATUS_PENDENTE,
                'data_envio'              => now(),
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

    public function abandonarResponsabilidade(Frota $frota)
    {
        $user = Auth::user();

        // Garante autenticação
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // Garante que a relação 'responsavel' está carregada
        $frota->load('responsavel');

        // Verifica se o usuário é realmente responsável
        if (!$frota->responsavel->contains('id', $user->id)) {
            return redirect()->back()->with('error', 'Você não é responsável por esta frota.');
        }

        // Remove o vínculo na tabela pivô
        $frota->responsavel()->detach($user->id);

        // Cria a notificação apenas se a frota tiver dono válido
        if ($frota->usuario_dono_id) {
            Notificacao::create([
                'usuario_remetente_id'    => $user->id,                 // quem saiu
                'usuario_destinatario_id' => $frota->usuario_dono_id,   // dono da frota
                'frota_id'                => $frota->frota_id,          // frota
                'tipo'                    => 3,                         // aviso interno
                'status'                  => 0,                         // pendente
                'data_envio'              => now(),
            ]);
        }

        return redirect()
            ->route('frota.index')
            ->with('success', 'Você saiu da responsabilidade desta frota. O dono foi notificado.');
    }
}
