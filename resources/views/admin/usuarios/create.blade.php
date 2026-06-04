@extends('layouts.app')

@section('title', 'Crear Usuario - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel">
    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Nuevo acceso</span>
            <h1>Crear usuario</h1>
            <p>Registra cuentas para clientes o personal interno manteniendo el acceso separado por rol.</p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-user-plus"></i>
            <div>
                <strong>Alta</strong>
                <span>usuario seguro</span>
            </div>
        </div>
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Datos de acceso</span>
                <h2>Informacion del usuario</h2>
            </div>
            <div class="admin-actions">
                <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver
                </a>
            </div>
        </div>

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

        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="admin-user-form">
            @csrf

            <div class="profile-form-grid">
                <div>
                    <label class="form-label" for="nombres">Nombres</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres') }}" required>
                </div>

                <div>
                    <label class="form-label" for="apellidos">Apellidos</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
                </div>

                <div>
                    <label class="form-label" for="tipo_doc">Tipo de documento</label>
                    <select class="form-select" id="tipo_doc" name="tipo_doc" required>
                        <option value="">Seleccionar</option>
                        <option value="DNI" @selected(old('tipo_doc') === 'DNI')>DNI</option>
                        <option value="CE" @selected(old('tipo_doc') === 'CE')>CE</option>
                        <option value="PASAPORTE" @selected(old('tipo_doc') === 'PASAPORTE')>Pasaporte</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="nro_documento">Nro. documento</label>
                    <input type="text" class="form-control" id="nro_documento" name="nro_documento" value="{{ old('nro_documento') }}" required>
                </div>

                <div>
                    <label class="form-label" for="telefono">Telefono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}">
                </div>

                <div>
                    <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                </div>

                <div class="profile-form-wide">
                    <label class="form-label" for="direccion">Direccion</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion') }}">
                </div>

                <div>
                    <label class="form-label" for="correo">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="{{ old('correo') }}" required>
                </div>

                <div>
                    <label class="form-label" for="rol">Rol</label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="">Seleccionar</option>
                        <option value="Cliente" @selected(old('rol') === 'Cliente')>Cliente</option>
                        <option value="Empleado" @selected(old('rol') === 'Empleado')>Empleado</option>
                        <option value="Supervisor" @selected(old('rol') === 'Supervisor')>Supervisor</option>
                        <option value="Admin" @selected(old('rol') === 'Admin')>Admin</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="estado">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="A" @selected(old('estado', 'A') === 'A')>Activo</option>
                        <option value="I" @selected(old('estado') === 'I')>Inactivo</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="puesto">Puesto interno</label>
                    <input type="text" class="form-control" id="puesto" name="puesto" value="{{ old('puesto') }}" placeholder="Ej. Groomer, Supervisor, Sistemas">
                </div>

                <div>
                    <label class="form-label" for="password">Contrasena temporal</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                </div>

                <div>
                    <label class="form-label" for="password_confirmation">Confirmar contrasena</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="admin-help-box">
                <i class="fas fa-shield-heart"></i>
                <p>La contrasena debe tener minimo 9 caracteres, mayusculas, minusculas, numeros y simbolos. El MFA se activara cuando el usuario ingrese por primera vez.</p>
            </div>

            <div class="admin-form-actions profile-form-actions">
                <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    Crear usuario
                </button>
            </div>
        </form>
    </section>
</main>
@endsection
