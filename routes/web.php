<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MotivationController;

Route::get('/', function () {
    return view('products.index');
});

Route::get('/motivation/random', [MotivationController::class, 'random'])->name('motivation.random');
