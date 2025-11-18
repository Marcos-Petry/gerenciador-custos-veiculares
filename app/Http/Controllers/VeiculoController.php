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
    $user = Auth::user();

    // ==========================
    // BASE DA QUERY
    // ==========================
    $query = Veiculo::with('frota')
        ->where(function ($q) use ($user) {
            $q->where('usuario_dono_id', $user->id)
              ->orWhereHas('responsavel', fn($r) => $r->where('usucodigo', $user->id))
              ->orWhereHas('frota.responsavel', fn($r) => $r->where('usucodigo', $user->id));
        });

    // ==========================
    // FILTRO ÚNICO (MESMO ESQUEMA DA TELA DE GASTOS)
    // ==========================
    $campo            = $request->input('campo');
    $operador         = $request->input('operador');
    $valor            = $request->input('valor');
    $valorDe          = $request->input('valor_de');
    $valorAte         = $request->input('valor_ate');
    $valorVisibilidade = $request->input('valor_visibilidade');
    $valorVinculo      = $request->input('valor_vinculo');

    if ($campo && $operador) {

        switch ($campo) {

            // --------------------
            // MODELO
            // --------------------
            case 'modelo':
                if ($valor !== null && $valor !== '') {
                    if ($operador === '=') {
                        $query->where('modelo', $valor);
                    } elseif ($operador === 'like') {
                        $query->where('modelo', 'like', "%{$valor}%");
                    } elseif ($operador === 'starts') {
                        $query->where('modelo', 'like', "{$valor}%");
                    } elseif ($operador === 'ends') {
                        $query->where('modelo', 'like', "%{$valor}");
                    }
                }
                break;

            // --------------------
            // PLACA
            // --------------------
            case 'placa':
                if ($valor !== null && $valor !== '') {
                    if ($operador === '=') {
                        $query->where('placa', $valor);
                    } elseif ($operador === 'like') {
                        $query->where('placa', 'like', "%{$valor}%");
                    }
                }
                break;

            // --------------------
            // ANO
            // --------------------
            case 'ano':
                if ($operador === 'between' && $valorDe !== null && $valorAte !== null && $valorDe !== '' && $valorAte !== '') {
                    $query->whereBetween('ano', [$valorDe, $valorAte]);
                } elseif ($valor !== null && $valor !== '') {
                    if ($operador === '=') {
                        $query->where('ano', $valor);
                    } elseif ($operador === '>') {
                        $query->where('ano', '>', $valor);
                    } elseif ($operador === '<') {
                        $query->where('ano', '<', $valor);
                    }
                }
                break;

            // --------------------
            // VISIBILIDADE (usa valor_visibilidade)
            // --------------------
            case 'visibilidade':
                if ($valorVisibilidade !== null && $valorVisibilidade !== '') {
                    $query->where('visibilidade', $valorVisibilidade);
                }
                break;

            // --------------------
            // VÍNCULO (usa valor_vinculo)
            // --------------------
            case 'vinculo':
                if ($valorVinculo === 'dono') {
                    $query->where('usuario_dono_id', $user->id);
                } elseif ($valorVinculo === 'responsavel') {
                    $query->whereHas('responsavel', function ($r) use ($user) {
                        $r->where('usucodigo', $user->id);
                    });
                }
                break;
        }
    }

    // ==========================
    // PAGINAÇÃO + ORDEM
    // ==========================
    $veiculos = $query
        ->orderBy('modelo')
        ->paginate(6)
        ->appends($request->query());

    // ==========================
    // OUTRAS VARIÁVEIS DA VIEW
    // ==========================
    $origemCampoExterno = $request->boolean('origemCampoExterno', false);
    $modoSomenteVisualizacao = !$veiculos->contains('usuario_dono_id', $user->id);

    return view('veiculo.index', compact('veiculos', 'origemCampoExterno', 'modoSomenteVisualizacao'));
}

    public function create(Request $request)
    {
        // 🔹 Verifica se veio da tela de seleção de frotas
        $frotaSelecionada = null;
        if ($request->has('frotas')) {
            $ids = (array) $request->get('frotas');
            $frotaSelecionada = \App\Models\Frota::whereIn('frota_id', $ids)->first();
            // mantém o ID no request para exibir na view
            $request->merge(['frota_id' => $frotaSelecionada?->frota_id]);
        }

        return view('veiculo.create', compact('frotaSelecionada'));
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

    public function show(Veiculo $veiculo, Request $request)
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

        // 🔹 Detecta se veio da tela de frota ou da consulta pública
        $fromFrota = $request->query('from_frota');        // Ex: ?from_frota=3
        $fromPublico = $request->boolean('from_publico');  // Ex: ?from_publico=1

        // Envia tudo pra view
        return view('veiculo.show', compact(
            'veiculo',
            'convitesPendentes',
            'convitesRespondidos',
            'fromFrota',
            'fromPublico'
        ));
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

        // Atualizar responsáveis (adição e remoção)
        $this->sincronizarResponsaveis($veiculo, $request->input('responsaveis', []));


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
     * Atualiza as notificações de responsáveis de um veículo
     * sem sobrescrever convites pendentes já existentes.
     */
    private function atualizarNotificacoesResponsaveis(Veiculo $veiculo, array $novosResponsaveis): void
    {
        // Responsáveis já ativos
        $responsaveisAtivos = $veiculo->responsavel()->pluck('users.id')->toArray();

        // Usuários que já possuem algum convite (pendente, aceito ou recusado)
        $jaConvidados = Notificacao::where('veiculo_id', $veiculo->veiculo_id)
            ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
            ->pluck('usuario_destinatario_id')
            ->toArray();

        // Apenas cria convite se não for ativo nem já convidado
        $novos = array_diff($novosResponsaveis, $responsaveisAtivos, $jaConvidados);

        foreach ($novos as $userId) {
            Notificacao::create([
                'usuario_remetente_id' => Auth::id(),
                'usuario_destinatario_id' => $userId,
                'veiculo_id' => $veiculo->veiculo_id,
                'frota_id' => null,
                'tipo' => Notificacao::TIPO_CONVITE_VEICULO,
                'status' => Notificacao::STATUS_PENDENTE,
                'data_envio' => now(),
            ]);
        }

        // 🔹 Não apaga convites antigos!
        // O cancelamento deve ser feito manualmente pelo botão na interface.
    }

    public function indexPorFrota($frota_id)
    {
        $user = Auth::user();

        // Carrega a frota com seus veículos e responsáveis
        $frota = \App\Models\Frota::with(['veiculos', 'responsavel'])->findOrFail($frota_id);

        // Verifica permissões
        $usuarioEhDono = $frota->usuario_dono_id === ($user->id ?? null);
        $usuarioEhResponsavel = $frota->responsavel->contains('id', $user->id ?? null);
        $frotaEhPublica = $frota->visibilidade == 1;

        // Se não for pública, nem dono e nem responsável → acesso negado
        if (!($frotaEhPublica || $usuarioEhDono || $usuarioEhResponsavel)) {
            abort(403, 'Você não tem permissão para acessar esta frota.');
        }

        // Determina modo de exibição (somente visualização para responsáveis e público)
        $modoSomenteVisualizacao = !$usuarioEhDono;

        // Carrega os veículos da frota
        $veiculos = \App\Models\Veiculo::where('frota_id', $frota_id)
            ->orderBy('modelo')
            ->paginate(9);

        return view('veiculo.index_por_frota', compact('frota', 'veiculos', 'modoSomenteVisualizacao'));
    }

    /**
     * Sincroniza os responsáveis de um veículo (adiciona, mantém e remove)
     * e cria notificações adequadas (convite ou aviso de remoção).
     */
    private function sincronizarResponsaveis(Veiculo $veiculo, array $novosResponsaveis): void
    {
        // Responsáveis atuais
        $atuais = $veiculo->responsavel()->pluck('users.id')->toArray();

        // Quem foi removido
        $removidos = array_diff($atuais, $novosResponsaveis);
        // Quem é novo
        $novos = array_diff($novosResponsaveis, $atuais);

        // 🔹 Remove responsáveis antigos e manda aviso
        foreach ($removidos as $userId) {
            $veiculo->responsavel()->detach($userId);

            // Envia aviso de remoção (não é convite)
            Notificacao::create([
                'usuario_remetente_id'    => Auth::id(),                 // dono que removeu
                'usuario_destinatario_id' => $userId,                    // removido
                'veiculo_id'              => $veiculo->veiculo_id,
                'frota_id'                => null,
                'tipo'                    => Notificacao::TIPO_AVISO_INTERNO,    // ou 3, conforme teu enum
                'status'                  => Notificacao::STATUS_PENDENTE,
                'mensagem'                => 'Você foi removido como responsável do veículo: ' . $veiculo->modelo,
                'data_envio'              => now(),
            ]);
        }

        // 🔹 Adiciona novos responsáveis
        foreach ($novos as $userId) {
            // Evita recriar convite se já tiver pendente
            $jaConvidado = Notificacao::where('veiculo_id', $veiculo->veiculo_id)
                ->where('tipo', Notificacao::TIPO_CONVITE_VEICULO)
                ->where('usuario_destinatario_id', $userId)
                ->where('status', Notificacao::STATUS_PENDENTE)
                ->exists();

            if (!$jaConvidado) {
                Notificacao::create([
                    'usuario_remetente_id'    => Auth::id(),
                    'usuario_destinatario_id' => $userId,
                    'veiculo_id'              => $veiculo->veiculo_id,
                    'frota_id'                => null,
                    'tipo'                    => Notificacao::TIPO_CONVITE_VEICULO,
                    'status'                  => Notificacao::STATUS_PENDENTE,
                    'data_envio'              => now(),
                ]);
            }
        }
    }
}
