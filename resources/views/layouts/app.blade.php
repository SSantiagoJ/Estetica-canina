<!DOCTYPE html>
<html lang="es">
<head>
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
