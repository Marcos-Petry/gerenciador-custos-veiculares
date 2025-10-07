<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gasto';
    protected $primaryKey = 'gasto_id';

    protected $fillable = [
        'veiculo_id',
        'usuario_id',
        'categoria',
        'descricao',
        'valor',
        'data_gasto',
    ];

    // Constantes para mapear categorias
    const CATEGORIAS = [
        1 => 'Combustível',
        2 => 'Manutenção',
        3 => 'Seguro',
        4 => 'Imposto',
        5 => 'Outro',
    ];

    // Acessor para exibir nome da categoria
    public function getCategoriaNomeAttribute()
    {
        return self::CATEGORIAS[$this->categoria] ?? '—';
    }

    // Relações
    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id', 'veiculo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function categoriaTexto()
    {
        return match ($this->categoria) {
            1 => 'Combustível',
            2 => 'Manutenção',
            3 => 'Seguro',
            4 => 'Imposto',
            default => 'Outro',
        };
    }

    public function anexos()
    {
        return $this->hasMany(AnexoGasto::class, 'gasto_id', 'gasto_id');
    }
}