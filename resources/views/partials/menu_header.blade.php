<link rel="stylesheet" href="{{ asset('css/admin_header.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Header del Menú Principal --}}
<header class="admin-site-header">
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <a href="{{ route('home') }}" class="admin-logo-link">
                <div class="admin-logo">
                    <i class="fas fa-paw"></i>
                </div>
                <span class="admin-brand-text">Pet Grooming</span>
            </a>
            
            {{-- Dropdown del Cliente --}}
            <div class="client-dropdown-container">
                <button class="client-dropdown-btn" onclick="toggleClientDropdown()">
                    <i class="fas fa-user-circle"></i>
                    <span>Cliente</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                
                <div class="client-dropdown-menu" id="client-dropdown">
                    @auth
                        {{-- Si el usuario está autenticado --}}
                        <div class="dropdown-header">
                            <div class="dropdown-user-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="dropdown-user-details">
                                <span class="dropdown-user-name">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                                <span class="dropdown-user-email">{{ auth()->user()->correo }}</span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ url('/perfil') }}" class="dropdown-option">
                            <i class="fas fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('reservas.mis-reservas') }}" class="dropdown-option">
                            <i class="fas fa-history"></i>
                            <span>Mis Reservas</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-option logout-option">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    @else
                        {{-- Si el usuario NO está autenticado --}}
                        <button type="button" class="dropdown-option" onclick="openLoginModal()">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Iniciar Sesión</span>
                        </button>
                        <button type="button" class="dropdown-option" onclick="openRegisterModal()">
                            <i class="fas fa-user-plus"></i>
                            <span>Registrarse</span>
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
/* Estilos para el dropdown del cliente */
.client-dropdown-container {
    position: relative;
}

.client-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 8px 16px;
    color: white;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.client-dropdown-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

.dropdown-arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
}

.client-dropdown-btn.active .dropdown-arrow {
    transform: rotate(180deg);
}

.client-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
    margin-top: 8px;
}

.client-dropdown-menu.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.dropdown-user-avatar {
    font-size: 24px;
}

.dropdown-user-details {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.dropdown-user-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
}

.dropdown-user-email {
    font-size: 12px;
    opacity: 0.9;
}

.dropdown-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 8px 0;
}

.dropdown-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
    font-family: inherit;
}

.dropdown-option:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.logout-option {
    color: #dc2626;
}

.logout-option:hover {
    background: #fef2f2;
    color: #dc2626;
}

.logout-form {
    margin: 0;
}
</style>

<script>
function toggleClientDropdown() {
    const dropdown = document.getElementById('client-dropdown');
    const button = document.querySelector('.client-dropdown-btn');
    
    dropdown.classList.toggle('active');
    button.classList.toggle('active');
}

// Funciones para abrir modales (reutilizadas del header principal)
function openLoginModal() {
    // Cerrar dropdown primero
    const dropdown = document.getElementById('client-dropdown');
    const button = document.querySelector('.client-dropdown-btn');
    if (dropdown && button) {
        dropdown.classList.remove('active');
        button.classList.remove('active');
    }
    
    // Abrir modal (el modal está en el header principal)
    const loginModal = document.getElementById('loginModal');
    if (loginModal) {
        loginModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function openRegisterModal() {
    // Cerrar dropdown primero
    const dropdown = document.getElementById('client-dropdown');
    const button = document.querySelector('.client-dropdown-btn');
    if (dropdown && button) {
        dropdown.classList.remove('active');
        button.classList.remove('active');
    }
    
    // Abrir modal (el modal está en el header principal)
    const registerModal = document.getElementById('registerModal');
    if (registerModal) {
        registerModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('client-dropdown');
    const button = document.querySelector('.client-dropdown-btn');
    const container = event.target.closest('.client-dropdown-container');
    
    if (!container && dropdown && button) {
        dropdown.classList.remove('active');
        button.classList.remove('active');
    }
});
</script>