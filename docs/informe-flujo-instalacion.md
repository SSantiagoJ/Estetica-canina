# Informe de flujo, seguridad e instalacion - Pet Grooming

## 1. Vista general del proyecto

El sistema es una aplicacion Laravel con dos superficies principales:

- Web Blade: pantallas para clientes, intranet, empleado, supervisor y admin. Usa autenticacion por sesion Laravel.
- API REST: endpoints bajo `/api/v1`. Usa JWT para las rutas protegidas y respuestas JSON consistentes.

La base de datos principal es MySQL y el archivo de respaldo para levantar el proyecto esta en `database/sql/spa_mascotas_base.sql`.

## 2. Flujo de autenticacion

### Cliente web

1. El cliente entra desde el header publico.
2. Puede registrarse o iniciar sesion desde el modal.
3. Si no tiene MFA, el sistema le permite crearlo mediante codigo enviado por correo.
4. Cuando inicia sesion, accede a perfil, mascotas, reservas, pagos y boletas.

### Intranet

1. Trabajador, supervisor y admin entran por el boton `Intranet`.
2. El login de intranet separa a los roles internos del login de clientes.
3. Cada rol mantiene su sesion y tiene boton `Mi Perfil` dentro del header interno.
4. Admin puede crear usuarios y administrar servicios, mascotas, reservas y configuracion.

### API REST con JWT

1. `GET /api/v1/auth/csrf-token` genera CSRF para login/MFA desde Postman.
2. `POST /api/v1/auth/login` autentica clientes.
3. `POST /api/v1/auth/intranet-login` autentica roles internos.
4. Si MFA aplica, `POST /api/v1/auth/mfa` completa el reto.
5. La API devuelve:

```json
{
  "access_token": "TOKEN",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

6. Las rutas protegidas consumen el token con:

```http
Authorization: Bearer {access_token}
```

## 3. Proteccion de endpoints

Las rutas privadas usan el middleware `jwt.auth`.

- Sin token: `401 Unauthorized`
- Token invalido, expirado o revocado: `401 Unauthorized`
- Token valido sin permisos de rol: `403 Forbidden`

Ejemplo protegido:

```http
GET /api/v1/clientes/perfil
Authorization: Bearer {token_cliente}
```

Si un usuario sin credenciales o sin permiso intenta acceder, se registra una notificacion `Seguridad` para los usuarios `Admin`. El admin la ve en `Mi Perfil`, en el bloque `Alertas de accesos no autorizados`.

## 4. Roles y alcances

- Cliente: perfil, mascotas propias, reservas propias, pagos y boletas.
- Empleado: panel del dia, bandeja de reservas asignadas, turnos, novedades e ingresos mensuales propios.
- Supervisor: ingresos generales, metricas y descarga de reportes Excel.
- Admin: usuarios, servicios, reservas, mascotas, razas, configuracion y alertas de seguridad.

## 5. Servicios API REST implementados

Todos los endpoints REST estan versionados con `/api/v1`.

- Auth API: login, MFA, logout, usuario actual.
- Clientes API: perfil, mascotas y reservas del cliente.
- Reservas API: listar, crear, editar, cancelar, horarios disponibles, pago y boleta.
- Servicios API: listar, crear, editar, eliminar y subir imagen.
- Mascotas/Razas API: razas por especie y fotos de razas.
- Empleado API: bandeja, atender reserva, turnos y panel del dia.
- Supervisor API: ingresos, reportes y metricas.
- Admin API: usuarios, servicios, reservas, mascotas y configuracion.

## 6. Codigos HTTP esperados

| Codigo | Uso |
| --- | --- |
| 200 OK | Consultas, actualizaciones y operaciones correctas. |
| 201 Created | Creacion de recursos. |
| 400 Bad Request | Datos invalidos o solicitud no procesable. |
| 401 Unauthorized | Falta token, token invalido o credenciales incorrectas. |
| 403 Forbidden | Token valido sin permisos. |
| 404 Not Found | Ruta o recurso inexistente. |
| 500 Internal Server Error | Error interno no controlado. |

## 7. Validacion con Postman

Archivos:

- `docs/postman/Pet_Grooming_API_REST.postman_collection.json`
- `docs/postman/Pet_Grooming_API_REST.postman_environment.json`
- `docs/postman/README.md`

Orden recomendado:

1. Importar coleccion y environment.
2. Seleccionar `Pet Grooming Local`.
3. Ejecutar `01 Preparacion / Obtener CSRF token`.
4. Ejecutar codigos publicos.
5. Ejecutar login admin y CRUD de servicios.
6. Ejecutar `04 Proteccion de endpoints`.
7. Ejecutar `05 Consumo JSON de servicios REST`.

La coleccion valida que las respuestas sean JSON, que incluyan `success`, `status_code`, `message` y que los servicios puedan consumirse desde Postman.

## 8. Instalacion para otro colaborador

### Requisitos

- PHP compatible con el proyecto Laravel.
- Composer.
- Node.js y npm.
- MySQL o MariaDB.
- Extensiones PHP comunes de Laravel: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `ctype`, `json`, `tokenizer`, `xml`.

### Clonar e instalar

```bash
git clone https://github.com/SSantiagoJ/Estetica-canina.git
cd Estetica-canina
composer install
npm install
```

En Windows, crear el `.env`:

```powershell
Copy-Item .env.example .env
```

Generar clave:

```bash
php artisan key:generate
```

Configurar `.env`:

```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spa_mascotas
DB_USERNAME=root
DB_PASSWORD=
```

Si se usara correo real para MFA y notificaciones, configurar tambien `MAIL_*` con una clave de aplicacion, no con la clave normal del correo.

### Base de datos

Crear la base:

```sql
CREATE DATABASE spa_mascotas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importar el respaldo principal:

```bash
mysql -u root -p spa_mascotas < database/sql/spa_mascotas_base.sql
```

Luego ejecutar migraciones nuevas del proyecto:

```bash
php artisan migrate
```

Crear enlace de storage para imagenes subidas:

```bash
php artisan storage:link
```

### Ejecutar localmente

```bash
php artisan serve
```

Si se trabaja con assets:

```bash
npm run dev
```

Abrir:

```text
http://127.0.0.1:8000
```

### Validaciones tecnicas

```bash
php artisan route:list --path=api
php artisan test
```

## 9. Flujo resumido del codigo

1. `routes/web.php` atiende pantallas Blade y sesiones web.
2. `routes/api.php` atiende API REST versionada.
3. `AuthenticateJwt` valida el token Bearer y carga el usuario.
4. `EnsureUserHasRole` valida permisos por rol.
5. `SecurityAlertService` registra alertas para admin cuando hay accesos no autorizados.
6. Los controladores API devuelven JSON mediante `ApiResponse` y recursos Laravel.
7. Los controladores web mantienen la experiencia visual de cliente, empleado, supervisor y admin.
8. Las imagenes subidas se guardan en storage y se sirven con `storage:link`.
