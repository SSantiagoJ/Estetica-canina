@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection

@section('content')

<link rel="stylesheet" href="{{ asset('css/finalizar.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="completada-box">
    <img src="{{ asset('images/completada.png') }}" alt="Reserva Completada">
    <h2>✔ Reserva Completada</h2>
    <p>Tu reserva se confirmó con éxito. Te hemos enviado un correo con los detalles.</p>
    <a href="{{ URL('/dashboard') }}" class="btn-finalizar">Volver al inicio</a>
</div>
@endsection
