<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UfEscritorio extends Model
{
    use HasFactory;

    protected $table = "ufs_escritorios";

    protected $fillable = [
        'id_escritorio_regional',
        'id_uf',
    ];
}
