<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'genre',
        'group_type',
        'img_url',
        'flo_id',
        'flo_img_url',
    ];

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'song_artists');
    }

    /**
     * 추천 수 기준 인기 아티스트 (MainController - 비로그인)
     */
    public static function popularByRecommends(int $limit = 20): array
    {
        return static::select('artists.flo_id', 'artists.name', 'artists.img_url')
            ->selectRaw('COUNT(*) as recommend_cnt')
            ->join('song_artists', 'artists.id', '=', 'song_artists.artist_id')
            ->join('songs', 'song_artists.song_id', '=', 'songs.id')
            ->join('recommends', 'songs.id', '=', 'recommends.song_id')
            ->groupBy('artists.flo_id', 'artists.name', 'artists.img_url')
            ->orderByDesc('recommend_cnt')
            ->orderByRaw('MAX(recommends.created_at) DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * 특정 유저의 추천 아티스트 목록 (MainController - 로그인)
     */
    public static function byUser(int $userId): array
    {
        return static::select('artists.flo_id', 'artists.name', 'artists.img_url')
            ->selectRaw('COUNT(*) as recommend_cnt')
            ->join('song_artists', 'artists.id', '=', 'song_artists.artist_id')
            ->join('songs', 'song_artists.song_id', '=', 'songs.id')
            ->join('recommends', 'songs.id', '=', 'recommends.song_id')
            ->where('recommends.user_id', $userId)
            ->groupBy('artists.flo_id', 'artists.name', 'artists.img_url')
            ->orderByDesc('recommend_cnt')
            ->orderByRaw('MAX(recommends.created_at) DESC')
            ->get()
            ->toArray();
    }
}
