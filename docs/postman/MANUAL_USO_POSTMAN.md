# Manual de uso de Postman - Pet Grooming

Este manual explica como usar la coleccion de Postman del proyecto Pet Grooming, para que puedas probar la API REST, validar codigos HTTP, autenticarte con JWT y consumir servicios protegidos.

## 1. Que es Postman

Postman es una herramienta para probar APIs. En este proyecto sirve para:

- Enviar peticiones HTTP al sistema Laravel.
- Probar endpoints REST como `GET`, `POST`, `PATCH` y `DELETE`.
- Validar respuestas JSON.
- Probar autenticacion con JWT.
- Confirmar codigos HTTP como `200`, `201`, `400`, `401`, `403` y `404`.
- Simular el consumo de servicios desde otro sistema.

## 2. Archivos que debes importar

Los archivos estan en:

```text
docs/postman/
```

Debes importar estos dos:

```text
Pet_Grooming_API_REST.postman_collection.json
Pet_Grooming_API_REST.postman_environment.json
```

La `collection` contiene las rutas y pruebas.

El `environment` contiene variables como:

- `base_url`
- `csrf_token`
- `admin_correo`
- `admin_password`
- `jwt_token`
- `mfa_code`
- `created_service_id`
- `empleado_id`

## 3. Como importar en Postman

1. Abre Postman.
2. Haz clic en `Import`.
3. Selecciona `Files`.
4. Busca los dos archivos del proyecto:

```text
C:\Users\LENOVO\Documents\proyecto veterinaria\esteticacaninav2\docs\postman\Pet_Grooming_API_REST.postman_collection.json
C:\Users\LENOVO\Documents\proyecto veterinaria\esteticacaninav2\docs\postman\Pet_Grooming_API_REST.postman_environment.json
```

5. Haz clic en `Import`.
6. Arriba a la derecha, selecciona el environment:

```text
Pet Grooming Local
```

Si arriba a la derecha dice `No environment`, las pruebas no funcionaran porque Postman no encontrara variables como `csrf_token` o `jwt_token`.

## 4. Antes de ejecutar Postman

El sistema Laravel debe estar corriendo:

```bash
php artisan serve
```

La URL esperada es:

```text
http://127.0.0.1:8000
```

Esa URL esta guardada en la variable:

```text
base_url
```

## 5. Orden correcto de ejecucion

Ejecuta en este orden:

1. `01 Preparacion / Obtener CSRF token`
2. `03 Codigos HTTP autenticados / 200 OK - Login admin`
3. Si pide MFA:
   - revisa el correo del admin,
   - copia el codigo,
   - pegalo en la variable `mfa_code`,
   - ejecuta `200 OK - Validar MFA admin`.
4. Ejecuta `201 Created - Crear servicio admin`.
5. Ejecuta `200 OK - Actualizar servicio admin`.
6. Ejecuta `200 OK - Eliminar servicio admin`.
7. Ejecuta `04 Proteccion de endpoints`.
8. Ejecuta `05 Consumo JSON de servicios REST`.

## 6. Para que sirve cada carpeta

### 01 Preparacion

Contiene:

```text
Obtener CSRF token
```

Sirve para pedirle a Laravel un token CSRF.

Ese token se guarda automaticamente en:

```text
csrf_token
```

Se usa para login y MFA, porque esas rutas usan sesion temporal de Laravel.

### 02 Codigos HTTP publicos

Sirve para probar rutas publicas y respuestas basicas.

Incluye:

- `200 OK - Health`: confirma que la API esta activa.
- `400 Bad Request - Validacion horarios`: valida error por datos incompletos.
- `401 Unauthorized - Auth me sin sesion`: valida acceso sin autenticacion.
- `401 Unauthorized - Credenciales incorrectas`: valida login incorrecto.
- `404 Not Found - Servicio inexistente`: valida recurso inexistente.

### 03 Codigos HTTP autenticados

Sirve para probar rutas protegidas de admin.

Incluye:

- `200 OK - Login admin`: inicia sesion con el admin.
- `200 OK - Validar MFA admin`: valida el codigo MFA si el sistema lo pide.
- `201 Created - Crear servicio admin`: crea un servicio usando JWT.
- `200 OK - Actualizar servicio admin`: actualiza el servicio creado.
- `200 OK - Eliminar servicio admin`: elimina el servicio creado.

Estas rutas necesitan:

```http
Authorization: Bearer {{jwt_token}}
```

### 04 Proteccion de endpoints

Sirve para demostrar seguridad.

Incluye:

- Sin token: debe responder `401 Unauthorized`.
- Token invalido: debe responder `401 Unauthorized`.
- Token valido pero sin permisos: debe responder `403 Forbidden`.

Ejemplo:

```text
GET /api/v1/clientes/perfil
```

Esa ruta requiere rol `Cliente`. Si entras con token de admin, debe devolver `403 Forbidden`.

### 05 Consumo JSON de servicios REST

Sirve para demostrar que los servicios pueden ser consumidos.

Incluye:

- `GET Servicios publicos`
- `GET Razas publicas`
- `POST Horarios disponibles JSON`

Estas pruebas validan que la API devuelve JSON correcto.

## 7. Variables importantes

### base_url

URL base del proyecto:

```text
http://127.0.0.1:8000
```

### csrf_token

Token usado para login y MFA.

Se obtiene ejecutando:

```text
01 Preparacion / Obtener CSRF token
```

### admin_correo

Correo del usuario admin:

```text
admin@spa.com
```

### admin_password

Password del admin:

```text
123456
```

### jwt_token

Token JWT que permite consumir rutas protegidas.

Se llena despues del login o despues de validar MFA.

Si esta vacio, las rutas protegidas responderan:

```json
{
  "success": false,
  "status_code": 401,
  "message": "Debes enviar un token Bearer para acceder a este recurso."
}
```

### mfa_code

Codigo MFA recibido por correo.

Solo se usa cuando el login responde que MFA es requerido.

### created_service_id

ID del servicio creado por Postman.

Se llena automaticamente cuando ejecutas:

```text
201 Created - Crear servicio admin
```

Luego se usa para actualizar y eliminar ese mismo servicio.

### empleado_id

ID del empleado usado para consultar horarios disponibles.

Si la prueba de horarios falla, revisa que exista ese empleado en la base de datos.

## 8. Codigos HTTP que valida la coleccion

| Codigo | Significado | Uso en el proyecto |
| --- | --- | --- |
| 200 OK | Consulta u operacion correcta | Health, login, update, delete |
| 201 Created | Recurso creado | Crear servicio |
| 400 Bad Request | Datos incorrectos | Validacion fallida |
| 401 Unauthorized | Falta autenticacion | Sin token o token invalido |
| 403 Forbidden | Sin permisos | Token valido, rol incorrecto |
| 404 Not Found | No encontrado | Recurso inexistente |

## 9. Errores comunes

### Error: No environment

Solucion:

Selecciona arriba a la derecha:

```text
Pet Grooming Local
```

### Error: Falta csrf_token

Solucion:

Ejecuta primero:

```text
01 Preparacion / Obtener CSRF token
```

### Error: Falta jwt_token

Solucion:

Ejecuta:

```text
200 OK - Login admin
```

Si pide MFA, ejecuta tambien:

```text
200 OK - Validar MFA admin
```

### Error: Falta mfa_code

Solucion:

1. Revisa el correo del admin.
2. Copia el codigo de 6 digitos.
3. Pegalo en la variable `mfa_code`.
4. Ejecuta `Validar MFA admin`.

### Error en horarios disponibles

Puede pasar si `empleado_id` no existe en la base.

Solucion:

Cambia `empleado_id` en el environment por un empleado real.

## 10. Como saber que todo salio bien

Todo va bien si ves respuestas como:

```json
{
  "success": true,
  "status_code": 200,
  "message": "Operacion realizada correctamente."
}
```

Para crear servicio, debe responder:

```json
{
  "success": true,
  "status_code": 201
}
```

Para seguridad, deben aparecer:

```json
{
  "success": false,
  "status_code": 401
}
```

o:

```json
{
  "success": false,
  "status_code": 403
}
```

## 11. Recomendacion final

Cada vez que vuelvas a importar o ejecutar la coleccion desde cero:

1. Selecciona `Pet Grooming Local`.
2. Ejecuta `Obtener CSRF token`.
3. Ejecuta `Login admin`.
4. Si pide MFA, valida MFA.
5. Ejecuta las pruebas protegidas.

Ese orden evita la mayoria de errores en Postman.
