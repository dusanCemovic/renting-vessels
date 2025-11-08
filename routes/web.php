<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VesselController;
use App\Http\Controllers\EquipmentController;

// home
Route::get('/', [MainController::class,'index'])->name('home');

// Vessels routes
Route::prefix('vessels')->name('vessels.')->group(function () {
    Route::get('/', [VesselController::class, 'index'])->name('index');
    Route::get('/create', [VesselController::class, 'create'])->name('create');
    Route::post('/', [VesselController::class, 'store'])->name('store');
    Route::get('/{vessel}', [VesselController::class, 'show'])->name('show');
    Route::get('/{vessel}/edit', [VesselController::class, 'edit'])->name('edit');
    Route::put('/{vessel}', [VesselController::class, 'update'])->name('update');
    Route::delete('/{vessel}', [VesselController::class, 'destroy'])->name('destroy');
});

// Equipment management routes
Route::prefix('equipments')->name('equipments.')->group(function () {
    Route::get('/', [EquipmentController::class, 'index'])->name('index');
    Route::get('/create', [EquipmentController::class, 'create'])->name('create');
    Route::post('/', [EquipmentController::class, 'store'])->name('store');
    Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
    Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
    Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
});

// Reservations routes grouped
Route::prefix('reservations')->name('reservations.')->group(function () {

    Route::get('/', [ReservationController::class, 'index'])->name('index');

    Route::get('/reserve', [ReservationController::class, 'create'])->name('create');
    Route::post('/reserve', [ReservationController::class, 'store'])->name('store');

    Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
});

// Maintenances routes grouped
Route::prefix('maintenances')->name('maintenances.')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index'])->name('index');
    Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
});

