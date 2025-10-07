<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnexoGasto extends Model
{
    use HasFactory;

    protected $table = 'anexo_gasto';
    protected $primaryKey = 'anexo_id';

    protected $fillable = [
        'gasto_id',
        'caminho',
        'nome_original',
    ];

    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'gasto_id', 'gasto_id');
    }
}
