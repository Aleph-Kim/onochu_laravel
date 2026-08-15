<?php

namespace App\Models;

use App\Enums\MusicApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'kakao_id',
        'nickname',
        'profile_album_id',
        'preferred_music_app',
    ];

    protected $hidden = [];

    protected $casts = [
        'preferred_music_app' => MusicApp::class,
    ];

    public function recommends()
    {
        return $this->hasMany(Recommend::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function mutedArtists()
    {
        return $this->belongsToMany(Artist::class, 'artist_notification_mutes');
    }

    public function profileAlbum()
    {
        return $this->belongsTo(Album::class, 'profile_album_id');
    }

    /**
     * 추천 수, 프로필 앨범을 함께 로드
     */
    public function loadStats(): static
    {
        return $this->loadCount('recommends')->load('profileAlbum');
    }

    /**
     * 유저가 추천한 아티스트 Top N, limit이 null이면 전체
     */
    public function likeArtists(?int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Artist::select('artists.id', 'artists.flo_id', 'artists.name', 'artists.img_url')
            ->selectRaw('COUNT(artists.id) as count')
            ->join('song_artists', 'artists.id', '=', 'song_artists.artist_id')
            ->join('songs', 'song_artists.song_id', '=', 'songs.id')
            ->join('recommends', 'songs.id', '=', 'recommends.song_id')
            ->where('recommends.user_id', $this->id)
            ->groupBy('artists.id', 'artists.flo_id', 'artists.name', 'artists.img_url')
            ->orderByDesc('count')
            ->when($limit, fn($q) => $q->limit($limit))
            ->get();
    }

    /**
     * 유저가 추천한 장르 통계
     */
    public function likeGenres(): array
    {
        $genreCounts = $this->recommends()
            ->with('song')
            ->get()
            ->flatMap(fn($recommend) => explode(',', str_replace(' ', '', $recommend->song->genre ?? '')))
            ->filter()
            ->countBy()
            ->sortDesc();

        $result = [];
        foreach ($genreCounts as $genre => $count) {
            if (count($result) < 5) {
                $result[$genre] = $count;
            } else {
                $result['기타'] = ($result['기타'] ?? 0) + $count;
            }
        }

        return $result;
    }
}
