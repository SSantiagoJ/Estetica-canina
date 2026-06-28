<?php

use App\Http\Controllers\Api\AdminMascotaApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CatalogoApiController;
use App\Http\Controllers\Api\ConfiguracionApiController;
use App\Http\Controllers\Api\EmpleadoApiController;
use App\Http\Controllers\Api\IngresoApiController;
use App\Http\Controllers\Api\MascotaApiController;
use App\Http\Controllers\Api\NovedadApiController;
use App\Http\Controllers\Api\PagoApiController;
use App\Http\Controllers\Api\PerfilApiController;
use App\Http\Controllers\Api\RazaApiController;
use App\Http\Controllers\Api\ReservaApiController;
use App\Http\Controllers\Api\ServicioApiController;
use App\Http\Controllers\Api\TurnoApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', fn () => response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'API Pet Grooming activa.',
        'version' => 'v1',
    ]))->name('health');

    Route::get('/catalogo', [CatalogoApiController::class, 'index'])->name('catalogo.index');

    Route::prefix('auth')->name('auth.')->middleware('web')->group(function () {
        Route::get('/csrf-token', fn () => response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Token CSRF generado correctamente.',
            'data' => [
                'csrf_token' => csrf_token(),
            ],
        ]))->name('csrf-token');

        Route::post('/login', [AuthApiController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login');
        Route::post('/intranet-login', [AuthApiController::class, 'intranetLogin'])
            ->middleware('throttle:5,1')
            ->name('intranet-login');
        Route::post('/mfa', [AuthApiController::class, 'mfa'])
            ->middleware('throttle:6,1')
            ->name('mfa');

        Route::middleware('jwt.auth')->group(function () {
            Route::get('/me', [AuthApiController::class, 'me'])->name('me');
            Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');
        });
    });

    Route::get('/servicios', [ServicioApiController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/{servicio}', [ServicioApiController::class, 'show'])->name('servicios.show');

    Route::get('/razas', [RazaApiController::class, 'index'])->name('razas.index');
    Route::get('/razas/{raza}', [RazaApiController::class, 'show'])->name('razas.show');

    Route::post('/reservas/horarios-disponibles', [ReservaApiController::class, 'horariosDisponibles'])
        ->middleware('throttle:30,1')
        ->name('reservas.horarios');

    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/me', [AuthApiController::class, 'me'])->name('me');

        Route::middleware('role:Cliente')->group(function () {
            Route::apiResource('mascotas', MascotaApiController::class);

            Route::prefix('clientes')->name('clientes.')->group(function () {
                Route::get('/perfil', [PerfilApiController::class, 'show'])->name('perfil.show');
                Route::put('/perfil', [PerfilApiController::class, 'update'])->name('perfil.update');
                Route::patch('/perfil', [PerfilApiController::class, 'update'])->name('perfil.patch');

                Route::apiResource('mascotas', MascotaApiController::class)
                    ->names('mascotas');

                Route::get('/reservas', [ReservaApiController::class, 'index'])->name('reservas.index');
                Route::post('/reservas', [ReservaApiController::class, 'store'])->name('reservas.store');
                Route::get('/reservas/{reserva}', [ReservaApiController::class, 'show'])->name('reservas.show');
                Route::put('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.update');
                Route::patch('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.patch');
                Route::delete('/reservas/{reserva}', [ReservaApiController::class, 'destroy'])->name('reservas.destroy');
            });

            Route::post('/reservas', [ReservaApiController::class, 'store'])->name('reservas.store');
            Route::put('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.update');
            Route::patch('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.patch');
            Route::delete('/reservas/{reserva}', [ReservaApiController::class, 'destroy'])->name('reservas.destroy');
        });

        Route::middleware('role:Cliente,Admin,Empleado,Supervisor')->group(function () {
            Route::get('/reservas', [ReservaApiController::class, 'index'])->name('reservas.index');
            Route::get('/reservas/{reserva}', [ReservaApiController::class, 'show'])->name('reservas.show');
            Route::get('/reservas/{reserva}/pagos', [PagoApiController::class, 'index'])->name('reservas.pagos.index');
            Route::post('/reservas/{reserva}/pagos', [PagoApiController::class, 'store'])->name('reservas.pagos.store');
            Route::post('/reservas/{reserva}/pago', [PagoApiController::class, 'store'])->name('reservas.pago.store');
            Route::get('/reservas/{reserva}/boleta', [PagoApiController::class, 'boletaReserva'])->name('reservas.boleta');
            Route::get('/reservas/{reserva}/boleta/descargar', [PagoApiController::class, 'descargarBoletaReserva'])->name('reservas.boleta.descargar');
            Route::get('/pagos/{pago}/boleta', [PagoApiController::class, 'boleta'])->name('pagos.boleta');
            Route::get('/pagos/{pago}/boleta/descargar', [PagoApiController::class, 'descargarBoleta'])->name('pagos.boleta.descargar');
        });

        Route::prefix('empleado')->name('empleado.')->middleware('role:Admin,Empleado,Supervisor')->group(function () {
            Route::get('/panel-del-dia', [EmpleadoApiController::class, 'panelDelDia'])->name('panel-del-dia');
            Route::get('/reservas', [EmpleadoApiController::class, 'reservas'])->name('reservas.index');
            Route::put('/reservas/{reserva}/atender', [EmpleadoApiController::class, 'atender'])->name('reservas.atender');
            Route::patch('/reservas/{reserva}/atender', [EmpleadoApiController::class, 'atender'])->name('reservas.atender.patch');
            Route::apiResource('turnos', TurnoApiController::class);
            Route::apiResource('novedades', NovedadApiController::class)
                ->parameters(['novedades' => 'novedad']);
            Route::get('/ingresos', [IngresoApiController::class, 'index'])->name('ingresos.index');
        });

        Route::prefix('supervisor')->name('supervisor.')->middleware('role:Admin,Supervisor')->group(function () {
            Route::get('/ingresos', [IngresoApiController::class, 'index'])->name('ingresos.index');
            Route::get('/metricas', [IngresoApiController::class, 'metricas'])->name('metricas');
            Route::get('/reportes/ingresos-excel', [IngresoApiController::class, 'reporteExcel'])->name('reportes.ingresos-excel');
        });

        Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
            Route::apiResource('usuarios', UsuarioApiController::class);

            Route::get('/reservas', [ReservaApiController::class, 'index'])->name('reservas.index');
            Route::get('/reservas/{reserva}', [ReservaApiController::class, 'show'])->name('reservas.show');
            Route::put('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.update');
            Route::patch('/reservas/{reserva}', [ReservaApiController::class, 'update'])->name('reservas.patch');
            Route::delete('/reservas/{reserva}', [ReservaApiController::class, 'destroy'])->name('reservas.destroy');

            Route::apiResource('mascotas', AdminMascotaApiController::class);

            Route::get('/servicios', [ServicioApiController::class, 'index'])->name('servicios.index');
            Route::get('/servicios/{servicio}', [ServicioApiController::class, 'show'])->name('servicios.show');
            Route::post('/servicios', [ServicioApiController::class, 'store'])->name('servicios.store');
            Route::put('/servicios/{servicio}', [ServicioApiController::class, 'update'])->name('servicios.update');
            Route::patch('/servicios/{servicio}', [ServicioApiController::class, 'update'])->name('servicios.patch');
            Route::post('/servicios/{servicio}/imagen', [ServicioApiController::class, 'uploadImage'])->name('servicios.imagen');
            Route::delete('/servicios/{servicio}', [ServicioApiController::class, 'destroy'])->name('servicios.destroy');

            Route::post('/razas', [RazaApiController::class, 'store'])->name('razas.store');
            Route::delete('/razas/{raza}', [RazaApiController::class, 'destroy'])->name('razas.destroy');

            Route::get('/configuracion', [ConfiguracionApiController::class, 'index'])->name('configuracion.index');
        });
    });
});
