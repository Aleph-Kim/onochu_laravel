<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RecommendsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index']);

Route::get('/search', [SearchController::class, 'index']);

Route::get('/song/detail', [SongController::class, 'detail']);

Route::get('/album/detail', [AlbumController::class, 'detail']);

Route::get('/artist/detail', [ArtistController::class, 'detail']);

Route::get('/auth/login', [AuthController::class, 'login'])->name('login');
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::get('/auth/logout', [AuthController::class, 'logout']);
Route::get('/login', [AuthController::class, 'login']);

Route::get('/recommends', [RecommendsController::class, 'index']);
Route::post('/recommends/post', [RecommendsController::class, 'post']);
Route::get('/recommends/detail', [RecommendsController::class, 'detail']);

Route::get('/mypage', [MypageController::class, 'index']);
Route::get('/mypage/user', [MypageController::class, 'user']);
Route::post('/mypage/setProfileAlbum', [MypageController::class, 'setProfileAlbum']);
