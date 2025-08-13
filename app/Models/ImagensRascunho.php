<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagensRascunho extends Model
{
    const BASE_UPLOAD_PATH = 'uploads' . DIRECTORY_SEPARATOR . 'imagens_rascunho';

    use HasFactory;

    protected $table = "imagens_rascunho";

    protected $fillable = [
        'arquivo',
        'id_anuncio_rascunho',
        'principal',
        'link',
    ];
}
