<?php

namespace App\Models;

use App\Services\PlatformService;
use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    protected $fillable = [
        'song_id',
        'user_id',
        'score',
        'comment',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 곡별 최신 추천 목록 (MainController, MypageController 공통)
     */
    public static function latestPerSong(int $limit = 10, ?int $userId = null): array
    {
        $latestIds = static::when($userId, fn($q) => $q->where('user_id', $userId))
            ->selectRaw('MAX(id) as id')
            ->groupBy('song_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->limit($limit)
            ->pluck('id');

        return static::with(['song.album', 'song.artists'])
            ->whereIn('id', $latestIds)
            ->orderByDesc('id')
            ->get()
            ->map(function ($recommend) {
                $firstArtist = $recommend->song->artists->first();
                return [
                    'id'             => $recommend->id,
                    'song_id'        => $recommend->song_id,
                    'song_title'     => $recommend->song->title,
                    'album_id'       => $recommend->song->album_id,
                    'album_img_url'  => $recommend->song->album->img_url,
                    'artist_id'      => $firstArtist?->id,
                    'artist_name'    => $recommend->song->artists->pluck('name')->implode(' & '),
                    'artist_img_url' => $firstArtist?->img_url,
                    'recommend_date' => $recommend->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * 추천 상세 (RecommendsController::detail)
     */
    public static function findWithDetails(int $id): ?array
    {
        $recommend = static::with(['song.album', 'song.artists', 'user'])->find($id);

        if (!$recommend) {
            return null;
        }

        $artists = $recommend->song->artists->map(fn($a) => [
            'id'      => $a->id,
            'name'    => $a->name,
            'img_url' => $a->img_url,
            'flo_id'  => $a->flo_id,
        ])->toArray();

        $song = ['flo_id' => $recommend->song->flo_id, 'title' => $recommend->song->title];
        $url = app(PlatformService::class)->getPlatformUrl($song, $artists);

        return [
            'id'             => $recommend->id,
            'score'          => $recommend->score,
            'comment'        => $recommend->comment,
            'recommend_date' => $recommend->created_at,
            'song_id'        => $recommend->song->id,
            'song_title'     => $recommend->song->title,
            'album_id'       => $recommend->song->album_id,
            'lyrics'         => $recommend->song->lyrics,
            'song_flo_id'    => $recommend->song->flo_id,
            'album_img_url'  => $recommend->song->album->img_url,
            'release_date'   => $recommend->song->album->release_date,
            'album_flo_id'   => $recommend->song->album->flo_id,
            'genre'          => $recommend->song->genre,
            'play_time'      => $recommend->song->play_time,
            'user_id'        => $recommend->user->id,
            'user_name'      => $recommend->user->nickname,
            'artists'        => $artists,
            'url'            => $url,
        ];
    }
}
