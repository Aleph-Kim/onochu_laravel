<?php

namespace App\Models;

use App\Services\PlatformService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    use HasFactory;

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
    public static function latestPerSong(int $limit = 10, ?int $userId = null): Collection
    {
        $latestIds = static::when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderByRaw('created_at DESC')
            ->limit($limit)
            ->pluck('id');

        return static::with(['song.album', 'song.artists'])
            ->whereIn('id', $latestIds)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * 공유용 플랫폼 딥링크 (RecommendsController::show)
     */
    public function getUrlAttribute(): array
    {
        $song = ['flo_id' => $this->song->flo_id, 'title' => $this->song->title];
        $artists = $this->song->artists->map(fn($a) => ['name' => $a->name])->all();

        return app(PlatformService::class)->getPlatformUrl($song, $artists);
    }
}
