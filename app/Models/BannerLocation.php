<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerLocation extends Model
{
    use HasFactory;

    protected $table = 'banner_locations';

    protected $fillable = [
        'location_key',
        'created_by_user',
    ];

    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_locations_relationship', 'banner_location_id', 'banner_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }
}
