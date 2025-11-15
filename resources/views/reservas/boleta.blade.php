<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Pago</title>
    <link rel="stylesheet" href="{{ public_path('css/cliente/boleta.css') }}" type="text/css">
</head>
<body>
  <div class="boleta-header">
    {{-- Si tienes logo en public/images/logo.png --}}
    {{-- <img src="{{ public_path('images/logo.png') }}" alt="PetSpa Logo"> --}}
    <h2>🐾 PetSpa - Boleta de Pago</h2>
  </div>

  <div class="boleta-info">
    <p><strong>N° de Boleta:</strong> {{ $pago->series }}</p>
    <p><strong>Fecha:</strong> {{ $pago->fecha }}</p>
    <p><strong>Hora:</strong> {{ $pago->hora }}</p>
    <p><strong>Cliente:</strong> {{ $cliente->persona->nombres }} {{ $cliente->persona->apellidos }}</p>
    <p><strong>Correo:</strong> {{ $pago->usuario_creacion }}</p>
    <p><strong>Método de pago:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Servicio</th>
        <th>Precio (S/)</th>
        <th>IGV (18%)</th>
        <th>Total (S/)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($servicios as $det)
        <tr>
          <td>{{ $det->servicio->nombre_servicio ?? 'Servicio' }}</td>
          <td>{{ number_format($det->precio_unitario, 2) }}</td>
          <td>{{ number_format($det->igv, 2) }}</td>
          <td>{{ number_format($det->total, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3">Monto Pagado</th>
        <td>S/ {{ number_format($pago->monto, 2) }}</td>
      </tr>
    </tfoot>
  </table>

  <div class="footer">
    <p>Gracias por confiar en PetSpa 💙</p>
    <p>Dirección: Calle Principal 123, Ica - Perú</p>
    <p>Teléfono: (056) 123456</p>
  </div>
</body>
</html>
