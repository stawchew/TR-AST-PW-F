<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ScheduleController as UserScheduleController;
use App\Http\Controllers\User\TicketController as UserTicketController;

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
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Katalog jadwal
    Route::get('/schedules', [UserScheduleController::class, 'index'])->name('schedules.index');

    // Pesan tiket untuk jadwal tertentu
    Route::get('/schedules/{schedule}/book', [UserTicketController::class, 'create'])->name('tickets.create');
    Route::post('/schedules/{schedule}/book', [UserTicketController::class, 'store'])->name('tickets.store');

    // Riwayat, detail, edit, batal
    Route::resource('tickets', UserTicketController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';