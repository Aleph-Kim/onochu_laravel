<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

// 메인
Route::get('/', IndexController::class)->name('index');

Route::fallback(function () {
    return redirect("/");
});
