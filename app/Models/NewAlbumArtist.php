<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewAlbumArtist extends Model
{
    protected $fillable = [
        'new_album_id',
        'artist_name',
        'flo_id',
    ];

    public function newAlbum()
    {
        return $this->belongsTo(NewAlbum::class);
    }
}
