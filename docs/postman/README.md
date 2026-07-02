# Validaciones Postman - API REST Pet Grooming

## Archivos

- `Pet_Grooming_API_REST.postman_collection.json`: coleccion con requests y pruebas `pm.test`.
- `Pet_Grooming_API_REST.postman_environment.json`: ambiente local con `base_url`, credenciales admin y variables temporales.

## Orden recomendado

1. Importa los dos archivos en Postman.
2. Selecciona el ambiente `Pet Grooming Local`.
3. Ejecuta primero `01 Preparacion / Obtener CSRF token`.
4. Ejecuta las pruebas publicas:
   - `200 OK - Health`
   - `400 Bad Request - Validacion horarios`
   - `401 Unauthorized - Auth me sin sesion`
   - `401 Unauthorized - Credenciales incorrectas`
   - `404 Not Found - Servicio inexistente`
5. Para validar `201 Created`, ejecuta:
   - `200 OK - Login admin`
   - Si el login indica MFA pendiente, la ejecucion se detiene para evitar llamadas sin token. Copia el codigo recibido por correo en la variable `mfa_code` del environment y ejecuta `200 OK - Validar MFA admin`.
   - `201 Created - Crear servicio admin`
   - `200 OK - Actualizar servicio admin`
   - `200 OK - Eliminar servicio admin`
6. Ejecuta `04 Proteccion de endpoints` para comprobar:
   - `401 Unauthorized` sin token Bearer.
   - `401 Unauthorized` con token invalido.
   - `403 Forbidden` con token valido pero sin rol suficiente.
7. Ejecuta `05 Consumo JSON de servicios REST` para demostrar intercambio de mensajes JSON y consumo de servicios publicos.

## Codigos cubiertos

| Codigo | Uso esperado | Ejemplo |
| --- | --- | --- |
| 200 OK | Consulta, actualizacion o eliminacion exitosa | `GET /api/v1/health` |
| 201 Created | Creacion exitosa de un recurso | `POST /api/v1/admin/servicios` |
| 400 Bad Request | Datos invalidos o solicitud no procesable | `POST /api/v1/reservas/horarios-disponibles` sin body valido |
| 401 Unauthorized | Usuario no autenticado o credenciales incorrectas | `GET /api/v1/auth/me` sin sesion |
| 403 Forbidden | Token valido sin permisos para el recurso | `GET /api/v1/clientes/perfil` con token admin |
| 404 Not Found | Ruta o recurso inexistente | `GET /api/v1/servicios/999999999` |
| 500 Internal Server Error | Excepcion interna no controlada | No se fuerza en la coleccion para no romper datos reales |

## JWT y MFA

Cuando el login termina correctamente, la API devuelve:

```json
{
  "access_token": "TOKEN",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

La coleccion guarda automaticamente `access_token` en la variable `jwt_token`. Las rutas protegidas usan:

```http
Authorization: Bearer {{jwt_token}}
```

Si el login admin devuelve `mfa_required: true`, la coleccion deja `jwt_token` vacio y marca `mfa_pending=true`. En ese caso:

1. Revisa el correo del usuario admin.
2. Copia el codigo de 6 digitos.
3. Pegalo en el environment como `mfa_code`.
4. Ejecuta `03 Codigos HTTP autenticados / 200 OK - Validar MFA admin`.
5. Ese request guarda automaticamente `access_token` en `jwt_token`.
6. Ejecuta nuevamente desde `201 Created - Crear servicio admin`.

Si `jwt_token` esta vacio, cualquier endpoint protegido como `POST /api/v1/admin/servicios` devolvera:

```json
{
  "success": false,
  "status_code": 401,
  "message": "Debes enviar un token Bearer para acceder a este recurso."
}
```

## Headers importantes

Usa siempre:

```http
Accept: application/json
```

Para login y MFA, que usan sesion temporal de Laravel:

```http
X-CSRF-TOKEN: {{csrf_token}}
```

Para endpoints protegidos de negocio, usa JWT:

```http
Authorization: Bearer {{jwt_token}}
```

## Observacion para admin

Cuando alguien intenta consumir una ruta API protegida sin token, con token invalido o con un rol incorrecto, el sistema registra una notificacion de tipo `Seguridad` para los usuarios `Admin`. El admin puede verla en `Mi Perfil`, en el bloque `Alertas de accesos no autorizados`.
