<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Architecture mode
    |--------------------------------------------------------------------------
    |
    | modular_monolith keeps the current Laravel application as the runtime.
    | remote_ready documents service URLs and queues for future extraction.
    |
    */
    'mode' => env('PET_ARCHITECTURE_MODE', 'modular_monolith'),

    'defaults' => [
        'timeout_seconds' => (int) env('PET_SERVICE_TIMEOUT', 10),
        'retry_attempts' => (int) env('PET_SERVICE_RETRIES', 2),
    ],

    'contexts' => [
        'auth' => [
            'label' => 'Auth and Access',
            'route_prefixes' => ['auth', 'intranet'],
            'models' => ['Usuario', 'Persona'],
            'local_service' => App\Services\Auth\JwtService::class,
            'service_url' => env('AUTH_SERVICE_URL'),
            'queue' => env('AUTH_QUEUE', 'auth'),
            'database_connection' => env('AUTH_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 2,
        ],

        'clientes' => [
            'label' => 'Clientes',
            'route_prefixes' => ['clientes', 'perfil'],
            'models' => ['Cliente', 'Persona', 'Usuario'],
            'service_url' => env('CLIENTES_SERVICE_URL'),
            'queue' => env('CLIENTES_QUEUE', 'clientes'),
            'database_connection' => env('CLIENTES_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 5,
        ],

        'mascotas' => [
            'label' => 'Mascotas and Razas',
            'route_prefixes' => ['mascotas', 'razas'],
            'models' => ['Mascota', 'RazaImagen'],
            'service_url' => env('MASCOTAS_SERVICE_URL'),
            'queue' => env('MASCOTAS_QUEUE', 'mascotas'),
            'database_connection' => env('MASCOTAS_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 4,
        ],

        'catalogo' => [
            'label' => 'Catalogo and Servicios',
            'route_prefixes' => ['catalogo', 'servicios'],
            'models' => ['Servicio'],
            'service_url' => env('CATALOGO_SERVICE_URL'),
            'queue' => env('CATALOGO_QUEUE', 'catalogo'),
            'database_connection' => env('CATALOGO_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 4,
        ],

        'reservas' => [
            'label' => 'Reservas',
            'route_prefixes' => ['reservas'],
            'models' => ['Reserva', 'DetalleReserva', 'Atencion', 'Delivery'],
            'local_service' => App\Services\Reservas\ReservationAvailabilityService::class,
            'service_url' => env('RESERVAS_SERVICE_URL'),
            'queue' => env('RESERVAS_QUEUE', 'reservas'),
            'database_connection' => env('RESERVAS_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 3,
        ],

        'pagos' => [
            'label' => 'Pagos and Boletas',
            'route_prefixes' => ['pagos', 'boleta'],
            'models' => ['Pago', 'PagoNotificacion'],
            'service_url' => env('PAGOS_SERVICE_URL'),
            'queue' => env('PAGOS_QUEUE', 'pagos'),
            'database_connection' => env('PAGOS_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 1,
        ],

        'notificaciones' => [
            'label' => 'Notificaciones',
            'route_prefixes' => ['notificaciones'],
            'models' => ['Notificacion'],
            'local_service' => App\Services\Security\SecurityAlertService::class,
            'service_url' => env('NOTIFICACIONES_SERVICE_URL'),
            'queue' => env('NOTIFICACIONES_QUEUE', 'notificaciones'),
            'database_connection' => env('NOTIFICACIONES_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 1,
        ],

        'empleados' => [
            'label' => 'Empleados and Turnos',
            'route_prefixes' => ['empleado', 'turnos', 'novedades'],
            'models' => ['Empleado', 'Turno', 'Novedad'],
            'service_url' => env('EMPLEADOS_SERVICE_URL'),
            'queue' => env('EMPLEADOS_QUEUE', 'empleados'),
            'database_connection' => env('EMPLEADOS_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 6,
        ],

        'reportes' => [
            'label' => 'Reportes and Ingresos',
            'route_prefixes' => ['ingresos', 'metricas', 'reportes'],
            'models' => ['Pago', 'Reserva', 'Empleado'],
            'service_url' => env('REPORTES_SERVICE_URL'),
            'queue' => env('REPORTES_QUEUE', 'reportes'),
            'database_connection' => env('REPORTES_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 2,
        ],

        'admin' => [
            'label' => 'Administracion',
            'route_prefixes' => ['admin'],
            'models' => ['Usuario', 'Servicio', 'Mascota', 'Reserva'],
            'service_url' => env('ADMIN_SERVICE_URL'),
            'queue' => env('ADMIN_QUEUE', 'admin'),
            'database_connection' => env('ADMIN_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'extraction_priority' => 7,
        ],
    ],
];
