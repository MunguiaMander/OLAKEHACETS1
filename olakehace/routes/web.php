<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [EventController::class, 'index'])->name('home');
Route::post('/search', [EventController::class, 'search'])->name('search.events');
Route::post('/report', [ReportController::class, 'reportEvent'])->name('report.event');
