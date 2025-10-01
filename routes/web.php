<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
});
