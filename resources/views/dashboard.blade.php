@extends('layouts.app')

@section('title', 'Dashboard - Pet Grooming')
@section('header')
    @include('partials.header')
@endsection
@section('content')
<main class="dashboard">
    <section class="welcome">
            <h1>
            Bienvenido,
            @if(auth()->check())
                {{ auth()->user()->nombres }} 👋
                <small>({{ auth()->user()->rol }})</small>
            @else
                Invitado 👋
            @endif
        </h1>


        @auth
            <p>Tu ID de usuario es: <strong>{{ auth()->user()->id_usuario }}</strong></p>
            <p>Tu rol es: <strong>{{ auth()->user()->rol }}</strong></p>
        @else
            <p>No has iniciado sesión. Usa los botones de arriba para ingresar.</p>
        @endauth
    </section>

    <section class="card-container">
        <div class="card">
            <h2>Perfil</h2>
            <p>Accede a tus datos personales y actualiza tu información.</p>
        </div>
        <div class="card">
            <h2>Citas</h2>
            <p>Consulta y administra tus reservas de grooming.</p>
        </div>
        <div class="card">
            <h2>Servicios</h2>
            <p>Explora todos los servicios disponibles para tu mascota.</p>
        </div>
    </section>
</main>
@endsection
