# Pet Grooming

Sistema Laravel para gestion de estetica veterinaria: clientes, mascotas, reservas, servicios, pagos, boletas, panel de empleado, supervisor y administracion.

## Requisitos

- PHP 8.2 o superior
- Composer
- MySQL o MariaDB
- Node.js y npm, si vas a compilar assets

## Instalacion rapida

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Crea una base vacia llamada `spa_mascotas` en MySQL/MariaDB:

```sql
CREATE DATABASE spa_mascotas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Revisa en `.env` que la conexion apunte a esa base:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spa_mascotas
DB_USERNAME=root
DB_PASSWORD=
```

Carga la base integrada del proyecto:

```bash
php artisan petgrooming:instalar-db
php artisan storage:link
php artisan serve
```

Luego abre:

```txt
http://127.0.0.1:8000
```

## Base de datos

El SQL base vive dentro del repositorio:

```txt
database/sql/spa_mascotas_base.sql
```

Para reinstalar la base desde cero:

```bash
php artisan petgrooming:instalar-db --fresh
```

Mas detalle en:

```txt
docs/base-de-datos-integrada.md
```

## Notas de seguridad

- No subas `.env` al repositorio.
- No guardes contrasenas reales ni contrasenas de aplicaciones en archivos versionados.
- Usa `.env.example` solo como plantilla.
- Si cambias la estructura de la base, crea migraciones nuevas para que el cambio viaje con el codigo.
