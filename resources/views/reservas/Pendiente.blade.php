@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/finalizar.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="pendiente-box">
    <img src="{{ asset('images/pendiente.png') }}" alt="Reserva Pendiente">
    <h2>⌛ Reserva Pendiente</h2>
    <p>Tu reserva está pendiente de confirmación. Se te notificará por correo una vez validado el pago.</p>
    <a href="{{ URL('/dashboard') }}" class="btn-volver">Regresar al inicio</a>
</div>

@endsection
