<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'kakao_id',
        'nickname',
        'profile_album_id',
    ];

    protected $hidden = [];

    protected $casts = [];

    public function recommends()
    {
        return $this->hasMany(Recommend::class);
    }

    public function profileAlbum()
    {
        return $this->belongsTo(Album::class, 'profile_album_id');
    }

    /**
     * 추천 수, 프로필 앨범 포함한 유저 정보 (MypageController 공통)
     */
    public function infoWithStats(): array
    {
        $this->loadCount('recommends')->load('profileAlbum');

        return [
            'id'                   => $this->id,
            'nickname'             => $this->nickname,
            'recommend_count'      => $this->recommends_count,
            'profile_album_flo_id' => $this->profileAlbum?->flo_id,
            'profile_img_url'      => $this->profileAlbum?->img_url,
        ];
    }

    /**
     * 유저가 추천한 아티스트 Top 5 (MypageController)
     */
    public function likeArtists(int $limit = 5): array
    {
        return Artist::select('artists.id', 'artists.flo_id', 'artists.name', 'artists.img_url')
            ->selectRaw('COUNT(artists.id) as count')
            ->join('song_artists', 'artists.id', '=', 'song_artists.artist_id')
            ->join('songs', 'song_artists.song_id', '=', 'songs.id')
            ->join('recommends', 'songs.id', '=', 'recommends.song_id')
            ->where('recommends.user_id', $this->id)
            ->groupBy('artists.id', 'artists.flo_id', 'artists.name', 'artists.img_url')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * 유저가 추천한 장르 통계 (MypageController)
     * MySQL JSON_TABLE 사용으로 raw query 유지
     */
    public function likeGenres(): array
    {
        $rows = DB::select("
            SELECT genre_list.genre, COUNT(genre_list.genre) as count
            FROM recommends r
            JOIN (
                SELECT s.id, replace(temp_table.genre, ' ', '') as genre
                FROM songs s
                JOIN json_table(
                    replace(json_array(s.genre), ',', '\",\"'),
                    '\$[*]' columns (genre varchar(50) path '\$')
                ) temp_table
            ) genre_list ON r.song_id = genre_list.id
            WHERE r.user_id = ?
            GROUP BY genre_list.genre
            ORDER BY count DESC
        ", [$this->id]);

        $result = [];
        foreach ($rows as $key => $genre) {
            if ($key < 5) {
                $result[$genre->genre] = $genre->count;
            } else {
                $result['기타'] = ($result['기타'] ?? 0) + $genre->count;
            }
        }

        return $result;
    }
}
