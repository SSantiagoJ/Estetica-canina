@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/reservas.css') }}">

<div class="container reserva-container">
    <div class="progressbar mb-5">
        <ul class="steps">
            <li class="active">Selección de mascotas</li>
            <li>Selección de servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <div class="reserva-heading">
        <span class="reserva-eyebrow">Agenda tu cita</span>
        <h2 class="titulo-seccion">Selecciona tu mascota</h2>
    </div>

    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <form action="{{ route('reservas.seleccionServicio') }}" method="POST">
        @csrf
        <div class="row justify-content-center">
            @forelse($mascotas as $mascota)
                @php
                    $razaImagen = strtolower(str_replace(' ', '-', $mascota->raza ?? 'default'));
                    $fotoMascota = $mascota->foto ?? asset('images/razas/' . $razaImagen . '.png');
                @endphp
                <div class="col-md-3 col-sm-6 mb-4">
                    <label class="mascota-card">
                        <input type="checkbox" name="mascotas[]" value="{{ $mascota->id_mascota }}">
                        <div class="card-body text-center">
                            <img src="{{ $fotoMascota }}"
                                alt="{{ $mascota->nombre }}"
                                class="mascota-img"
                                onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                            <h5 class="mascota-nombre">{{ $mascota->nombre }}</h5>
                        </div>
                    </label>
                </div>
            @empty
                <div class="col-12">
                    <div class="reserva-empty-state">
                        <i class="fas fa-paw"></i>
                        <h3>Aún no tienes mascotas registradas</h3>
                        <p>Agrega primero a tu mascota en tu perfil para poder crear una reserva.</p>
                        <a href="{{ route('perfil.index') }}" class="btn-siguiente">Agregar mascota</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="acciones mt-4">
            <a href="{{ url('/') }}" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-siguiente" @if($mascotas->isEmpty()) disabled @endif>Siguiente</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
