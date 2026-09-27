<?php

use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', ['services' => Service::where('is_active', true)->orderBy('name')->get()]);
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cliente
    Route::get('/mis-citas', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/reservar', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/reservar', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/citas/{appointment}/cancelar', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Panel barbero / admin
    Route::middleware('role:barber,admin')->prefix('panel')->name('admin.')->group(function () {
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');
        Route::patch('/citas/{appointment}/completar', [AgendaController::class, 'complete'])->name('appointments.complete');
        Route::resource('services', ServiceController::class)->except('show');
    });
});
