<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VesselController;

// home
Route::get('/', [MainController::class,'index'])->name('home');

// Vessels routes
Route::prefix('vessels')->name('vessels.')->group(function () {
    Route::get('/', [VesselController::class, 'index'])->name('index');
    Route::get('/{vessel}', [VesselController::class, 'show'])->name('show');
});

// Reservations routes grouped
Route::prefix('reservations')->name('reservations.')->group(function () {

    Route::get('/', [ReservationController::class, 'index'])->name('index');

    Route::get('/reserve', [ReservationController::class, 'create'])->name('create');
    Route::post('/reserve', [ReservationController::class, 'store'])->name('store');

    Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
});
