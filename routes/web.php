<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\JenisperangkatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DetailperangkatController;
use App\Http\Controllers\PengaturansistemController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/', [DashboardController::class, 'generalDashboard'])->name('gDashboard');

    // Auth routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Laporan (tanpa login)
    Route::post('/laporan/kirim', [LaporanController::class, 'sendReport'])->name('send.report');

    // Reset Password via OTP
    Route::get('/password/request', [AuthController::class, 'showRequestForm'])->name('password.request');
    Route::post('/password/send-otp', [AuthController::class, 'sendOtp'])->name('password.sendOtp');
    Route::post('/password/verify', [AuthController::class, 'verifyOtp'])->name('password.verify');
});

/*
|--------------------------------------------------------------------------
| Public (no auth) utilities
|--------------------------------------------------------------------------
*/
Route::get('/reloaddashboard', [DashboardController::class, 'reload']);
Route::get('/checkperangkat', [PerangkatController::class, 'checkPerangkat']);
Route::get('/pingperangkat', [PerangkatController::class, 'pingPerangkat']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |-----------------------------------
    | Dashboard & Pengaturan Sistem
    |-----------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'showdashboard'])->name('dashboard');

    Route::get('/pengaturansistem', [PengaturansistemController::class, 'index'])->name('pengaturansistem.index');
    Route::put('/pengaturansistem', [PengaturansistemController::class, 'update'])->name('pengaturansistem.update');
    Route::delete('/pengaturansistem/reset', [PengaturansistemController::class, 'reset'])->name('pengaturansistem.reset');

    /*
    |-----------------------------------
    | Laporan
    |-----------------------------------
    */
    Route::get('/laporan', [LaporanController::class, 'showReport'])->name('report.index');
    Route::post('/emailpenerima/ganti', [LaporanController::class, 'gantiemail'])->name('change.emaillaporan');
    Route::get('/clearlaporan', [LaporanController::class, 'clearlaporan'])->name('laporanperangkat.clear');

    /*
    |-----------------------------------
    | Perangkat & Detail Perangkat
    |-----------------------------------
    */
    Route::get('/perangkat/data', function () {
        return response()->json([
            'perangkat' => \App\Models\Perangkat::all()
        ]);
    });

    Route::get('/reloaddetailperangkat/{perangkat_id}', [DetailperangkatController::class, 'showByPerangkat'])
        ->name('detailperangkat.byPerangkat');

    Route::resource('/perangkat', PerangkatController::class);
    Route::post('/importperangkat', [PerangkatController::class, 'import'])->name('perangkat.import');
    Route::get('/clearlog', [PerangkatController::class, 'clearlog'])->name('logperangkat.clear');
    Route::get('/logperangkat', [PerangkatController::class, 'logperangkat'])->name('logperangkat.index');

    Route::resource('/detailperangkat', DetailperangkatController::class);
    Route::resource('/jenisperangkat', JenisperangkatController::class);

    /*
    |-----------------------------------
    | User Management
    |-----------------------------------
    */
    Route::resource('/user', UserController::class);

    /*
    |-----------------------------------
    | Profile
    |-----------------------------------
    */
    Route::get('/profile', [AuthController::class, 'show'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');

    /*
    |-----------------------------------
    | Logout
    |-----------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
