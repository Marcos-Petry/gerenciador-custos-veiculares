<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = 'notificacao';
    protected $primaryKey = 'notcodigo';
    public $timestamps = true;

    protected $fillable = [
        'usuario_remetente_id',
        'usuario_destinatario_id',
        'veiculo_id',
        'frota_id',
        'tipo',
        'status',
        'data_envio',
        'data_resposta',
    ];

    // Constantes para tipos
    const TIPO_CONVITE_VEICULO = 1;
    const TIPO_CONVITE_FROTA   = 2;

    // Constantes para status
    const STATUS_PENDENTE = 0;
    const STATUS_ACEITO   = 1;
    const STATUS_RECUSADO = 2;

    // Relacionamentos
    public function remetente()
    {
        return $this->belongsTo(User::class, 'usuario_remetente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'usuario_destinatario_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function frota()
    {
        return $this->belongsTo(Frota::class, 'frota_id');
    }

    // app/Models/Notificacao.php
    public function getRouteKeyName()
    {
        return 'notcodigo';
    }

    /**
     * Gera a mensagem descritiva conforme o tipo de notificação.
     */
    public function getMensagemAttribute()
    {
        switch ($this->tipo) {
            case self::TIPO_CONVITE_VEICULO:
                return "Você foi convidado para ser responsável pelo veículo: " .
                    ($this->veiculo->modelo ?? 'Desconhecido') .
                    ($this->remetente ? " (convite enviado por {$this->remetente->name})" : '');

            case self::TIPO_CONVITE_FROTA:
                return "Você foi convidado para participar da frota: " .
                    ($this->frota->nome ?? 'Desconhecida') .
                    ($this->remetente ? " (convite enviado por {$this->remetente->name})" : '');
            default:
                return "Nova notificação recebida.";
        }
    }

}