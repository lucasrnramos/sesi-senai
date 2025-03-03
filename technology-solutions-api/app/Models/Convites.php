<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convite extends Model
{
    use HasFactory;

    protected $table = 'convites';

    protected $fillable = [
        'email',
        'hash',
        'data_e_hora',
    ];
}
