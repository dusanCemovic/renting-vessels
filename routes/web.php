<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationWebController;

Route::get('/', [ReservationWebController::class, 'viewAllVessels']);
Route::get('/vessels/{id}/tasks', [ReservationWebController::class, 'vesselTasksView'])
    ->name('vessel.tasks');


Route::get('/reserve', [ReservationWebController::class, 'showReserveForm'])->name('reserve.form');
Route::post('/reserve', [ReservationWebController::class, 'submitReserveForm'])->name('reserve.submit');
