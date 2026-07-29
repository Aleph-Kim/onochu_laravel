<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RecommendsController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->name('main');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/song/detail', [SongController::class, 'detail'])->name('song.detail');

Route::get('/redirect/flo', [RedirectController::class, 'flo']);
Route::get('/redirect/youtube', [RedirectController::class, 'youtube']);

Route::get('/album/detail', [AlbumController::class, 'detail'])->name('album.detail');

Route::get('/artist/detail', [ArtistController::class, 'detail'])->name('artist.detail');

Route::get('/auth/login', [AuthController::class, 'login'])->name('login');
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'login']);

Route::get('/recommends', [RecommendsController::class, 'index'])->name('recommends.index');
Route::post('/recommends/post', [RecommendsController::class, 'post'])->name('recommends.post');
Route::get('/recommends/detail', [RecommendsController::class, 'detail'])->name('recommends.detail');

Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
Route::get('/mypage/user', [MypageController::class, 'user'])->name('mypage.user');
Route::post('/mypage/setProfileAlbum', [MypageController::class, 'setProfileAlbum'])->name('mypage.setProfileAlbum');

Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
//Route::get('/push/test', [PushSubscriptionController::class, 'test']);
