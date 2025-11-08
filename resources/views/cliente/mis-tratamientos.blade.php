@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('title', 'Mis Tratamientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mis-reservas.css') }}">
@endpush

@section('content')
<div class="mis-reservas-container">
    <header class="reservas-header">
        <h1><i class="fas fa-history"></i> Mis Tratamientos Comprados</h1>
    </header>

    <div class="reservas-list mt-3">
        @forelse($tratamientos as $tratamiento)
            <div class="reserva-card">
                <div class="reserva-info">
                    <img src="{{ asset('images/default-pet.png') }}" 
                         alt="{{ $tratamiento->mascota_nombre }}" 
                         class="pet-avatar">
                    <div class="reserva-details">
                        <h3 class="pet-name">{{ strtoupper($tratamiento->mascota_nombre) }}</h3>
                        <p class="reserva-fecha">{{ \Carbon\Carbon::parse($tratamiento->fecha)->format('d/m/Y') }} <small class="text-muted">{{ $tratamiento->hora }}</small></p>
                        <p class="reserva-servicios">{{ $tratamiento->nombre_servicio }} <span class="badge bg-primary">{{ $tratamiento->categoria }}</span></p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div>Precio: <strong>S/. {{ number_format($tratamiento->precio_unitario, 2) }}</strong></div>
                        <div>Total: <strong>S/. {{ number_format($tratamiento->total, 2) }}</strong></div>
                    </div>

                    <div>
                        @if($tratamiento->estado == 'A')
                            <span class="badge bg-success">Activo</span>
                        @elseif($tratamiento->estado == 'P')
                            <span class="badge bg-warning">Pendiente</span>
                        @else
                            <span class="badge bg-danger">Cancelado</span>
                        @endif
                    </div>

                    <div>
                        @if(isset($tratamiento->id_reserva))
                            <a href="{{ route('reservas.show', $tratamiento->id_reserva) }}" class="btn-action btn-detalles">Ver Reserva</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <p>No tienes tratamientos registrados aún.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
