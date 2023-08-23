<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\PresenceController;

Route::post('/clock-in', [PresenceController::class, 'clockIn']);
Route::post('/clock-out', [PresenceController::class, 'clockOut']);
Route::get('/generate-pay-slip', [PresenceController::class, 'generatePaySlip']);