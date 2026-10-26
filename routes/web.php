<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\User\LoanController as UserLoanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => 'Halaman Admin')->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('items', ItemController::class)->except('show');
    Route::resource('loans', AdminLoanController::class)->only(['index', 'create', 'store']);
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', fn () => 'Halaman User')->name('dashboard');
    Route::resource('loans', UserLoanController::class)->only(['index', 'create', 'store']);
});

require __DIR__.'/auth.php';
