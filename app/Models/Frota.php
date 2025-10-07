<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frota extends Model
{
    use HasFactory;

    protected $table = 'frota';
    protected $primaryKey = 'frota_id';

    protected $fillable = [
        'usuario_dono_id',
        'nome',
        'descricao',
        'foto',
        'visibilidade',
    ];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'frota_id', 'frota_id');
    }

    public function dono()
    {
        return $this->belongsTo(User::class, 'usuario_dono_id');
    }

    public function getVisibilidadeTextoAttribute()
    {
        return $this->visibilidade == 1 ? 'Público' : 'Privado';
    }

    public function responsavel()
    {
        return $this->belongsToMany(User::class, 'responsavelfrota', 'frota_id', 'usucodigo')
            ->withTimestamps();
    }

    // Gera automaticamente todos os gastos da frota (via veículos)
    public function gastos()
    {
        return $this->hasManyThrough(
            Gasto::class,
            Veiculo::class,
            'frota_id',      // chave estrangeira em Veiculo
            'veiculo_id',    // chave estrangeira em Gasto
            'frota_id',      // chave local em Frota
            'veiculo_id'     // chave local em Veiculo
        );
    }
}
