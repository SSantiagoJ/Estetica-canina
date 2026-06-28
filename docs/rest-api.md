# API REST Pet Grooming

La API REST vive en `routes/api.php` bajo el prefijo `/api/v1`. Se agrego como una capa separada de las pantallas Blade, por lo que el flujo web actual sigue funcionando desde `routes/web.php`.

La web Blade mantiene autenticacion por sesion Laravel. La API protegida usa JWT con el header:

```http
Authorization: Bearer {access_token}
```

## Convencion REST

| Operacion | Metodo HTTP | Ejemplo |
| --- | --- | --- |
| Consultar | GET | `GET /api/v1/servicios` |
| Registrar | POST | `POST /api/v1/reservas` |
| Actualizar | PUT/PATCH | `PUT /api/v1/clientes/mascotas/{mascota}` |
| Eliminar | DELETE | `DELETE /api/v1/empleado/turnos/{turno}` |

Las rutas usan nombres plurales, versionado (`/api/v1`) y agrupacion por contexto (`auth`, `clientes`, `empleado`, `supervisor`, `admin`).

## Codigos de respuesta HTTP

Las respuestas JSON de la API usan el campo `status_code` para que Postman pueda validar el estado esperado junto con el codigo HTTP real.

| Codigo | Nombre | Uso en la API |
| --- | --- | --- |
| 200 | OK | Consultas, actualizaciones, eliminaciones logicas y operaciones completadas correctamente. |
| 201 | Created | Creacion de recursos: servicios, usuarios, mascotas, reservas, turnos, novedades y pagos. |
| 400 | Bad Request | Datos invalidos, validaciones fallidas o solicitudes que no pueden procesarse. |
| 401 | Unauthorized | Credenciales incorrectas, token faltante o token JWT invalido. |
| 403 | Forbidden | Token valido, pero el rol no tiene permiso sobre el recurso. |
| 404 | Not Found | Ruta no existente o recurso no encontrado por binding/modelo. |
| 500 | Internal Server Error | Error interno no controlado del servidor. |

Para Postman se agregaron estos archivos:

- `docs/postman/Pet_Grooming_API_REST.postman_collection.json`
- `docs/postman/Pet_Grooming_API_REST.postman_environment.json`
- `docs/postman/README.md`

## Autenticacion JWT

El flujo recomendado es:

1. `GET /api/v1/auth/csrf-token` para obtener token CSRF de login/MFA.
2. `POST /api/v1/auth/login` o `POST /api/v1/auth/intranet-login`.
3. Si responde `mfa_required: true`, enviar el codigo a `POST /api/v1/auth/mfa`.
4. Cuando el login o MFA termina correctamente, la API devuelve:

```json
{
  "access_token": "eyJ...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "expires_at": "2026-06-27T23:59:00+00:00"
}
```

Ese token se usa en las rutas protegidas. El logout API revoca el token si existe la tabla `jwt_revoked_tokens`.

## Proteccion de endpoints

Las rutas de negocio privadas usan el middleware `jwt.auth`. Si no se envia un token Bearer, la API responde:

```json
{
  "success": false,
  "status_code": 401,
  "message": "Debes enviar un token Bearer para acceder a este recurso."
}
```

Si el token es invalido, expirado o revocado, tambien responde `401 Unauthorized`.

Las rutas con roles usan el middleware `role`. Por ejemplo, `/api/v1/clientes/perfil` requiere rol `Cliente`. Si se consume con token valido de `Admin`, `Empleado` o `Supervisor`, la API responde:

```json
{
  "success": false,
  "status_code": 403,
  "message": "No tienes permisos para acceder a esta seccion."
}
```

Cada intento sin token, con token invalido o con permisos insuficientes registra una notificacion `Seguridad` para los usuarios `Admin`. El admin puede revisar esas observaciones en `Mi Perfil`, dentro de `Alertas de accesos no autorizados`.

## Auth API

Usa MFA por correo. Login y MFA mantienen una sesion temporal de Laravel para completar el reto MFA; las rutas protegidas usan JWT.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/auth/csrf-token` | Genera token CSRF para pruebas con sesion Laravel en Postman. |
| POST | `/api/v1/auth/login` | Login de cliente. Tambien acepta `tipo_acceso=intranet`. |
| POST | `/api/v1/auth/intranet-login` | Login de trabajador, supervisor o admin. |
| POST | `/api/v1/auth/mfa` | Verifica el codigo MFA. |
| POST | `/api/v1/auth/logout` | Cierra sesion. |
| GET | `/api/v1/auth/me` | Usuario autenticado actual. |

## Clientes API

Requiere rol `Cliente`.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/clientes/perfil` | Perfil del cliente. |
| PUT/PATCH | `/api/v1/clientes/perfil` | Actualiza perfil del cliente. |
| GET | `/api/v1/clientes/mascotas` | Lista mascotas del cliente. |
| POST | `/api/v1/clientes/mascotas` | Crea mascota. |
| GET | `/api/v1/clientes/mascotas/{mascota}` | Detalle de mascota propia. |
| PUT/PATCH | `/api/v1/clientes/mascotas/{mascota}` | Actualiza mascota propia. |
| DELETE | `/api/v1/clientes/mascotas/{mascota}` | Elimina mascota propia. |
| GET | `/api/v1/clientes/reservas` | Lista reservas del cliente. |
| POST | `/api/v1/clientes/reservas` | Crea reserva del cliente. |
| GET | `/api/v1/clientes/reservas/{reserva}` | Detalle de reserva propia. |
| PUT/PATCH | `/api/v1/clientes/reservas/{reserva}` | Edita/reprograma reserva propia. |
| DELETE | `/api/v1/clientes/reservas/{reserva}` | Cancela reserva propia. |

## Reservas API

Requiere JWT. Clientes solo ven sus reservas; empleados solo las asignadas; supervisor/admin ven general.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/reservas` | Lista reservas segun rol. |
| POST | `/api/v1/reservas` | Crea reserva. |
| GET | `/api/v1/reservas/{reserva}` | Detalle de reserva. |
| PUT/PATCH | `/api/v1/reservas/{reserva}` | Actualiza/reprograma reserva. |
| DELETE | `/api/v1/reservas/{reserva}` | Cancela reserva. |
| POST | `/api/v1/reservas/horarios-disponibles` | Consulta horarios disponibles. |
| GET | `/api/v1/reservas/{reserva}/pagos` | Lista pagos de la reserva. |
| POST | `/api/v1/reservas/{reserva}/pagos` | Registra pago de reserva. |
| POST | `/api/v1/reservas/{reserva}/pago` | Alias para registrar pago. |
| GET | `/api/v1/reservas/{reserva}/boleta` | Muestra boleta PDF del ultimo pago. |
| GET | `/api/v1/reservas/{reserva}/boleta/descargar` | Descarga boleta PDF del ultimo pago. |
| GET | `/api/v1/pagos/{pago}/boleta` | Muestra boleta PDF por pago. |
| GET | `/api/v1/pagos/{pago}/boleta/descargar` | Descarga boleta PDF por pago. |

## Servicios API

Consulta publica. Escritura solo `Admin`.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/servicios` | Lista servicios. |
| GET | `/api/v1/servicios/{servicio}` | Detalle de servicio. |
| GET | `/api/v1/admin/servicios` | Lista servicios para admin. |
| POST | `/api/v1/admin/servicios` | Crea servicio. |
| GET | `/api/v1/admin/servicios/{servicio}` | Detalle de servicio para admin. |
| PUT/PATCH | `/api/v1/admin/servicios/{servicio}` | Actualiza servicio. |
| POST | `/api/v1/admin/servicios/{servicio}/imagen` | Sube/reemplaza imagen de servicio. |
| DELETE | `/api/v1/admin/servicios/{servicio}` | Elimina servicio. |

## Mascotas/Razas API

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/razas` | Lista razas con foto. Se puede filtrar con `?especie=Perro`. |
| GET | `/api/v1/razas/{raza}` | Detalle de una raza/foto. |
| POST | `/api/v1/admin/razas` | Sube foto de raza. |
| DELETE | `/api/v1/admin/razas/{raza}` | Elimina foto de raza. |

Las fotos se devuelven como `imagen_url` en el recurso de raza.

## Empleado API

Requiere rol `Admin`, `Empleado` o `Supervisor`.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/empleado/panel-del-dia` | Panel diario con resumen y reservas. |
| GET | `/api/v1/empleado/reservas` | Bandeja de reservas. |
| PUT/PATCH | `/api/v1/empleado/reservas/{reserva}/atender` | Marca reserva como atendida. |
| GET | `/api/v1/empleado/turnos` | Lista turnos. |
| POST | `/api/v1/empleado/turnos` | Crea turno. |
| GET | `/api/v1/empleado/turnos/{turno}` | Detalle de turno. |
| PUT/PATCH | `/api/v1/empleado/turnos/{turno}` | Actualiza turno. |
| DELETE | `/api/v1/empleado/turnos/{turno}` | Elimina turno. |
| GET | `/api/v1/empleado/novedades` | Lista novedades. |
| POST | `/api/v1/empleado/novedades` | Crea novedad. |
| PUT/PATCH | `/api/v1/empleado/novedades/{novedad}` | Actualiza novedad. |
| DELETE | `/api/v1/empleado/novedades/{novedad}` | Elimina novedad. |
| GET | `/api/v1/empleado/ingresos` | Ingresos segun rol. Empleado ve su mes. |

## Supervisor API

Requiere rol `Supervisor` o `Admin`.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/supervisor/ingresos` | Consulta ingresos del periodo. |
| GET | `/api/v1/supervisor/metricas` | Metricas de pagos, empleados, dias y servicios. |
| GET | `/api/v1/supervisor/reportes/ingresos-excel` | Descarga reporte Excel de ingresos. |

## Admin API

Requiere rol `Admin`.

| Metodo | Ruta | Uso |
| --- | --- | --- |
| GET | `/api/v1/admin/usuarios` | Lista usuarios. |
| POST | `/api/v1/admin/usuarios` | Crea usuario. |
| GET | `/api/v1/admin/usuarios/{usuario}` | Detalle de usuario. |
| PUT/PATCH | `/api/v1/admin/usuarios/{usuario}` | Actualiza usuario. |
| DELETE | `/api/v1/admin/usuarios/{usuario}` | Desactiva usuario. |
| GET | `/api/v1/admin/reservas` | Lista reservas. |
| GET | `/api/v1/admin/reservas/{reserva}` | Detalle de reserva. |
| PUT/PATCH | `/api/v1/admin/reservas/{reserva}` | Actualiza reserva. |
| DELETE | `/api/v1/admin/reservas/{reserva}` | Cancela reserva. |
| GET | `/api/v1/admin/mascotas` | Lista mascotas. |
| POST | `/api/v1/admin/mascotas` | Registra mascota. |
| GET | `/api/v1/admin/mascotas/{mascota}` | Detalle de mascota. |
| PUT/PATCH | `/api/v1/admin/mascotas/{mascota}` | Actualiza mascota. |
| DELETE | `/api/v1/admin/mascotas/{mascota}` | Elimina mascota. |
| GET | `/api/v1/admin/configuracion` | Configuracion general de la API. |

## Ejemplo de horarios

```http
POST /api/v1/reservas/horarios-disponibles
Content-Type: application/json

{
  "fecha": "2026-06-28",
  "id_empleado": 1,
  "servicios": [1, 2]
}
```

```json
{
  "success": true,
  "message": "Horarios disponibles obtenidos correctamente.",
  "data": {
    "fecha": "2026-06-28",
    "id_empleado": 1,
    "duracion_minutos": 150,
    "horarios": [
      {
        "hora": "08:00",
        "hora_fin": "10:30",
        "disponible": true
      }
    ]
  }
}
```
