<?php

use Illuminate\Http\Request;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::post('/rfid/scan', [RfidController::class, 'scan']);
Route::get('/rfid/status', [RfidController::class, 'status']);
Route::get('/attendance/today', [AttendanceController::class, 'todayApi']);
































