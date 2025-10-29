@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-history"></i> Mis Tratamientos Comprados
            </h2>
            
            @if($tratamientos->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    No tienes tratamientos registrados aún.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Mascota</th>
                                <th>Tratamiento</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tratamientos as $tratamiento)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($tratamiento->fecha)->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $tratamiento->hora }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $tratamiento->mascota_nombre }}</strong>
                                    </td>
                                    <td>{{ $tratamiento->nombre_servicio }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $tratamiento->categoria }}
                                        </span>
                                    </td>
                                    <td>S/. {{ number_format($tratamiento->precio_unitario, 2) }}</td>
                                    <td>
                                        <strong>S/. {{ number_format($tratamiento->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($tratamiento->estado == 'A')
                                            <span class="badge bg-success">Activo</span>
                                        @elseif($tratamiento->estado == 'P')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
