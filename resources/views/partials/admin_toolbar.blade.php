@php
    $adminLinks = [
        [
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'icon' => 'fas fa-chart-line',
            'label' => 'Dashboard',
        ],
        [
            'route' => 'admin.usuarios',
            'match' => 'admin.usuarios*',
            'icon' => 'fas fa-users',
            'label' => 'Usuarios',
        ],
        [
            'route' => 'admin.mascotas',
            'match' => 'admin.mascotas',
            'icon' => 'fas fa-dog',
            'label' => 'Mascotas',
        ],
        [
            'route' => 'admin.reservas',
            'match' => 'admin.reservas',
            'icon' => 'fas fa-calendar-check',
            'label' => 'Reservas',
        ],
        [
            'route' => 'admin.servicios',
            'match' => 'admin.servicios*',
            'icon' => 'fas fa-scissors',
            'label' => 'Servicios',
        ],
    ];
@endphp

<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        @foreach($adminLinks as $item)
            <li class="nav-item mb-2">
                <a href="{{ route($item['route']) }}"
                   class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                    <i class="{{ $item['icon'] }} fs-5"></i>
                    <span class="fw-semibold">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach

        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>
