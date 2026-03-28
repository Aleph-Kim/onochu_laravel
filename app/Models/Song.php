<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'album_id',
        'title',
        'genre',
        'title_yn',
        'play_time',
        'lyrics',
        'composer',
        'lyricist',
        'arranger',
        'flo_id',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'song_artists');
    }

    public function recommends()
    {
        return $this->hasMany(Recommend::class);
    }
}
