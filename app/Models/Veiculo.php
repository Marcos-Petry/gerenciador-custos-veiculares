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

    public function frota()
    {
        return $this->belongsTo(Frota::class, 'frota_id', 'frota_id');
    }

    public function dono()
    {
        return $this->belongsTo(User::class, 'usuario_dono_id');
    }

    public function getVisibilidade()
    {
        if ($this->frota) {
            return $this->frota->visibilidadeTexto; // usa accessor da frota
        }
        return $this->visibilidadeTexto; // usa accessor do próprio veículo
    }

    public function getVisibilidadeTextoAttribute()
    {
        return $this->visibilidade == 1 ? 'Público' : 'Privado';
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

}