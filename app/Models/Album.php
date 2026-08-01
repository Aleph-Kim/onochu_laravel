<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'release_date',
        'genre',
        'type',
        'img_url',
        'flo_id',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
