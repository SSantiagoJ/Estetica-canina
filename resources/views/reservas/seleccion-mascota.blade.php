@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection

@section('content')

<link rel="stylesheet" href="{{ asset('css/cliente/reservas.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container">
    <!-- Barra de progreso -->
    <div class="progressbar mb-5">
        <ul class="steps">
            <li class="active">Selección de Mascotas</li>
            <li>Selección de Servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion">Selección de Mascotas</h2>

    <form action="{{ route('reservas.seleccionServicio') }}" method="POST">
        @csrf
        <div class="row justify-content-center">
            @foreach($mascotas as $mascota)
                <div class="col-md-3 col-sm-6 mb-4">
                    <label class="mascota-card">
                        <input type="checkbox" name="mascotas[]" value="{{ $mascota->id_mascota }}">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/razas/' . $mascota->raza . '.png') }}"
                                alt="{{ $mascota->nombre }}"
                                class="mascota-img">
                            <h5 class="mascota-nombre">{{ $mascota->nombre }}</h5>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="acciones mt-4">
            <a href="{{ url('/dashboard') }}" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-siguiente">Siguiente</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
