@extends('layouts.app')

@section('title', 'Mascotas - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@php
    $totalMascotas = $mascotas->count();
    $perros = $mascotas->filter(fn ($mascota) => strtolower($mascota->especie ?? '') === 'perro')->count();
    $gatos = $mascotas->filter(fn ($mascota) => strtolower($mascota->especie ?? '') === 'gato')->count();
    $clientesUnicos = $mascotas->pluck('id_cliente')->filter()->unique()->count();
@endphp

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel">
    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Pacientes peludos</span>
            <h1>Mascotas</h1>
            <p>Consulta los datos principales de cada mascota y su cliente responsable.</p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-dog"></i>
            <div>
                <strong>{{ $totalMascotas }}</strong>
                <span>mascotas registradas</span>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid" aria-label="Resumen de mascotas">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-paw"></i></span>
            <div>
                <strong>{{ $totalMascotas }}</strong>
                <span>Total mascotas</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--soft"><i class="fas fa-dog"></i></span>
            <div>
                <strong>{{ $perros }}</strong>
                <span>Perros</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-cat"></i></span>
            <div>
                <strong>{{ $gatos }}</strong>
                <span>Gatos</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-users"></i></span>
            <div>
                <strong>{{ $clientesUnicos }}</strong>
                <span>Clientes asociados</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Registro clínico</span>
                <h2>Listado de Mascotas</h2>
            </div>
        </div>

        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Mascota</th>
                        <th>Especie</th>
                        <th>Raza</th>
                        <th>Sexo</th>
                        <th>Tamaño</th>
                        <th>Peso</th>
                        <th>Edad</th>
                        <th>Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mascotas as $mascota)
                        @php
                            $persona = $mascota->cliente->persona ?? null;
                            $cliente = trim(($persona->nombres ?? '') . ' ' . ($persona->apellido_paterno ?? '') . ' ' . ($persona->apellido_materno ?? ''));
                        @endphp
                        <tr>
                            <td class="fw-bold">#{{ $mascota->id_mascota }}</td>
                            <td>{{ $mascota->nombre ?: 'Sin nombre' }}</td>
                            <td><span class="badge bg-primary">{{ $mascota->especie ?: 'N/A' }}</span></td>
                            <td>{{ $mascota->raza ?: 'N/A' }}</td>
                            <td>{{ $mascota->sexo ?: 'N/A' }}</td>
                            <td>{{ $mascota->tamano ?: 'N/A' }}</td>
                            <td>{{ $mascota->peso ? $mascota->peso . ' kg' : 'N/A' }}</td>
                            <td>{{ $mascota->edad ? $mascota->edad . ' años' : 'N/A' }}</td>
                            <td>{{ $cliente ?: 'Sin cliente' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="admin-empty-state">
                                    <i class="fas fa-paw"></i>
                                    <h3>No hay mascotas registradas</h3>
                                    <p>Las mascotas agregadas por los clientes aparecerán en este listado.</p>
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
