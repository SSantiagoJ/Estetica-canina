# Ordenamiento profesional del proyecto Laravel

## Estado actual

El proyecto ya esta mucho mas alineado a una estructura Laravel:

- Controladores web en `app/Http/Controllers`.
- Controladores API en `app/Http/Controllers/Api`.
- Recursos API en `app/Http/Resources`.
- Vistas Blade agrupadas por dominio en `resources/views`.
- Plantillas parciales en `resources/views/partials`.
- Plantillas de correo en `resources/views/emails`.
- Base SQL integrada en `database/sql`.
- Documentacion tecnica en `docs`.

## Orden aplicado en esta revision

- `app/Services/JwtService.php` se movio a `app/Services/Auth/JwtService.php`.
- `app/Services/ReservationAvailabilityService.php` se movio a `app/Services/Reservas/ReservationAvailabilityService.php`.
- `configurar_feedbacks.sql` se movio a `database/sql/feedbacks_demo.sql`.
- `app/Models/Notificacion.php` ahora relaciona `id_usuario` con `App\Models\Usuario`, que es la tabla real del sistema.
- `database/seeders/DatabaseSeeder.php` ya no crea un usuario en la tabla default `users`, porque el sistema usa `usuarios`.

## Archivos que NO eliminaria todavia

Estos archivos parecen viejos o pendientes, pero no deben borrarse sin revisar flujo por flujo:

- `resources/views/legacy/*`
  - Son vistas antiguas no usadas por rutas actuales.
  - Recomendacion: conservar una version mas hasta confirmar que no se necesitan para sustentar el proyecto.

- `app/Http/Controllers/TurnoController.php`
  - No aparece conectado en rutas actuales.
  - Referencia la vista `gestionar_turno`, que no existe.
  - Recomendacion: eliminarlo solo despues de confirmar que todo el flujo de turnos usa `EmpleadoController` y `TurnoApiController`.

- `app/Http/Controllers/TratamientosController.php`
  - No aparece conectado en rutas actuales.
  - Usa `resources/views/cliente/mis-tratamientos.blade.php`.
  - Recomendacion: decidir si se reactivara como historial de tratamientos o se elimina junto con esa vista.

- `app/Models/User.php` y `database/factories/UserFactory.php`
  - Son archivos default de Laravel.
  - Ya no deberian usarse para el negocio, porque el sistema usa `App\Models\Usuario`.
  - Recomendacion: eliminarlos solo si tambien se ajusta la migracion default `0001_01_01_000000_create_users_table.php` para no depender de la tabla `users`.

## Archivos candidatos a eliminar

Estos son los candidatos mas claros para eliminar, con bajo riesgo:

- `.env copy.example`
  - Es una copia vieja del `.env.example` default de Laravel.
  - Ya existe `.env.example` correcto para Pet Grooming.

- `resources/views/legacy/catalogo_dinamico.blade.php`
- `resources/views/legacy/catalogo-estatico.blade.php`
- `resources/views/legacy/dashboard.blade.php`
- `resources/views/legacy/servicio.blade.php`
- `resources/views/legacy/welcome.blade.php`
  - No estan referenciados por rutas actuales.
  - Conviene eliminarlos cuando el usuario confirme que no necesita conservar pantallas antiguas.

## Vistas faltantes detectadas

Estas referencias existen en controladores, pero las vistas no aparecen en `resources/views`:

- `reservas.pago_resumen`
- `reservas.detalle`
- `reservas.editar`
- `empleado.ver-reserva`
- `gestionar_turno`

Recomendacion:

- Crear o recuperar `reservas.detalle` y `reservas.editar` si el flujo de "Mis Reservas" debe permitir ver/editar.
- Revisar si `reservas.pago_resumen` aun se usa o si fue reemplazada por `reservas.pago` y `reservas.completada`.
- Eliminar `TurnoController` si `gestionar_turno` ya fue reemplazado por `empleado.gestionar-turnos`.

## Refactor recomendado por etapas

Para que el proyecto quede mas profesional sin romperlo:

1. Mantener controladores web actuales, pero extraer reglas de negocio a servicios.
2. Crear servicios por dominio:
   - `app/Services/Auth`
   - `app/Services/Reservas`
   - `app/Services/Pagos`
   - `app/Services/Notificaciones`
   - `app/Services/Media`
3. Evitar que controladores grandes como `ReservaController` y `EmpleadoController` sigan creciendo.
4. Crear pruebas Feature para los flujos principales antes de borrar legacy.
5. Eliminar archivos legacy en una rama separada cuando las pruebas esten verdes.

## Conclusion

El proyecto ya esta ordenado a nivel de carpetas principales. Lo que falta no es mover todo de golpe, sino limpiar piezas antiguas y extraer logica desde controladores grandes hacia servicios por dominio. La eliminacion debe hacerse por pasos para no romper pantallas que todavia puedan estar conectadas por rutas o botones.
