# Auditoria de estructura Laravel

## Resumen

El proyecto funciona como una aplicacion Laravel tradicional, pero gran parte de la logica de negocio esta concentrada en controladores. No existe todavia una capa formal `app/Services`.

## Servicios de tarea

Estos componentes coordinan flujos completos del sistema:

- `AuthController`: login de cliente, login de intranet, registro, MFA, bypass MFA y logout.
- `ReservaController`: seleccion de mascota, seleccion de servicios, horarios, pago, creacion de reserva, delivery, boletas y mis reservas.
- `EmpleadoController`: panel del dia, bandeja de reservas, atenciones, turnos, novedades y dashboard.
- `GestorController`: administracion de dashboard, usuarios, mascotas, razas, reservas y servicios.
- `ServicioController`: CRUD de servicios e imagenes de servicios.
- `PerfilController`: perfil de cliente, mascotas y perfil intranet.
- `NotificacionController`: configuracion, prueba, ejecucion y correos personalizados.
- `CalificacionController`: feedback y opiniones destacadas.
- `CatalogoController`: catalogo publico de servicios.

## Servicios de entidad

Modelos principales en `app/Models`:

- `Usuario`, `Persona`, `Cliente`, `Empleado`
- `Mascota`, `RazaImagen`
- `Servicio`
- `Reserva`, `DetalleReserva`, `Pago`
- `Turno`, `Atencion`
- `Novedad`, `Notificacion`, `Feedback`

## Utilidades y transversales

- `EnsureUserHasRole`: middleware de permisos por rol.
- `AuthController`: helpers MFA (`startMfaChallenge`, `maskEmail`, validacion de columnas MFA).
- `ReservaController`: helpers de horarios, disponibilidad, formateo, boletas y autorizacion de pago.
- `Servicio::imagen_url`: URL estable para imagenes de servicios.
- `Mascota::foto` y `RazaImagen::fotoPara`: resolucion de fotos por raza.
- `Console\Kernel`: agenda comandos de notificaciones segun configuracion de BD.
- `Notifications`: plantillas de correo para reserva, vacuna y promociones.

## Orden aplicado

- La nota `app/Http/Controllers/Como encender` se movio a `docs/como-encender-laravel.md`.
- La nota `public/images/leer` se movio a `docs/public-assets.md`.
- La prueba de PayPal se movio a `resources/views/dev/prueba-paypal.blade.php`.
- Vistas no enlazadas directamente se movieron a `resources/views/legacy`.
- Boletas generadas se consolidaron localmente en `storage/app/boletas`.
- La ruta `/pruebaPaypal` ahora apunta a `view('dev.prueba-paypal')`.
- La ruta de prueba PDF usa `storage/app/boletas`, igual que el flujo real de boletas.

## Piezas que conviene revisar antes de borrar

- `TurnoController`: no aparece conectado a rutas actuales y referencia `gestionar_turno`, vista que no existe.
- `TratamientosController`: no aparece conectado a rutas actuales, aunque existe `resources/views/cliente/mis-tratamientos.blade.php`.
- `resources/views/legacy/*`: vistas historicas no usadas por rutas actuales.
- `resources/views/dashboard.blade.php` y `resources/views/welcome.blade.php`: parecen vistas base/antiguas.
- `public/storage`: actualmente es carpeta normal con `.gitignore`; en Laravel normalmente deberia ser enlace simbolico a `storage/app/public`.

## Recomendacion de refactor posterior

Para ordenar la logica sin cambiar comportamiento, conviene crear servicios de aplicacion:

- `app/Services/Auth/MfaService.php`
- `app/Services/Reservas/ReservaService.php`
- `app/Services/Reservas/HorarioService.php`
- `app/Services/Pagos/BoletaService.php`
- `app/Services/Notificaciones/NotificacionService.php`
- `app/Services/Media/ImagenService.php`

Ese refactor debe hacerse por etapas y con pruebas de rutas, porque hoy los controladores mezclan validacion, consultas, reglas de negocio y respuestas HTTP.

