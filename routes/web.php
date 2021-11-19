<?php

use Illuminate\Support\Facades\Route;
use Kevincobain2000\LaravelERD\Controllers\LaravelERDController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your package. These
| routes are loaded by your package ServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::get(config('laravel-erd.url'), [LaravelERDController::class, 'index'])
    ->name('laravel-erd.index')
    ->middleware(config('laravel-erd.middlewares'));