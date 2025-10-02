@extends('layouts.app')

@section('title', 'Panel de Administración')
@section('header')
    @include('partials.admin_header')
@endsection
@section('content')
<main class="dashboard">
    <section class="card-container">
        <div class="card">
            <h2>Gestión de Usuarios</h2>
            <p>Administra los usuarios del sistema.</p>
        </div>
        <div class="card">
            <h2>Reportes</h2>
            <p>Visualiza estadísticas y reportes de la plataforma.</p>
        </div>
        <div class="card">
            <h2>Configuraciones</h2>
            <p>Ajusta las opciones generales del sistema.</p>
        </div>
    </section>
</main>
@endsection
