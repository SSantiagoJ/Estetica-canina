@extends('layouts.app')

@section('title', 'Usuarios - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@php
    $totalUsuarios = $usuarios->count();
    $admins = $usuarios->where('rol', 'Admin')->count();
    $empleados = $usuarios->where('rol', 'Empleado')->count();
    $clientes = $usuarios->where('rol', 'Cliente')->count();

    $estadoUsuario = function ($estado) {
        if (in_array($estado, ['A', 'Activo', 'activo', 1, true], true)) {
            return ['Activo', 'bg-success'];
        }

        if (in_array($estado, ['I', 'Inactivo', 'inactivo', 0, false], true)) {
            return ['Inactivo', 'bg-secondary'];
        }

        return [$estado ?: 'Sin estado', 'bg-secondary'];
    };
@endphp

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel">
    @if(session('success'))
        <div class="alert alert-success admin-flash-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger admin-flash-message">
            {{ session('error') }}
        </div>
    @endif

    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Equipo y accesos</span>
            <h1>Usuarios</h1>
            <p>Revisa las cuentas registradas, su rol, estado de acceso y configuración de MFA.</p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-users"></i>
            <div>
                <strong>{{ $totalUsuarios }}</strong>
                <span>usuarios registrados</span>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid" aria-label="Resumen de usuarios">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-user-shield"></i></span>
            <div>
                <strong>{{ $admins }}</strong>
                <span>Admins</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--soft"><i class="fas fa-user-nurse"></i></span>
            <div>
                <strong>{{ $empleados }}</strong>
                <span>Empleados</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-users"></i></span>
            <div>
                <strong>{{ $clientes }}</strong>
                <span>Clientes</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-key"></i></span>
            <div>
                <strong>{{ $usuarios->where('mfa_enabled', true)->count() }}</strong>
                <span>Con MFA</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Directorio</span>
                <h2>Listado de Usuarios</h2>
            </div>
            <div class="admin-actions">
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i>
                    Nuevo usuario
                </a>
            </div>
        </div>

        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>MFA</th>
                        <th>Omitir MFA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        @php
                            $persona = $usuario->persona;
                            $nombreCompleto = trim(($persona->nombres ?? '') . ' ' . ($persona->apellidos ?? '') . ' ' . ($persona->apellido_paterno ?? '') . ' ' . ($persona->apellido_materno ?? ''));
                            [$estadoLabel, $estadoClass] = $estadoUsuario($usuario->estado);
                        @endphp
                        <tr>
                            <td class="fw-bold">#{{ $usuario->id_usuario }}</td>
                            <td>{{ $nombreCompleto ?: 'Sin nombre registrado' }}</td>
                            <td>{{ $usuario->correo }}</td>
                            <td><span class="badge bg-primary">{{ $usuario->rol ?: 'Sin rol' }}</span></td>
                            <td><span class="badge {{ $estadoClass }}">{{ $estadoLabel }}</span></td>
                            <td>
                                <span class="badge {{ $usuario->mfa_enabled ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $usuario->mfa_enabled ? 'Activo' : 'Pendiente' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $usuario->mfa_bypass ? 'bg-info text-white' : 'bg-secondary' }}">
                                    {{ $usuario->mfa_bypass ? 'Permitido' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-empty-state">
                                    <i class="fas fa-user-plus"></i>
                                    <h3>No hay usuarios registrados</h3>
                                    <p>Las cuentas creadas aparecerán aquí para su revisión.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
