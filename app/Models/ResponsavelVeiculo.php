<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsavelVeiculo extends Model
{
    protected $table = 'responsavelveiculo';
    protected $primaryKey = 'revcodigo';
    public $timestamps = true;

    protected $fillable = [
        'usucodigo',
        'veiculo_id',
        'revdatacadastro',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usucodigo', 'id'); // tabela users usa "id"
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id', 'veiculo_id');
    }
}