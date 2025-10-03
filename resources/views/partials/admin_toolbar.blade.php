
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<aside class="bg-primary text-white position-fixed top-0 start-0 h-100 shadow-sm d-flex flex-column pt-4"
       style="width: 250px; margin-top:70px; border-top-right-radius: 12px;">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('admin.usuarios') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-users fs-5"></i> 
                <span class="fw-semibold">Usuarios</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.mascotas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-dog fs-5"></i> 
                <span class="fw-semibold">Mascotas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i> 
                <span class="fw-semibold">Reservas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.servicios') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-cut fs-5"></i> 
                <span class="fw-semibold">Servicios</span>
            </a>
        </li>
    </ul>
</aside>