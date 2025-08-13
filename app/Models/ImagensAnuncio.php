<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagensAnuncio extends Model
{
    const BASE_UPLOAD_PATH = 'uploads' . DIRECTORY_SEPARATOR . 'imagens_anuncio';

    use HasFactory;

    protected $table = "imagens_anuncio";

    protected $fillable = [
        'arquivo',
        'id_anuncio',
        'principal',
        'link',
    ];
}
