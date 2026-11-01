<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VolunteerReportController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\AreaReportController;
use App\Models\Area;
use App\Http\Controllers\ReportRecapController;
use App\Http\Controllers\DashboardController;

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

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('auth')
        ->name('dashboard');

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

// Fitur Relawan Pemantau
Route::middleware(['auth', 'role:relawan_pemantau'])
    ->prefix('relawan')
    ->name('relawan.')
    ->group(function () {

        Route::get('/reports',
            [VolunteerReportController::class, 'index']
        )->name('reports.index');

        Route::get('/reports/{report}',
            [VolunteerReportController::class, 'show']
        )->name('reports.show');

        Route::post('/reports/{report}/review',
            [VolunteerReportController::class, 'review']
        )->name('reports.review');

        Route::post('/reports/{report}/complete',
            [VolunteerReportController::class, 'complete']
        )->name('reports.complete');
    });


// Fitur Koordinator Relawan
Route::middleware(['auth', 'role:koordinator_relawan'])
    ->prefix('koordinator')
    ->name('koordinator.')
    ->group(function () {

        Route::get('/reports',
            [CoordinatorReportController::class, 'index']
        )->name('reports.index');

        Route::get('/reports/{report}',
            [CoordinatorReportController::class, 'show']
        )->name('reports.show');

        Route::post('/reports/{report}/forward',
            [CoordinatorReportController::class, 'forward']
        )->name('reports.forward');

        Route::post('/reports/{report}/complete',
            [CoordinatorReportController::class, 'complete']
        )->name('reports.complete');
    });

// Informasi laporan aktif berdasarkan wilayah
Route::get(
    '/areas/{area}/active-reports',
    [AreaReportController::class, 'activeReports']
)
    ->middleware('auth')
    ->name('areas.active-reports');

// Rekap laporan untuk Relawan dan Koordinator
Route::middleware([
    'auth',
    'role:relawan_pemantau,koordinator_relawan'
])->group(function () {

    Route::get('/rekap', [ReportRecapController::class, 'index'])
        ->name('recap.index');

});