<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;

Route::get('/', fn () => redirect()->route('login'));

// Redirect sesuai role setelah login
Route::get('/dashboard', function () {
    return request()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');

// Edit profil (semua role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Khusus admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    Route::resource('routes', RouteController::class)->except('show');
    Route::resource('schedules', ScheduleController::class)->except('show');
});

// Khusus user
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::view('/dashboard', 'user.dashboard')->name('dashboard');
});

require __DIR__.'/auth.php';