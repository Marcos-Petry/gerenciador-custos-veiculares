<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\Veiculo;
use App\Models\Frota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{

public function index(Request $request)
{
    $filtro = $request->get('status', 'pendentes'); // padrão = pendentes

    $query = Notificacao::where('usuario_destinatario_id', Auth::id())
        ->latest();

    switch ($filtro) {
        case 'aceitas':
            $query->where('status', Notificacao::STATUS_ACEITO);
            break;
        case 'recusadas':
            $query->where('status', Notificacao::STATUS_RECUSADO);
            break;
        case 'todas':
            // sem filtro extra → mostra tudo
            break;
        default: // pendentes
            $query->where('status', Notificacao::STATUS_PENDENTE);
    }

    $notificacoes = $query->paginate(10);

    return view('notificacao.index', compact('notificacoes', 'filtro'));
}



    // Enviar convite
    public function enviar(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'tipo' => 'required|integer', // 1 = veiculo, 2 = frota
            'id' => 'required|integer' // id do veículo ou frota
        ]);

        $destinatario = User::where('email', $request->email)->first();

        $notificacao = Notificacao::create([
            'usuario_remetente_id' => Auth::id(),
            'usuario_destinatario_id' => $destinatario->id,
            'veiculo_id' => $request->tipo == Notificacao::TIPO_CONVITE_VEICULO ? $request->id : null,
            'frota_id' => $request->tipo == Notificacao::TIPO_CONVITE_FROTA ? $request->id : null,
            'tipo' => $request->tipo,
            'status' => Notificacao::STATUS_PENDENTE,
        ]);

        return back()->with('success', 'Convite enviado com sucesso!');
    }

    // Aceitar convite
    public function aceitar($id)
    {
        $notificacao = Notificacao::findOrFail($id);

        if ($notificacao->status != Notificacao::STATUS_PENDENTE) {
            return back()->with('error', 'Convite já respondido.');
        }

        $notificacao->status = Notificacao::STATUS_ACEITO;
        $notificacao->data_resposta = now();
        $notificacao->save();

        // Vincula usuário como responsável
        if ($notificacao->tipo == Notificacao::TIPO_CONVITE_VEICULO) {
            $notificacao->veiculo->responsavel()->attach($notificacao->usuario_destinatario_id);
        } elseif ($notificacao->tipo == Notificacao::TIPO_CONVITE_FROTA) {
            $notificacao->frota->responsavel()->attach($notificacao->usuario_destinatario_id);
        }

        return back()->with('success', 'Convite aceito!');
    }

    // Recusar convite
    public function recusar($id)
    {
        $notificacao = Notificacao::findOrFail($id);

        if ($notificacao->status != Notificacao::STATUS_PENDENTE) {
            return back()->with('error', 'Convite já respondido.');
        }

        $notificacao->status = Notificacao::STATUS_RECUSADO;
        $notificacao->data_resposta = now();
        $notificacao->save();

        return back()->with('success', 'Convite recusado!');
    }

    public function cancelar(Notificacao $notificacao)
    {
        // só quem enviou pode cancelar (ajuste se quiser permitir donos de frota/veículo)
        if ($notificacao->usuario_remetente_id !== Auth::id()) {
            abort(403, 'Sem permissão para cancelar este convite.');
        }

        // só pendentes fazem sentido cancelar
        if ($notificacao->status !== Notificacao::STATUS_PENDENTE) {
            return back()->with('warning', 'Este convite já foi respondido.');
        }

        $notificacao->delete();

        return response()->json(['success' => true]);
    }
}
