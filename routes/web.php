<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PerfilController;

// Inicio de Sesion 
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/register', function () {
    return view('register'); // resources/views/register.blade.php
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.process');

//logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Dashboards
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/header', function () {
    return view('header');
})->name('header');

//Proteccion mediante rol
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::get('/admin_dashboard', function () {
        if (auth()->user()->rol !== 'Admin' && auth()->user()->rol !== 'Empleado') {
            abort(403, 'Acceso no autorizado');
        }
        return view('admin_dashboard');
    });
    Route::get('/catalogo', function () {
    // Si el archivo es 'resources/views/catalogo.blade.php'
    return view('catalogo');
    });
});

// Reserva
Route::middleware(['auth'])->group(function () {
    Route::get('/reservas/seleccion-mascota', [ReservaController::class, 'seleccionMascota'])
        ->name('reservas.seleccionMascota');

    Route::post('/reservas/seleccion-servicio', [ReservaController::class, 'seleccionServicio'])
        ->name('reservas.seleccionServicio');

    Route::post('/reservas/pago', [ReservaController::class, 'pago'])
        ->name('reservas.pago');

    Route::post('/reservas/finalizar', [ReservaController::class, 'finalizar'])
        ->name('reservas.finalizar');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::post('/perfil/mascotas', [PerfilController::class, 'storeMascota'])->name('perfil.mascotas.store');
    Route::put('/perfil/mascotas/{id}', [PerfilController::class, 'updateMascota'])->name('perfil.mascotas.update');
    Route::put('/perfil/actualizar', [PerfilController::class, 'updatePerfil'])->name('perfil.update');
    Route::delete('/mascotas/{id}', [PerfilController::class, 'destroy'])
    ->name('mascotas.destroy')
    ->middleware('auth');
});