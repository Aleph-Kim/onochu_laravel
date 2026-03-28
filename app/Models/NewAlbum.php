<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewAlbum extends Model
{
    protected $fillable = [
        'album_title',
        'album_img_url',
        'flo_id',
    ];

    public function artists()
    {
        return $this->hasMany(NewAlbumArtist::class);
    }
}
