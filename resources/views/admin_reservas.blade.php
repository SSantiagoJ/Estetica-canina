@extends('layouts.app')

@section('title', 'Admin Dashboard - Pet Grooming')
@section('header')
    @include('partials.admin_header') {{-- Aquí usamos el admin_header que ya hiciste --}}
@endsection

@section('content')
@foreach($reservas as $reserva)
<tr>
    <td>{{ $reserva->id_reserva }}</td>
    <td>{{ $reserva->mascota->nombre ?? 'Sin mascota' }}</td>
    <td>{{ $reserva->cliente->persona->nombres ?? '' }} {{ $reserva->cliente->persona->apellido_paterno ?? '' }}</td>
    <td>
        @foreach($reserva->detalles as $detalle)
            {{ $detalle->servicio->nombre_servicio ?? 'Servicio eliminado' }}
            (S/ {{ number_format($detalle->total,2) }}) <br>
        @endforeach
    </td>
    <td>{{ $reserva->fecha }} {{ $reserva->hora }}</td>
    <td>{{ $reserva->estado == 'P' ? 'Pagado' : 'Pendiente' }}</td>
</tr>
@endforeach

@endsection