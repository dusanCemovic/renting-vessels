<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationAPIController;

Route::post('/reserve', [ReservationAPIController::class,'reserve']);
Route::get('/vessels/{id}/tasks', [ReservationAPIController::class,'vesselTasks']);
Route::post('/vessels/{id}/maintenance', [ReservationAPIController::class,'addMaintenance']);
