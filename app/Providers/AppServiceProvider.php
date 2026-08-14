<?php

namespace App\Providers;

use App\Models\RecommendPlay;
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
            $recommendToRate = null;

            if ($userId && !request()->routeIs('music-app.open')) {
                $pendingPlays = RecommendPlay::pendingFor($userId);

                if ($pendingPlays->isNotEmpty()) {
                    $recommendToRate = $pendingPlays->first()->recommend;
                    RecommendPlay::whereIn('id', $pendingPlays->pluck('id'))->update(['notified_at' => now()]);
                }
            }

            $view->with('recommendToRate', $recommendToRate);
        });
    }
}
