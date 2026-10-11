<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendPlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommend_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommend()
    {
        return $this->belongsTo(Recommend::class);
    }

    /**
     * 아직 모달로 노출하지 않은 재생 기록
     */
    public static function pendingFor(int $userId): Builder
    {
        return static::where('user_id', $userId)->whereNull('notified_at');
    }
}
