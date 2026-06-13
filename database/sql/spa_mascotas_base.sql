-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2025 a las 05:45:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `spa_mascotas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atenciones`
--

CREATE TABLE `atenciones` (
  `id_atencion` int(11) NOT NULL,
  `id_reserva` int(11) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `comentarios` varchar(500) DEFAULT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `id_persona`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 5, '2025-09-29 22:24:10', '2025-09-29 22:24:10'),
(2, 5, '2025-10-01 02:03:54', '2025-10-01 02:03:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deliveries`
--

CREATE TABLE `deliveries` (
  `id_delivery` int(11) NOT NULL,
  `id_reserva` int(11) NOT NULL,
  `direccion_recojo` varchar(200) NOT NULL,
  `direccion_entrega` varchar(200) NOT NULL,
  `costo_delivery` decimal(10,2) NOT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_actualizacion` varchar(50) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_reservas`
--

CREATE TABLE `detalles_reservas` (
  `id_detalle` int(11) NOT NULL,
  `id_reserva` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `id_promociones` int(11) DEFAULT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_reservas`
--

INSERT INTO `detalles_reservas` (`id_detalle`, `id_reserva`, `precio_unitario`, `igv`, `total`, `id_servicio`, `id_promociones`, `estado`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`) VALUES
(1, 4, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:06:38', NULL, NULL),
(2, 4, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:06:38', NULL, NULL),
(3, 5, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:07:54', NULL, NULL),
(4, 5, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:07:54', NULL, NULL),
(5, 6, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:09:04', NULL, NULL),
(6, 6, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:09:04', NULL, NULL),
(7, 7, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:10:37', NULL, NULL),
(8, 7, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:10:37', NULL, NULL),
(9, 8, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:14:03', NULL, NULL),
(10, 8, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:14:03', NULL, NULL),
(11, 9, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:15:34', NULL, NULL),
(12, 9, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:15:34', NULL, NULL),
(13, 10, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:17:41', NULL, NULL),
(14, 10, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:17:41', NULL, NULL),
(15, 11, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:18:21', NULL, NULL),
(16, 11, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:18:21', NULL, NULL),
(17, 12, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:21:03', NULL, NULL),
(18, 12, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:21:03', NULL, NULL),
(19, 13, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:21:41', NULL, NULL),
(20, 13, 20.00, 3.60, 23.60, 3, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:21:41', NULL, NULL),
(21, 14, 50.00, 9.00, 59.00, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 23:02:47', NULL, NULL),
(22, 14, 40.00, 7.20, 47.20, 4, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 23:02:47', NULL, NULL),
(23, 15, 50.00, 9.00, 59.00, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-02 21:18:53', NULL, NULL),
(24, 15, 10.00, 1.80, 11.80, 2001, NULL, 'A', 'renzomd68@gmail.com', '2025-10-02 21:18:53', NULL, NULL),
(25, 16, 70.00, 12.60, 82.60, 2, NULL, 'A', 'renzomd68@gmail.com', '2025-10-29 00:43:31', NULL, NULL),
(26, 16, 10.00, 1.80, 11.80, 2001, NULL, 'A', 'renzomd68@gmail.com', '2025-10-29 00:43:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id_empleado`, `id_persona`, `puesto`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 4, 'Veterinario', '2025-09-28 12:43:19', '2025-09-28 12:43:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id_feedback` int(11) NOT NULL,
  `id_reserva` int(11) NOT NULL,
  `calificacion` int(11) NOT NULL,
  `comentarios` varchar(500) DEFAULT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_actualizacion` varchar(50) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `feedbacks`
--

INSERT INTO `feedbacks` (`id_feedback`, `id_reserva`, `calificacion`, `comentarios`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`) VALUES
(1, 15, 5, 'Es la mejor estética canina', 'renzomd68@gmail.com', '2025-12-01 03:57:21', 'renzomd68@gmail.com', '2025-12-01 03:57:21'),
(2, 14, 5, 'Recibí muy buena atención', 'renzomd68@gmail.com', '2025-12-01 04:00:01', 'renzomd68@gmail.com', '2025-12-01 04:00:01'),
(3, 7, 5, 'Lo mejor', 'renzomd68@gmail.com', '2025-12-01 04:47:47', 'renzomd68@gmail.com', '2025-12-01 04:47:47'),
(4, 6, 5, NULL, 'renzomd68@gmail.com', '2025-12-01 05:09:29', 'renzomd68@gmail.com', '2025-12-01 05:09:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id_mascota` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `raza` varchar(50) DEFAULT NULL,
  `tamano` varchar(20) DEFAULT NULL,
  `especie` varchar(50) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id_mascota`, `nombre`, `fecha_nacimiento`, `sexo`, `raza`, `tamano`, `especie`, `peso`, `descripcion`, `id_cliente`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`) VALUES
(1, 'Doki', '2020-05-10', 'Macho', 'border-collie', 'Mediano', 'Perro', 18.50, 'Muy activo y obediente.', 1, 'renzomd68@gmail.com', '2025-09-29 22:24:12', NULL, NULL),
(2, 'Maru', '2019-08-22', 'Macho', 'labrador', 'Grande', 'Perro', 30.20, 'Cariñoso y juguetón.', 1, 'renzomd68@gmail.com', '2025-09-29 22:24:12', NULL, NULL),
(3, 'Rayo', '2021-01-15', 'Macho', 'golden-retriever', 'Grande', 'Perro', 25.70, 'Energético y rápido.', 1, 'renzomd68@gmail.com', '2025-09-29 22:24:12', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_31_161532_add_descripcion_to_servicios_table', 1),
(5, '2025_11_18_070126_add_id_empleado_to_reservas_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_actualizacion` varchar(50) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `id_usuario`, `tipo`, `mensaje`, `fecha_envio`, `estado`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`) VALUES
(1, 2, 'vacunas', 'Recordatorio: Doki necesita su vacuna anual.', '2025-10-02 08:00:00', 'A', 'admin', '2025-11-30 16:44:12', 'admin', '2025-11-30 16:44:12'),
(2, 2, 'promos-nuevas', 'Nueva promoción de baño 2x1 activada.', '2025-10-03 09:00:00', 'A', 'admin', '2025-11-30 16:44:12', 'admin', '2025-11-30 16:44:12'),
(3, 2, 'previas', 'Turno pendiente de confirmación para Maru.', '2025-10-02 10:00:00', 'I', 'admin', '2025-11-30 16:44:12', 'admin', '2025-11-30 16:44:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedades`
--

CREATE TABLE `novedades` (
  `id_novedades` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `resumen` varchar(500) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `usuario_actualizacion` varchar(50) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_reserva` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `series` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_reserva`, `monto`, `metodo_pago`, `fecha`, `hora`, `estado`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`, `series`) VALUES
(1, 15, 30.00, 'paypal', '2025-10-26', '00:51:19', 'P', 'renzomd68@gmail.com', '2025-10-25 19:51:19', NULL, NULL, 'BOL-00001'),
(2, 15, 30.00, 'paypal', '2025-10-26', '01:02:56', 'P', 'renzomd68@gmail.com', '2025-10-25 20:02:56', NULL, NULL, 'BOL-00002'),
(3, 15, 30.00, 'paypal', '2025-10-26', '01:08:09', 'P', 'renzomd68@gmail.com', '2025-10-25 20:08:09', NULL, NULL, 'BOL-00003'),
(4, 15, 30.00, 'paypal', '2025-10-26', '01:09:19', 'P', 'renzomd68@gmail.com', '2025-10-25 20:09:19', NULL, NULL, 'BOL-00004'),
(5, 15, 30.00, 'paypal', '2025-10-26', '01:10:44', 'P', 'renzomd68@gmail.com', '2025-10-25 20:10:44', NULL, NULL, 'BOL-00005'),
(6, 15, 30.00, 'paypal', '2025-10-26', '01:12:07', 'P', 'renzomd68@gmail.com', '2025-10-25 20:12:07', NULL, NULL, 'BOL-00006'),
(7, 15, 30.00, 'paypal', '2025-10-26', '01:13:43', 'P', 'renzomd68@gmail.com', '2025-10-25 20:13:43', NULL, NULL, 'BOL-00007'),
(8, 15, 30.00, 'paypal', '2025-10-26', '01:14:09', 'P', 'renzomd68@gmail.com', '2025-10-25 20:14:09', NULL, NULL, 'BOL-00008'),
(9, 15, 30.00, 'paypal', '2025-10-26', '01:17:18', 'P', 'renzomd68@gmail.com', '2025-10-25 20:17:18', NULL, NULL, 'BOL-00009'),
(10, 15, 70.80, 'paypal', '2025-10-26', '01:23:42', 'P', 'renzomd68@gmail.com', '2025-10-25 20:23:42', NULL, NULL, 'BOL-00010'),
(11, 15, 70.80, 'paypal', '2025-10-26', '01:24:35', 'P', 'renzomd68@gmail.com', '2025-10-25 20:24:35', NULL, NULL, 'BOL-00011'),
(12, 16, 94.40, 'paypal', '2025-10-29', '05:44:12', 'P', 'renzomd68@gmail.com', '2025-10-29 00:44:12', NULL, NULL, 'BOL-00012');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `tipo_doc` varchar(20) NOT NULL,
  `nro_documento` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `nombres`, `apellidos`, `tipo_doc`, `nro_documento`, `telefono`, `direccion`, `fecha_nacimiento`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Juan', 'Cliente', 'DNI', '22222222', '999222222', 'Av. Cliente 456', '1995-02-02', 'A', '2025-09-28 12:36:08', '2025-09-28 12:36:08'),
(4, 'Pedro', 'Empleado', 'DNI', '33333333', '999333333', 'Av. Empleado 789', '1992-03-03', 'A', '2025-09-28 12:43:19', '2025-09-28 12:43:19'),
(5, 'Renzo', 'Mendoza', 'DNI', '72401109', NULL, NULL, NULL, 'A', '2025-09-28 18:32:40', '2025-09-28 18:32:40'),
(6, 'Renzo', 'Mendoza', 'DNI', '72401108', NULL, NULL, NULL, 'A', '2025-10-03 02:07:52', '2025-10-03 02:07:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre_promocion` varchar(100) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `descuento` decimal(5,2) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `imagen_ref` varchar(255) DEFAULT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones_servicios`
--

CREATE TABLE `promociones_servicios` (
  `id_prom_serv` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_mascota` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `enfermedad` tinyint(1) NOT NULL,
  `vacuna` tinyint(1) NOT NULL,
  `alergia` tinyint(1) NOT NULL,
  `descripcion_alergia` varchar(500) DEFAULT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_mascota`, `id_cliente`, `id_usuario`, `id_empleado`, `fecha`, `hora`, `enfermedad`, `vacuna`, `alergia`, `descripcion_alergia`, `estado`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`) VALUES
(4, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:06:38', NULL, NULL),
(5, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:07:54', NULL, NULL),
(6, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:09:04', NULL, NULL),
(7, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'A', 'renzomd68@gmail.com', '2025-10-01 02:10:37', NULL, NULL),
(8, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:14:03', NULL, NULL),
(9, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:15:34', NULL, NULL),
(10, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:17:41', NULL, NULL),
(11, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:18:21', NULL, NULL),
(12, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:21:03', NULL, NULL),
(13, 2, 1, 4, NULL, '2025-10-02', '03:44:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-01 02:21:41', NULL, NULL),
(14, 1, 1, 4, NULL, '2025-10-02', '04:01:00', 1, 1, 1, 'Hola', 'N', 'renzomd68@gmail.com', '2025-10-01 23:02:47', NULL, NULL),
(15, 1, 1, 4, NULL, '2025-10-03', '10:20:00', 1, 1, 1, NULL, 'N', 'renzomd68@gmail.com', '2025-10-02 21:18:53', NULL, NULL),
(16, 2, 1, 4, NULL, '2025-12-01', '02:40:00', 1, 1, 1, NULL, 'P', 'renzomd68@gmail.com', '2025-11-30 00:43:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `tipo_servicio` varchar(50) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `duracion` decimal(10,2) DEFAULT NULL,
  `imagen_referencial` varchar(255) DEFAULT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `categoria`, `tipo_servicio`, `nombre_servicio`, `costo`, `especie`, `duracion`, `imagen_referencial`, `estado`, `usuario_creacion`, `fecha_creacion`, `usuario_actualizacion`, `fecha_actualizacion`, `descripcion`) VALUES
(1, 'Estética', 'Normal', 'Baño Completo', 50.00, 'Perro', 1.00, 'bano.png', 'A', 'admin', '2025-09-30 23:01:22', NULL, NULL, NULL),
(2, 'Estética', 'Normal', 'Peluquería Canina', 70.00, 'Perro', 1.50, 'peluqueria.png', 'A', 'admin', '2025-09-30 23:01:22', NULL, NULL, NULL),
(3, 'Salud', 'Adicional', 'Corte de Uñas', 20.00, 'Perro', 0.30, 'corte-unas.png', 'A', 'admin', '2025-09-30 23:01:23', NULL, NULL, NULL),
(4, 'Salud', 'Adicional', 'Vacuna Antirrábica', 40.00, 'Perro', 0.20, 'vacuna.png', 'A', 'admin', '2025-09-30 23:01:23', NULL, NULL, NULL),
(2001, 'Salud', 'Adicional', 'Cepillo de Dentadura', 10.00, 'Perro', 0.30, 'cepillo.png', 'A', 'admin', '2025-09-30 23:01:23', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0TgGv8SjxkvsfDVszVFPyQGBGdR8c2lf6MiO078B', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOW9pSVVNUXpLTWo3R1hVYXI5d2JMQldsamMzWGxvdWJ2OXo5dGp2eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1764739784),
('86nTprK4ogLQQKq11xlgIQVixiRbiR4uocCGzSrA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidHBGSDhHZThBY1dFeExlaTg2enpJZFFpalVITGtFODN3WEx3c01mVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXNlcnZhcy9zZWxlY2Npb24tbWFzY290YSI7fX0=', 1764740003),
('F985QAT3Xyz6KOD9p3xQ2PcOUqQfYXSig7HjITyA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY1d4VzcwVDVJVFRwS0Y3Y2w4MHByZlR2UUgxWGEzdGk4M2JrYnd0TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXNlcnZhcy9zZWxlY2Npb24tbWFzY290YSI7fX0=', 1764740002),
('gABrlMYxi9ONrvwVDc8Ta6C0pr0nh6gbFPM02ufz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMnZ6U0R1cHRPaU9qbEJJajdlUkdoRzVCVXFhM3hwOVVLaGl1czc1VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9taXMtcmVzZXJ2YXMiO319', 1764739995),
('hZ3WiWxjRDqdR4h5ambbfJNVDdwpXUbQA5ZAaWA1', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZmtTV2pQZktIdElzRVFlcTVFdWpWNGxOU1dEb2czWVQ4dGZNa09qNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lbXBsZWFkby9wYW5lbC1kZWwtZGlhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1765336059),
('kSewECAxgZj9Zyji2xYXlq5rz3409XzXdIIPa3Ac', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTTNySTVqT2tJOTcwcTVRc3RrS21yS2NRQWk4eXNvcVZJZ05CMllxeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9taXMtcmVzZXJ2YXMiO319', 1764740005),
('n7Z3oMWDV076H4A0n38Wxg3mXS6kNkLGhVmtTTgI', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUlpUSUhVelRibVpzRDR3cld1QUlJNnRJekRtd2RxeGVCY3RiZGNLTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZXJmaWwiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1764809500),
('RHj9veUjZEceRQOINVLGfyDXDrKhuvrHNi0JPMSM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMG1TaDl0cm9SNGlIZHo0S21BaUxidE43UzY4T25TMmlUazk4eDFuaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9taXMtcmVzZXJ2YXMiO319', 1764740001),
('tqXpIMLFof13zVi3fX7rDugZhuK5WR4f3Ii7NsVP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibkpic3FyNGUwZVRuRFBJZnRqYUZsSkxodk8yaGdZdXowM1VGenhTQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NTA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXNlcnZhcy80ODAxMzM1MjI3MDgwNjY1MzAxIjt9fQ==', 1764740008),
('uaTHyCicOC137aseM2DUhzQggSltskXL9NutYZzg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSHZsazl0ZkhlNkZtWXlmS1ZQUVVPMWphRE5PaUlUM1FDc1hKTktvQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXRhbG9nbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXNlcnZhcy9zZWxlY2Npb24tbWFzY290YSI7fX0=', 1764740001),
('VUXA93ELNEsnoz7FVq31tmzdPlr1WS62PiPL0Pza', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1NGVElrZ2RzS0l1c01VUE4xUjRzWFdDNHdoN29lYTR2TEluWXVYZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1764739489);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_empleados`
--

CREATE TABLE `turnos_empleados` (
  `id_turno` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `estado` varchar(1) NOT NULL,
  `usuario_creacion` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Cliente','Empleado','Admin') DEFAULT 'Cliente',
  `estado` char(1) DEFAULT 'A',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_persona`, `correo`, `contrasena`, `rol`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(2, 4, 'empleado@spa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Empleado', 'A', '2025-09-28 12:43:19', '2025-09-28 12:43:19'),
(3, 1, 'cliente@spa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 'A', '2025-09-28 12:52:58', '2025-09-28 12:52:58'),
(4, 5, 'renzomd68@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cliente', 'A', '2025-09-28 18:32:40', '2025-09-30 00:06:57'),
(5, 6, 'renzomd8@gmail.com', '$2y$12$.xb1NIIWZ/QaP4Yys3gvPui7Nnbkq78KjMtpljQ1V/5M4OO3vfzlS', 'Cliente', 'A', '2025-10-03 02:07:52', '2025-10-03 02:07:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `atenciones`
--
ALTER TABLE `atenciones`
  ADD PRIMARY KEY (`id_atencion`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id_delivery`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `detalles_reservas`
--
ALTER TABLE `detalles_reservas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_reserva` (`id_reserva`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_promocion` (`id_promociones`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id_feedback`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id_mascota`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD PRIMARY KEY (`id_novedades`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `nro_documento` (`nro_documento`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `promociones_servicios`
--
ALTER TABLE `promociones_servicios`
  ADD PRIMARY KEY (`id_prom_serv`),
  ADD KEY `id_promocion` (`id_promocion`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_mascota` (`id_mascota`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `turnos_empleados`
--
ALTER TABLE `turnos_empleados`
  ADD PRIMARY KEY (`id_turno`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_persona` (`id_persona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `atenciones`
--
ALTER TABLE `atenciones`
  MODIFY `id_atencion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id_delivery` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_reservas`
--
ALTER TABLE `detalles_reservas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id_mascota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `id_novedades` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promociones_servicios`
--
ALTER TABLE `promociones_servicios`
  MODIFY `id_prom_serv` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2002;

--
-- AUTO_INCREMENT de la tabla `turnos_empleados`
--
ALTER TABLE `turnos_empleados`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `atenciones`
--
ALTER TABLE `atenciones`
  ADD CONSTRAINT `atenciones_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`);

--
-- Filtros para la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `detalles_reservas`
--
ALTER TABLE `detalles_reservas`
  ADD CONSTRAINT `detalles_reservas_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`),
  ADD CONSTRAINT `detalles_reservas_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `detalles_reservas_ibfk_3` FOREIGN KEY (`id_promociones`) REFERENCES `promociones` (`id_promocion`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`);

--
-- Filtros para la tabla `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `promociones_servicios`
--
ALTER TABLE `promociones_servicios`
  ADD CONSTRAINT `promociones_servicios_ibfk_1` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`),
  ADD CONSTRAINT `promociones_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `reservas_ibfk_4` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`);

--
-- Filtros para la tabla `turnos_empleados`
--
ALTER TABLE `turnos_empleados`
  ADD CONSTRAINT `turnos_empleados_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
