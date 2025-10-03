<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PetSpa')</title>
<!-- Normalmente esto es una plantilla de las paginas -->
    <!-- CSS global -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    {{-- HEADER dinámico --}}
    @yield('header')

    {{-- CONTENIDO dinámico --}}
    <div class="main-container">
        @yield('content')
    </div>
    @yield('scripts')
</body>
</html>
