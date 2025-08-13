<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPneu extends Model
{
    use HasFactory;

    protected $table = "tipos_pneu";

    protected $fillable = [
        'descricao',
    ];
}
