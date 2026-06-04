@extends('layouts.header')

@section('title', 'Mi Perfil - Intranet')

@section('header')
    @include('partials.admin_header')
@endsection

@php
    $apellidos = trim($persona->apellidos ?? trim(($persona->apellido_paterno ?? '') . ' ' . ($persona->apellido_materno ?? '')));
    $nombreCompleto = trim(($persona->nombres ?? '') . ' ' . $apellidos);
    $tipoDocumento = $persona->tipo_doc ?? $persona->tipo_documento ?? 'Sin registrar';
    $numeroDocumento = $persona->nro_documento ?? 'Sin registrar';
    $fechaNacimiento = $persona->fecha_nacimiento ?? null;
    $fechaNacimientoValor = $fechaNacimiento ? substr((string) $fechaNacimiento, 0, 10) : '';
    $mfaEstado = $usuario->mfa_bypass ? 'Omitido' : ($usuario->mfa_enabled ? 'Activo' : 'Pendiente');
    $mfaClase = $usuario->mfa_bypass ? 'profile-pill--soft' : ($usuario->mfa_enabled ? 'profile-pill--ok' : 'profile-pill--warn');
@endphp

@section('content')
<main class="admin-content intranet-profile-panel">
    <section class="admin-shell-hero intranet-profile-hero">
        <div>
            <span class="admin-eyebrow">Intranet Pet Grooming</span>
            <h1>Mi Perfil</h1>
            <p>
                Revisa y actualiza tus datos personales sin salir de tu sesion interna.
            </p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-user-shield"></i>
            <div>
                <strong>{{ $usuario->rol }}</strong>
                <span>{{ $nombreCompleto ?: 'Personal interno' }}</span>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid intranet-profile-summary" aria-label="Resumen del perfil">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-envelope"></i></span>
            <div>
                <strong>{{ $usuario->correo }}</strong>
                <span>Correo institucional</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--soft"><i class="fas fa-id-badge"></i></span>
            <div>
                <strong>{{ $tipoDocumento }}</strong>
                <span>{{ $numeroDocumento }}</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-key"></i></span>
            <div>
                <strong>{{ $mfaEstado }}</strong>
                <span>Estado MFA</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--soft"><i class="fas fa-briefcase-medical"></i></span>
            <div>
                <strong>{{ $empleado->puesto ?? $usuario->rol }}</strong>
                <span>Area o cargo</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card intranet-profile-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Datos personales</span>
                <h2>Informacion de la cuenta</h2>
            </div>
            <span class="profile-pill {{ $mfaClase }}">
                <i class="fas fa-shield-alt"></i>
                MFA {{ $mfaEstado }}
            </span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los datos ingresados.</strong>
                <ul class="profile-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('intranet.perfil.update') }}" class="intranet-profile-form">
            @csrf
            @method('PUT')

            <div class="profile-form-grid">
                <div>
                    <label class="form-label" for="nombres">Nombres</label>
                    <input
                        type="text"
                        class="form-control"
                        id="nombres"
                        name="nombres"
                        value="{{ old('nombres', $persona->nombres ?? '') }}"
                        required>
                </div>

                <div>
                    <label class="form-label" for="apellidos">Apellidos</label>
                    <input
                        type="text"
                        class="form-control"
                        id="apellidos"
                        name="apellidos"
                        value="{{ old('apellidos', $apellidos) }}">
                </div>

                <div>
                    <label class="form-label" for="correo">Correo</label>
                    <input
                        type="email"
                        class="form-control"
                        id="correo"
                        value="{{ $usuario->correo }}"
                        disabled>
                </div>

                <div>
                    <label class="form-label" for="rol">Rol</label>
                    <input
                        type="text"
                        class="form-control"
                        id="rol"
                        value="{{ $usuario->rol }}"
                        disabled>
                </div>

                <div>
                    <label class="form-label" for="telefono">Telefono</label>
                    <input
                        type="text"
                        class="form-control"
                        id="telefono"
                        name="telefono"
                        value="{{ old('telefono', $persona->telefono ?? '') }}">
                </div>

                <div>
                    <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input
                        type="date"
                        class="form-control"
                        id="fecha_nacimiento"
                        name="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento', $fechaNacimientoValor) }}">
                </div>

                <div class="profile-form-wide">
                    <label class="form-label" for="direccion">Direccion</label>
                    <input
                        type="text"
                        class="form-control"
                        id="direccion"
                        name="direccion"
                        value="{{ old('direccion', $persona->direccion ?? '') }}">
                </div>
            </div>

            <div class="admin-form-actions profile-form-actions">
                <a href="{{ $usuario->rol === 'Admin' ? route('admin.dashboard') : route('empleado.bandeja.reservas') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver al panel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
</main>
@endsection
