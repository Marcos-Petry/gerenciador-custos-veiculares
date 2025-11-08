<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $table = 'veiculo';
    protected $primaryKey = 'veiculo_id';

    protected $fillable = [
        'usuario_dono_id',
        'frota_id',
        'modelo',
        'placa',
        'ano',
        'foto',
        'visibilidade',
    ];

    protected $casts = [
        'visibilidade' => 'boolean',
    ];

    // 🔹 Relações
    public function frota()
    {
        return $this->belongsTo(Frota::class, 'frota_id', 'frota_id');
    }

    public function dono()
    {
        return $this->belongsTo(User::class, 'usuario_dono_id');
    }

    public function responsavel()
    {
        return $this->belongsToMany(User::class, 'responsavelveiculo', 'veiculo_id', 'usucodigo')
            ->withTimestamps();
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'veiculo_id');
    }

    // 🔹 Accessors
    public function getVisibilidade()
    {
        // Usa a visibilidade do próprio veículo se existir
        if (!is_null($this->visibilidade)) {
            return $this->visibilidadeTexto;
        }

        // Caso contrário, tenta herdar da frota
        if ($this->frota) {
            return $this->frota->visibilidadeTexto;
        }

        // Valor padrão
        return 'Privado';
    }

    public function getVisibilidadeTextoAttribute()
    {
        return $this->visibilidade ? 'Público' : 'Privado';
    }
}
