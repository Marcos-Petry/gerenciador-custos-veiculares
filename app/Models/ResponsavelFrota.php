<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsavelFrota extends Model
{
    protected $table = 'responsavelfrota';
    protected $primaryKey = 'refcodigo';
    public $timestamps = true;

    protected $fillable = [
        'usucodigo',
        'frota_id',
        'refdatacadastro',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usucodigo', 'id'); // tabela users usa "id"
    }

    public function frota()
    {
        return $this->belongsTo(Frota::class, 'frota_id', 'frota_id');
    }
}

