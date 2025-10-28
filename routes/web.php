<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Employee\LeaveController;
use App\Http\Controllers\Admin\LeaveManageController;
use App\Http\Controllers\Admin\LeaveReportController;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        }
        abort(403, 'Unauthorized access');
    })->name('dashboard');

    //Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Employee Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('dashboard');

    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves/store', [LeaveController::class, 'store'])->name('leaves.store');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Leave Management Routes
    Route::get('/leaves', [LeaveManageController::class, 'index'])->name('leaves.index');
    Route::post('/leaves/{leave}/approve', [LeaveManageController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveManageController::class, 'reject'])->name('leaves.reject');
    Route::get('/leaves/report', [LeaveManageController::class, 'report'])->name('leaves.report');

    // Advanced Reports
    Route::get('/reports', [LeaveReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [LeaveReportController::class, 'export'])->name('reports.export');
});

require __DIR__ . '/auth.php';
