{{-- Header para Empleado --}}
<nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #FFC0CB 0%, #FFB6C1 100%);">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold" href="#">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px;" class="me-2">
            Estética Canina
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEmpleado">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarEmpleado">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('empleado.bandeja.reservas') }}">Atención Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('empleado.gestionar.novedades') }}">Novedades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white active" href="{{ route('empleado.gestionar.turnos') }}">Horarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Reserva</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <span class="badge bg-white text-dark px-3 py-2 rounded-pill">
                    Empleado
                </span>
                <form action="{{ route('logout') }}" method="POST" class="ms-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</nav>