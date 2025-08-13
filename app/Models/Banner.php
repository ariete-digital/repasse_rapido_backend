<?php

namespace App\Models;

use App\Helpers\Base64Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Banner extends Model
{
    const BASE_UPLOAD_PATH = 'uploads' . DIRECTORY_SEPARATOR . 'banners';

    use HasFactory;

    protected $table = 'banner';

    protected $fillable = [
        'original_filename',
        'filename',
        'cdn_url',
        'title',
        'subtitle',
        'link',
        'created_by_user',
        'starts_at',
        'type',
        'format',
    ];

    protected $appends = [
        'url_imagem'
    ];

    public function getUrlImagemAttribute()
    {
        if(!$this->cdn_url) return null;
        $basePath = Banner::BASE_UPLOAD_PATH;
        if(Config::get('app.env') == 'production'){
            $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $this->filename;
        } else {
            $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $this->filename);
        }
        return $urlImg;
    }

    public function locations()
    {
        return $this->belongsToMany(BannerLocation::class, 'banner_locations_relationship', 'banner_id', 'banner_location_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    public function cities()
    {
        return $this->belongsToMany(Cidade::class, 'banner_city_relationship', 'banner_id', 'city_id');
    }

    public function states()
    {
        return $this->belongsToMany(Uf::class, 'banner_city_relationship', 'banner_id', 'state_id');
    }
}
