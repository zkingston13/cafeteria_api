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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-session', function() {
    return [
        'driver_config' => config('session.driver'),
        'driver_env' => env('SESSION_DRIVER'),
        'session_path' => storage_path('framework/sessions'),
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ];
});
