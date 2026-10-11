<?php

namespace App\Providers;

use App\Models\RecommendPlay;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // N+1 감지
        Model::preventLazyLoading($this->app->isLocal());

        View::composer('layouts.app', function ($view) {
            $userId = session('user.id');

            if (!$userId) {
                $view->with([
                    'recommendToRate' => null,
                    'unreadNotificationCount' => 0,
                ]);
                return;
            }

            // count만 필요하므로 User 모델 로드 없이 DatabaseNotification 직접 조회
            $unreadNotificationCount = DatabaseNotification::where('notifiable_type', User::class)
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->count();

            $recommendToRate = null;

            if (!request()->routeIs('music-app.open')) {
                // 모달은 최신 1건만 노출하고 나머지 미노출 기록은 함께 노출 처리
                $pendingPlay = RecommendPlay::pendingFor($userId)
                    ->with(['recommend.song.album', 'recommend.song.artists', 'recommend.user.profileAlbum'])
                    ->latest('id')
                    ->first();

                if ($pendingPlay) {
                    $recommendToRate = $pendingPlay->recommend;

                    RecommendPlay::pendingFor($userId)
                        ->where('id', '<=', $pendingPlay->id)
                        ->update(['notified_at' => now()]);
                }
            }

            $view->with([
                'recommendToRate' => $recommendToRate,
                'unreadNotificationCount' => $unreadNotificationCount,
            ]);
        });
    }
}
