@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/finalizar.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="completada-box text-center">
    <img src="{{ asset('images/completada.png') }}" alt="Reserva Completada" class="mb-4" style="width: 150px;">
    <h2>✔ Reserva Completada</h2>
    <p>Tu reserva se confirmó con éxito. Te hemos enviado un correo con los detalles.</p>

    @isset($pago)
        <div class="mt-3 d-flex gap-2 justify-content-center flex-wrap">
            {{-- Ver/abrir PDF vía controlador --}}
            <a href="{{ route('reservas.boleta', ['id_pago' => $pago->id_pago]) }}" class="btn btn-success" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Ver Boleta PDF
            </a>
            {{-- Descargar archivo guardado en storage (requiere php artisan storage:link) --}}
            <a href="{{ asset('storage/boletas/'.$pago->series.'.pdf') }}" class="btn btn-outline-primary" download>
                <i class="fa-solid fa-download"></i> Descargar ({{ $pago->series }})
            </a>
        </div>
    @endisset

    <br><br>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Volver al inicio</a>
</div>
@endsection
