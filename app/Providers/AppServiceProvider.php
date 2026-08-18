<?php

namespace App\Providers;

use App\Models\RecommendPlay;
use App\Models\User;
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
                $pendingPlays = RecommendPlay::pendingFor($userId);

                if ($pendingPlays->isNotEmpty()) {
                    $recommendToRate = $pendingPlays->first()->recommend;

                    RecommendPlay::whereIn('id', $pendingPlays->pluck('id'))
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
