<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\CalificacionController;



// Ruta raíz - Mostrar menú
Route::get('/', function () {
    try {
        $calificacionController = new \App\Http\Controllers\CalificacionController();
        $calificaciones = $calificacionController->calificacionesDestacadas();
    } catch (\Exception $e) {
        $calificaciones = collect(); // Colección vacía si hay error
    }
    
    return view('cliente.inicio', [
        'calificaciones' => $calificaciones
    ]);
})->name('home');

// Prueba
Route::get('/pruebaPaypal', function () {
    return view('dev.prueba-paypal');
})->name('pruebaPaypal');

Route::get('/imagenes/razas/{razaImagen}', [GestorController::class, 'mostrarFotoRaza'])
    ->name('razas.imagenes.show');

Route::get('/imagenes/servicios/{servicio}', [ServicioController::class, 'mostrarImagen'])
    ->name('servicios.imagenes.show');


// Inicio de Sesion 
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.process');

Route::post('/login/mfa', [AuthController::class, 'verifyMfa'])
    ->middleware('throttle:6,1')
    ->name('login.mfa');

Route::get('/intranet/login', function () {
    return view('intranet.login');
})->name('intranet.login');

Route::post('/intranet/login', [AuthController::class, 'intranetLogin'])
    ->middleware('throttle:5,1')
    ->name('intranet.login.process');

Route::post('/intranet/login/mfa', [AuthController::class, 'verifyMfa'])
    ->middleware('throttle:6,1')
    ->name('intranet.login.mfa');

Route::middleware(['role:Admin,Empleado,Supervisor'])->group(function () {
    Route::get('/intranet/perfil', [PerfilController::class, 'intranetPerfil'])
        ->name('intranet.perfil');
    Route::put('/intranet/perfil/actualizar', [PerfilController::class, 'updateIntranetPerfil'])
        ->name('intranet.perfil.update');
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.process');

//logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Dashboards

Route::get('/header', function () {
    return view('partials.header');
})->name('header');

//Proteccion mediante rol
// El dashboard se elimina ya que ahora los clientes van directamente al menú


Route::middleware(['role:Admin,Empleado'])->group(function () {
    Route::get('/admin_dashboard', [GestorController::class, 'index'])->name('admin.dashboard');
});
Route::middleware(['role:Admin'])->group(function () {
    // Rutas adicionales del admin
    Route::get('/admin/usuarios', [GestorController::class, 'usuarios'])->name('admin.usuarios');
    Route::get('/admin/usuarios/crear', [GestorController::class, 'crearUsuario'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [GestorController::class, 'guardarUsuario'])->name('admin.usuarios.store');
    Route::get('/admin/mascotas', [GestorController::class, 'mascotas'])->name('admin.mascotas');
    Route::post('/admin/mascotas/razas', [GestorController::class, 'guardarFotoRaza'])->name('admin.mascotas.razas.store');
    Route::delete('/admin/mascotas/razas/{razaImagen}', [GestorController::class, 'eliminarFotoRaza'])->name('admin.mascotas.razas.destroy');
    Route::get('/admin/reservas', [GestorController::class, 'reservas'])->name('admin.reservas');
    Route::get('/admin/servicios', [GestorController::class, 'servicios'])->name('admin.servicios');
});
    
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// La ruta del menú ya está definida arriba como ruta raíz


// Reserva
Route::middleware(['auth', 'role:Cliente'])->group(function () {
    Route::get('/reservas/seleccion-mascota', [ReservaController::class, 'seleccionMascota'])
        ->name('reservas.seleccionMascota');

    Route::post('/reservas/seleccion-servicio', [ReservaController::class, 'seleccionServicio'])
        ->name('reservas.seleccionServicio');

    Route::post('/reservas/obtener-horarios', [ReservaController::class, 'obtenerHorariosDisponibles'])
        ->name('reservas.obtenerHorarios');

    Route::post('/reservas/pago', [ReservaController::class, 'pago'])
        ->name('reservas.pago');

    Route::post('/reservas/finalizar', [ReservaController::class, 'finalizar'])
        ->name('reservas.finalizar');

    Route::get('/reservas/resumen', [ReservaController::class, 'resumenPago'])
        ->name('reservas.resumen');

    Route::post('/reservas/guardar-pago', [ReservaController::class, 'guardarPago'])
        ->name('reservas.guardarPago');

    Route::get('/reservas/boleta/{id_pago}', [ReservaController::class, 'generarBoleta'])
        ->name('reservas.boleta');

    Route::get('/reservas/boleta/{id_pago}/descargar', [ReservaController::class, 'descargarBoleta'])
        ->name('reservas.boleta.descargar');
});

Route::middleware(['auth', 'role:Cliente'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::post('/perfil/mascotas', [PerfilController::class, 'storeMascota'])->name('perfil.mascotas.store');
    Route::put('/perfil/mascotas/{id}', [PerfilController::class, 'updateMascota'])->name('perfil.mascotas.update');
    Route::put('/perfil/actualizar', [PerfilController::class, 'updatePerfil'])->name('perfil.update');
    Route::delete('/mascotas/{id}', [PerfilController::class, 'destroy'])
    ->name('mascotas.destroy')
    ->middleware('auth');
});



// Rutas CRUD de Servicios
Route::middleware(['role:Admin'])->group(function () {
    Route::get('/admin/servicios/crear', [ServicioController::class, 'create'])->name('admin.servicios.create');
    Route::post('/admin/servicios', [ServicioController::class, 'store'])->name('admin.servicios.store');
    Route::get('/admin/servicios/{id}/editar', [ServicioController::class, 'edit'])->name('admin.servicios.edit');
    Route::put('/admin/servicios/{id}', [ServicioController::class, 'update'])->name('admin.servicios.update');
    Route::delete('/admin/servicios/{id}', [ServicioController::class, 'destroy'])->name('admin.servicios.destroy');
    Route::post('/admin/servicios/upload-image', [ServicioController::class, 'uploadImage'])->name('admin.servicios.uploadImage');
});

Route::post('/admin/reservas/update', [GestorController::class, 'update'])
    ->name('admin.reservas.update')
    ->middleware(['role:Admin']);

    //Tester
    Route::middleware(['role:Admin'])->get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Hola Mundo PDF</h1>');
    $path = storage_path('app/boletas/test.pdf');
    \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app/boletas'));
    $pdf->save($path);
    return 'PDF generado en: ' . $path;

    
});

// MIS RESERVAS
Route::middleware(['auth', 'role:Cliente'])->group(function () {
    // Nuevas rutas para Mis Reservas
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas');
    Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
    Route::get('/reservas/{id}/editar', [ReservaController::class, 'edit'])->name('reservas.edit');
    Route::put('/reservas/{id}', [ReservaController::class, 'update'])->name('reservas.update');
});

// RUTAS PARA CALIFICACIONES
Route::middleware(['auth', 'role:Cliente'])->group(function () {
    Route::post('/calificacion/guardar', [CalificacionController::class, 'guardarCalificacion'])->name('calificacion.guardar');
});


// ============================================
// RUTAS PARA EMPLEADO - PANEL DEL DÍA
// ============================================
Route::middleware(['role:Admin,Empleado,Supervisor'])->group(function () {
Route::prefix('empleado')->name('empleado.')->group(function () {
    
    // Vista del panel del día con reservas asignadas y dashboards
    Route::get('/panel-del-dia', [EmpleadoController::class, 'panelDelDia'])
        ->name('panel.del.dia');
    
    // Rutas AJAX para el panel del día
    Route::post('/filtrar-reservas', [EmpleadoController::class, 'filtrarReservas'])
        ->name('filtrar.reservas');
    Route::get('/cargar-reserva/{id}', [EmpleadoController::class, 'cargarReserva'])
        ->name('cargar.reserva');
    Route::post('/marcar-atendido/{id}', [EmpleadoController::class, 'marcarComoAtendido'])
        ->name('marcar.atendido');
});

// ============================================
// RUTAS PARA EMPLEADO - GESTIÓN DE TURNOS
// ============================================
Route::prefix('empleado')->name('empleado.')->group(function () {
    
    // Vista principal de gestionar turnos
    Route::get('/gestionar-turnos', [EmpleadoController::class, 'gestionarTurnos'])
        ->name('gestionar.turnos');
    
    // Crear nuevo turno
    Route::post('/turnos', [EmpleadoController::class, 'storeTurno'])
        ->name('turnos.store');
    
    // Actualizar turno existente
    Route::put('/turnos/{id}', [EmpleadoController::class, 'updateTurno'])
        ->name('turnos.update');
    
    // Eliminar turno
    Route::delete('/turnos/{id}', [EmpleadoController::class, 'destroyTurno'])
        ->name('turnos.destroy');
});
// ============================================
// RUTAS PARA EMPLEADO - DASHBOARD
// ============================================
Route::prefix('empleado')->name('empleado.')->group(function () {

    Route::get('dashboard', [EmpleadoController::class, 'dashboardEmpleado'])
        ->name('dashboard');

});

// ============================================
// RUTAS PARA EMPLEADO - GESTIÓN DE NOVEDADES
// ============================================
Route::prefix('empleado')->name('empleado.')->group(function () {
    
    // Vista principal de gestionar novedades
    Route::get('/gestionar-novedades', [EmpleadoController::class, 'gestionarNovedades'])
        ->name('gestionar.novedades');
    
    // Crear nueva novedad
    Route::post('/novedades', [EmpleadoController::class, 'storeNovedad'])
        ->name('novedades.store');
    
    // Actualizar novedad existente
    Route::put('/novedades/{id}', [EmpleadoController::class, 'updateNovedad'])
        ->name('novedades.update');
    
    // Eliminar novedad
    Route::delete('/novedades/{id}', [EmpleadoController::class, 'destroyNovedad'])
        ->name('novedades.destroy');
});

// ============================================
// RUTAS PARA EMPLEADO - BANDEJA DE RESERVAS
// ============================================
Route::prefix('empleado')->name('empleado.')->group(function () {
    
    // Vista principal de bandeja de reservas
    Route::get('/bandeja-reservas', [EmpleadoController::class, 'bandejaReservas'])
        ->name('bandeja.reservas');
    
    // Ver detalles de una reserva
    Route::get('/reservas/{id}', [EmpleadoController::class, 'verReserva'])
        ->name('reservas.ver');
    
    // Atender una reserva (cambiar estado)
    Route::put('/reservas/{id}/atender', [EmpleadoController::class, 'atenderReserva'])
        ->name('reservas.atender');
    // Guardar la atención de una reserva
Route::post('/reservas/guardar-atencion', 
            [EmpleadoController::class, 'guardarAtencion']
    )->name('reservas.guardarAtencion');

});
Route::prefix('empleado/notificaciones')->group(function () {

    Route::get('/', [NotificacionController::class, 'index'])->name('empleado.notificaciones');

    Route::post('/store', [NotificacionController::class, 'store'])
        ->name('empleado.notificaciones.store');

    Route::put('/update', [NotificacionController::class, 'update'])
        ->name('empleado.notificaciones.update');

    Route::post('/probar/{id}', [NotificacionController::class, 'probar'])
        ->name('empleado.notificaciones.probar');

    Route::post('/cambiar-estado', [NotificacionController::class, 'cambiarEstado'])
        ->name('empleado.notificaciones.estado');
    Route::post('/{id}/ejecutar',
    [NotificacionController::class, 'ejecutar']
)->name('empleado.notificaciones.ejecutar');
    Route::post('/custom',
        [NotificacionController::class, 'enviarCorreoPersonalizado'])
        ->name('empleado.notificaciones.custom');

});
});
