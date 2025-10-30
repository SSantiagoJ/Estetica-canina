<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CatalogoController;

// Prueba
Route::get('/pruebaPaypal', function () {
    return view('pruebaPaypal');
})->name('pruebaPaypal');


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

Route::get('/header', function () {
    return view('header');
})->name('header');

//Proteccion mediante rol
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard'); 
});


    Route::get('/admin_dashboard', function () {
        if (auth()->user()->rol !== 'Admin' && auth()->user()->rol !== 'Empleado') {
            abort(403, 'Acceso no autorizado');
        }
        return view('admin_dashboard');
    });
    
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// menu
Route::get('/menu', function () {
    // Si el archivo es 'resources/views/catalogo.blade.php'
    return view('menu');

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

Route::get('/reservas/resumen', [ReservaController::class, 'resumenPago'])->name('reservas.resumen');
Route::get('/reservas/guardar-pago', [ReservaController::class, 'guardarPago'])->name('reservas.guardarPago');

Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::post('/perfil/mascotas', [PerfilController::class, 'storeMascota'])->name('perfil.mascotas.store');
    Route::put('/perfil/mascotas/{id}', [PerfilController::class, 'updateMascota'])->name('perfil.mascotas.update');
    Route::put('/perfil/actualizar', [PerfilController::class, 'updatePerfil'])->name('perfil.update');
    Route::delete('/mascotas/{id}', [PerfilController::class, 'destroy'])
    ->name('mascotas.destroy')
    ->middleware('auth');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin_dashboard', [GestorController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/usuarios', [GestorController::class, 'usuarios'])->name('admin.usuarios');
    Route::get('/admin/mascotas', [GestorController::class, 'mascotas'])->name('admin.mascotas');
    Route::get('/admin/reservas', [GestorController::class, 'reservas'])->name('admin.reservas');
    Route::get('/admin/servicios', [GestorController::class, 'servicios'])->name('admin.servicios');
});

// Rutas CRUD de Servicios
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/servicios/crear', [ServicioController::class, 'create'])->name('admin.servicios.create');
    Route::post('/admin/servicios', [ServicioController::class, 'store'])->name('admin.servicios.store');
    Route::get('/admin/servicios/{id}/editar', [ServicioController::class, 'edit'])->name('admin.servicios.edit');
    Route::put('/admin/servicios/{id}', [ServicioController::class, 'update'])->name('admin.servicios.update');
    Route::delete('/admin/servicios/{id}', [ServicioController::class, 'destroy'])->name('admin.servicios.destroy');
    Route::post('/admin/servicios/upload-image', [ServicioController::class, 'uploadImage'])->name('admin.servicios.uploadImage');
});

Route::post('/admin/reservas/update', [GestorController::class, 'update'])
    ->name('admin.reservas.update');

    //Boleta
Route::get('/reservas/boleta/{id_pago}', [ReservaController::class, 'generarBoleta'])->name('reservas.boleta');

    //Tester
    Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Hola Mundo PDF</h1>');
    $path = storage_path('app/public/boletas/test.pdf');
    \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app/public/boletas'));
    $pdf->save($path);
    return 'PDF generado en: ' . $path;

    
});

// MIS RESERVAS
Route::middleware(['auth'])->group(function () {
    // Nuevas rutas para Mis Reservas
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas');
    Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
    Route::get('/reservas/{id}/editar', [ReservaController::class, 'edit'])->name('reservas.edit');
});
