<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

// 🔹 Tampilan untuk USER (tanpa login)
Route::get('/', [ReportController::class, 'create'])->name('home'); // alias home
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
Route::get('/cek-laporan', [ReportController::class, 'cekForm']);
Route::post('/cek-laporan', [ReportController::class, 'cekStatus']);

// 🔹 LOGIN ADMIN
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.process');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// 🔹 ADMIN AREA (dengan middleware proteksi)
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::delete('/reports/destroy-all', [ReportController::class, 'destroyAll'])->name('reports.destroyAll');    
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('admin/reports/export', [ReportController::class, 'export'])->name('reports.export');

});
