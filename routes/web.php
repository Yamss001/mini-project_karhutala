<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

// Halaman utama
Route::redirect('/', '/login');

// Authentication untuk guest
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

});

// Halaman yang membutuhkan login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});

// Akses khusus Relawan Pemantau
Route::middleware(['auth', 'role:relawan_pemantau'])
    ->get('/relawan/akses', function () {
        return response('Akses Relawan Pemantau berhasil.');
    })->name('relawan.akses');


// Akses khusus Koordinator Relawan
Route::middleware(['auth', 'role:koordinator_relawan'])
    ->get('/koordinator/akses', function () {
        return response('Akses Koordinator Relawan berhasil.');
    })->name('koordinator.akses');


Route::middleware(['auth', 'role:pelapor'])->group(function () {

    Route::resource('reports', ReportController::class)
        ->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
        ]);

    Route::get(
        '/reports/{report}/evidence',
        [ReportController::class, 'downloadEvidence']
    )->name('reports.evidence');

});
