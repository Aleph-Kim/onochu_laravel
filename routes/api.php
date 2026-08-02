<?php

use App\Http\Controllers\ArtistNotificationController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RecommendsController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::resource('recommends', RecommendsController::class)->only(['destroy']);

Route::post('/mypage/profile-album/{recommend}', [MypageController::class, 'setProfileAlbum'])->name('mypage.setProfileAlbum');

Route::get('/apple-music-url', [RedirectController::class, 'appleMusicUrl'])->name('apple-music.url');

Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
//Route::get('/push/test', [PushSubscriptionController::class, 'test']);

Route::post('/artists/{artist}/notification-toggle', [ArtistNotificationController::class, 'toggle']);
