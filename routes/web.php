<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\ReturnLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ItemController as UserItemController;
use App\Http\Controllers\User\LoanController as UserLoanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ItemLabelController;
use App\Http\Controllers\Admin\ScanController as AdminScanController;
use App\Http\Controllers\User\ScanController as UserScanController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('items', ItemController::class)->except('show');

    Route::resource('loans', AdminLoanController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
    Route::get('loans/{loan}/reject', [AdminLoanController::class, 'rejectCreate'])->name('loans.reject.create');
    Route::post('loans/{loan}/reject', [AdminLoanController::class, 'rejectStore'])->name('loans.reject.store');
    Route::get('loans/{loan}/return', [ReturnLogController::class, 'create'])->name('loans.return.create');
    Route::post('loans/{loan}/return', [ReturnLogController::class, 'store'])->name('loans.return.store');

    Route::get('return-logs', [ReturnLogController::class, 'index'])->name('return-logs.index');

    Route::resource('users', UserController::class)->except('show');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    Route::get('reports/loans', [ReportController::class, 'loans'])->name('reports.loans');
    Route::get('reports/loans/pdf', [ReportController::class, 'loansPdf'])->name('reports.loans.pdf');
    Route::get('reports/loans/excel', [ReportController::class, 'loansExcel'])->name('reports.loans.excel');

    Route::get('items/labels', [ItemLabelController::class, 'index'])->name('items.labels');
    Route::get('scan', [AdminScanController::class, 'index'])->name('scan');
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('items', [UserItemController::class, 'index'])->name('items.index');
    Route::resource('loans', UserLoanController::class)->only(['index', 'create', 'store']);
    Route::patch('loans/{loan}/cancel', [UserLoanController::class, 'cancel'])->name('loans.cancel');

    Route::get('scan', [UserScanController::class, 'index'])->name('scan');
});

require __DIR__ . '/auth.php';