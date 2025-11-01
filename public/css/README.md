# Estructura de CSS del Proyecto

Esta carpeta contiene todos los archivos CSS del proyecto organizados en carpetas según su función.

## 📁 Estructura de Carpetas

```
css/
├── core/           # Archivos CSS globales y del sistema
├── cliente/        # Archivos CSS de la interfaz del cliente
├── admin/          # Archivos CSS del panel administrativo
└── auth/           # Archivos CSS de autenticación
```

## 📄 Descripción de Carpetas

### `core/` - Estilos Globales
Contiene los archivos CSS fundamentales que se usan en todo el proyecto:

- **`variables.css`** - Variables CSS globales (colores, espaciado, sombras, tipografía)
- **`app.css`** - Estilos base de la aplicación
- **`header.css`** - Estilos del header principal
- **`estilo_menu.css`** - Estilos del menú de navegación y página de inicio

**Uso:** Se incluyen automáticamente en `layouts/app.blade.php`

---

### `cliente/` - Área del Cliente
Contiene los estilos específicos para las funcionalidades del cliente:

- **`catalogo.css`** - Página del catálogo de servicios
- **`reservas.css`** - Formulario de creación de reservas (pasos 1-3)
- **`mis-reservas.css`** - Página de historial y gestión de reservas
- **`feedback.css`** - Modal y formulario de calificación de servicios
- **`perfil.css`** - Página de perfil del usuario y gestión de mascotas
- **`pago.css`** - Página de proceso de pago
- **`finalizar.css`** - Página de confirmación de reserva
- **`boleta.css`** - Formato de impresión de boletas

**Rutas de uso:**
- `css/cliente/catalogo.css` → `/catalogo`
- `css/cliente/reservas.css` → `/reservas/*`
- `css/cliente/mis-reservas.css` → `/mis-reservas`
- `css/cliente/feedback.css` → Modal de calificación
- `css/cliente/perfil.css` → `/perfil`

---

### `admin/` - Panel Administrativo
Contiene los estilos del área administrativa:

- **`admin_header.css`** - Header del panel administrativo
- **`admin_toolbar.css`** - Barra lateral de navegación del admin
- **`admin_dashboard.css`** - Dashboard principal del administrador
- **`admin-servicios.css`** - Gestión de servicios (CRUD)

**Rutas de uso:**
- `css/admin/admin_header.css` → `partials/admin_header.blade.php`
- `css/admin/admin_toolbar.css` → Menú lateral admin
- `css/admin/admin_dashboard.css` → `/admin/dashboard`
- `css/admin/admin-servicios.css` → `/admin/servicios`

---

### `auth/` - Autenticación
Contiene los estilos de las páginas de autenticación:

- **`login.css`** - Página de inicio de sesión
- **`dashboard.css`** - Dashboard de bienvenida post-login

**Rutas de uso:**
- `css/auth/login.css` → `/login`
- `css/auth/dashboard.css` → `/dashboard`

---

## 🎨 Sistema de Variables CSS

Todos los archivos CSS del proyecto usan las variables definidas en `core/variables.css`:

### Colores Principales
```css
--color-primary: #2E324D          /* Azul oscuro principal */
--color-primary-light: #4F5C92    /* Azul claro */
--color-background: #EECCC3       /* Rosa suave */
--color-background-light: #F9F6F1 /* Beige claro */
```

### Gradientes
```css
--gradient-primary: linear-gradient(135deg, #2E324D 0%, #1d2738 100%)
--gradient-warning: linear-gradient(135deg, #ffc107 0%, #ff9800 100%)
--gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%)
```

### Espaciado
```css
--spacing-xs: 0.25rem    /* 4px */
--spacing-sm: 0.5rem     /* 8px */
--spacing-md: 1rem       /* 16px */
--spacing-lg: 1.5rem     /* 24px */
--spacing-xl: 2rem       /* 32px */
--spacing-2xl: 3rem      /* 48px */
```

### Sombras
```css
--shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.08)
--shadow-md: 0 4px 8px rgba(0, 0, 0, 0.12)
--shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.16)
```

---

## 📝 Convenciones de Uso

### En Vistas Blade

**Método recomendado usando `@push`:**
```php
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cliente/catalogo.css') }}">
@endpush
```

**Para múltiples archivos CSS:**
```php
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cliente/mis-reservas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cliente/feedback.css') }}">
@endpush
```

---

## 🔧 Mantenimiento

### Agregar Nuevo Archivo CSS

1. **Identificar la categoría**: ¿Es core, cliente, admin o auth?
2. **Crear el archivo** en la carpeta correspondiente
3. **Usar variables CSS** de `core/variables.css`
4. **Incluir en la vista** usando `@push('styles')`

### Modificar Estilos Globales

- Editar `core/variables.css` para cambios en colores, espaciado, etc.
- Los cambios se aplicarán automáticamente en todo el proyecto

### Buenas Prácticas

✅ **SÍ hacer:**
- Usar variables CSS (`var(--color-primary)`)
- Organizar por secciones con comentarios
- Seguir la convención BEM para clases CSS
- Incluir media queries para responsive

❌ **NO hacer:**
- Hardcodear colores directamente (`#2E324D`)
- Usar `!important` innecesariamente
- Crear estilos inline en las vistas
- Duplicar código CSS entre archivos

---

## 📊 Estadísticas del Proyecto

- **Total de archivos CSS:** 18
- **Carpetas organizadas:** 4
- **Variables globales:** ~40
- **Convención:** BEM + CSS Variables
- **Framework:** Bootstrap 5.3.2 (CDN)
- **Iconos:** Font Awesome 6.4.0

---

## 🚀 Ventajas de Esta Estructura

1. **Modularidad:** Cada página tiene su propio archivo CSS
2. **Mantenibilidad:** Fácil localizar y modificar estilos
3. **Escalabilidad:** Agregar nuevas funcionalidades es simple
4. **Consistencia:** Variables CSS aseguran diseño uniforme
5. **Performance:** Solo se cargan los CSS necesarios por página
6. **Colaboración:** Estructura clara para trabajo en equipo

---

Última actualización: 31 de Octubre, 2025
