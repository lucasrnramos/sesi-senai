<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    use HasFactory;

    protected $table = 'colaborador';

    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'celular',
        'cep',
        'uf',
        'localidade',
        'bairro',
        'logradouro',
        'senha'
    ];
}
