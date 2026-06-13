# Base de datos integrada

El proyecto ya no debe depender de un archivo SQL suelto en Descargas. La base inicial esta dentro del repositorio en:

```txt
database/sql/spa_mascotas_base.sql
```

Ese archivo se carga desde Laravel con un comando Artisan propio:

```bash
php artisan petgrooming:instalar-db
```

## Instalacion despues de clonar

1. Instala dependencias:

```bash
composer install
```

2. Crea el archivo de entorno:

```bash
copy .env.example .env
```

3. Configura en `.env` tu base local. Por defecto se espera MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spa_mascotas
DB_USERNAME=root
DB_PASSWORD=
```

4. Crea la base vacia en MySQL/MariaDB:

```sql
CREATE DATABASE spa_mascotas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Genera la llave de Laravel:

```bash
php artisan key:generate
```

6. Carga la base integrada y aplica migraciones nuevas:

```bash
php artisan petgrooming:instalar-db
```

7. Crea el enlace de storage para imagenes/archivos:

```bash
php artisan storage:link
```

8. Levanta el proyecto:

```bash
php artisan serve
```

## Reinstalar desde cero

Si ya tienes tablas creadas y quieres reconstruir todo:

```bash
php artisan petgrooming:instalar-db --fresh
```

Para automatizarlo sin pregunta de confirmacion:

```bash
php artisan petgrooming:instalar-db --fresh --yes
```

## Recomendacion de evolucion

Este comando deja el proyecto clonable y reproducible desde Laravel. El siguiente paso ideal es convertir poco a poco `spa_mascotas_base.sql` en migraciones y seeders separados:

- Migraciones: estructura de tablas, llaves e indices.
- Seeders: servicios base, usuarios de prueba, empleados y datos iniciales.
- No guardar sesiones, cache ni datos personales reales como datos semilla.
