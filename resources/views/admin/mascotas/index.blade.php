@extends('layouts.app')

@section('title', 'Mascotas - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-mascotas.css') }}">
@endpush

@php
    $razaImagenes = $razaImagenes ?? collect();
    $totalMascotas = $mascotas->count();
    $perros = $mascotas->filter(fn ($mascota) => strtolower($mascota->especie ?? '') === 'perro')->count();
    $gatos = $mascotas->filter(fn ($mascota) => strtolower($mascota->especie ?? '') === 'gato')->count();
    $clientesUnicos = $mascotas->pluck('id_cliente')->filter()->unique()->count();
    $totalFotosRazas = $razaImagenes->count();
@endphp

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel">
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <span><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div>
                <strong><i class="fas fa-triangle-exclamation me-2"></i>No se pudo guardar la foto</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Pacientes peludos</span>
            <h1>Mascotas</h1>
            <p>Consulta mascotas y administra fotos por raza para perros, gatos y otros pacientes.</p>
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
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-image"></i></span>
            <div>
                <strong>{{ $totalFotosRazas }}</strong>
                <span>Fotos de razas</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card breed-upload-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Biblioteca visual</span>
                <h2>Fotos por raza</h2>
                <p class="breed-section-copy">
                    Sube una foto por especie y raza. Si ya existe, se reemplazara automaticamente.
                </p>
            </div>
            <span class="breed-limit-pill">
                <i class="fas fa-cloud-arrow-up"></i>
                Max. 50 MB
            </span>
        </div>

        <form action="{{ route('admin.mascotas.razas.store') }}" method="POST" enctype="multipart/form-data" class="breed-photo-form">
            @csrf
            <div class="breed-form-grid">
                <div class="breed-form-field">
                    <label for="especie" class="form-label">Especie</label>
                    <select name="especie" id="especie" class="form-select" required>
                        <option value="">Seleccionar</option>
                        @foreach(['Perro', 'Gato', 'Otro'] as $especie)
                            <option value="{{ $especie }}" @selected(old('especie') === $especie)>{{ $especie }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="breed-form-field">
                    <label for="raza" class="form-label">Raza</label>
                    <input type="text" name="raza" id="raza" class="form-control" value="{{ old('raza') }}" placeholder="Ej. Labrador, Persa, Conejo Belier" required>
                </div>

                <div class="breed-form-field breed-file-field">
                    <label for="imagen" class="form-label">Foto referencial</label>
                    <input type="file" name="imagen" id="imagen" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" required>
                    <small>Formatos: JPG, PNG, WEBP o GIF. Peso maximo: 50 MB.</small>
                </div>

                <button type="submit" class="btn-nuevo-servicio breed-submit-btn">
                    <i class="fas fa-upload"></i>
                    Guardar foto
                </button>
            </div>
        </form>

        @if($razaImagenes->isNotEmpty())
            <div class="breed-gallery">
                @foreach($razaImagenes as $imagen)
                    <article class="breed-photo-card">
                        <img src="{{ $imagen->url }}" alt="{{ $imagen->raza }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                        <div>
                            <span>{{ $imagen->especie }}</span>
                            <h3>{{ $imagen->raza }}</h3>
                            <p>{{ $imagen->tamano_legible }}</p>
                        </div>
                        <form action="{{ route('admin.mascotas.razas.destroy', $imagen) }}" method="POST" onsubmit="return confirm('Eliminar la foto de {{ $imagen->raza }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="breed-delete-btn" title="Eliminar foto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        @else
            <div class="breed-empty-note">
                <i class="fas fa-paw"></i>
                <span>Aun no hay fotos por raza. Carga la primera para que aparezca en perfil y reservas.</span>
            </div>
        @endif
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Registro clinico</span>
                <h2>Listado de Mascotas</h2>
            </div>
        </div>

        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Foto</th>
                        <th>Mascota</th>
                        <th>Especie</th>
                        <th>Raza</th>
                        <th>Sexo</th>
                        <th>Tamano</th>
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
                            $razaImagen = strtolower(str_replace(' ', '-', $mascota->raza ?? 'default'));
                            $fotoMascota = $mascota->foto ?? asset('images/razas/' . $razaImagen . '.png');
                        @endphp
                        <tr>
                            <td class="fw-bold">#{{ $mascota->id_mascota }}</td>
                            <td>
                                <img src="{{ $fotoMascota }}"
                                     alt="{{ $mascota->nombre ?: 'Mascota' }}"
                                     class="breed-table-thumb"
                                     onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                            </td>
                            <td>{{ $mascota->nombre ?: 'Sin nombre' }}</td>
                            <td><span class="badge bg-primary">{{ $mascota->especie ?: 'N/A' }}</span></td>
                            <td>{{ $mascota->raza ?: 'N/A' }}</td>
                            <td>{{ $mascota->sexo ?: 'N/A' }}</td>
                            <td>{{ $mascota->tamano ?: 'N/A' }}</td>
                            <td>{{ $mascota->peso ? $mascota->peso . ' kg' : 'N/A' }}</td>
                            <td>{{ $mascota->edad ? $mascota->edad . ' anos' : 'N/A' }}</td>
                            <td>{{ $cliente ?: 'Sin cliente' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="admin-empty-state">
                                    <i class="fas fa-paw"></i>
                                    <h3>No hay mascotas registradas</h3>
                                    <p>Las mascotas agregadas por los clientes apareceran en este listado.</p>
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
