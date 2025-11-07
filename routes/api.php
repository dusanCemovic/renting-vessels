<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

Route::post('/reserve', [ReservationController::class,'reserve']);
Route::get('/vessels/{id}/tasks', [ReservationController::class,'vesselTasks']);
Route::post('/vessels/{id}/maintenance', [ReservationController::class,'addMaintenance']);
