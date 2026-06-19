<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #2d2538;
        }

        .title {
            background: #c34acb;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
        }

        .subtitle {
            background: #f8e9f4;
            color: #574a6f;
            font-weight: 700;
        }

        .section {
            background: #efe7fb;
            color: #6d2db2;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #f4effb;
            color: #2d2538;
            font-weight: 700;
        }

        th,
        td {
            border: 1px solid #d8cde5;
            padding: 8px;
            vertical-align: middle;
        }

        .money {
            mso-number-format: "0.00";
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="11" class="title">Pet Grooming - Reporte de ingresos</td>
        </tr>
        <tr>
            <td colspan="11" class="subtitle">Periodo: {{ $inicioMes->format('d/m/Y') }} al {{ $finMes->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="6">Generado por: {{ $generadoPor }}</td>
            <td colspan="5">Fecha de generacion: {{ $fechaGeneracion }}</td>
        </tr>
        <tr><td colspan="11"></td></tr>
        <tr>
            <td class="section">Total del mes</td>
            <td class="money">{{ number_format($totalMes, 2, '.', '') }}</td>
            <td class="section">Pagos confirmados</td>
            <td class="center">{{ $cantidadPagos }}</td>
            <td class="section">Ticket promedio</td>
            <td class="money">{{ number_format($ticketPromedio, 2, '.', '') }}</td>
            <td colspan="5"></td>
        </tr>
        <tr><td colspan="11"></td></tr>
        <tr>
            <td colspan="11" class="section">Detalle de pagos confirmados</td>
        </tr>
        <tr>
            <th>Boleta</th>
            <th>Operacion</th>
            <th>Fecha pago</th>
            <th>Fecha reserva</th>
            <th>Hora reserva</th>
            <th>Cliente</th>
            <th>Mascota</th>
            <th>Empleado</th>
            <th>Metodo</th>
            <th>Estado pago</th>
            <th>Monto</th>
        </tr>
        @forelse($pagos as $pago)
            @php
                $reserva = $pago->reserva;
                $clientePersona = $reserva?->cliente?->persona;
                $empleadoPersona = $reserva?->empleado?->persona;
                $clienteNombre = $clientePersona ? trim(($clientePersona->nombres ?? '') . ' ' . ($clientePersona->apellidos ?? '')) : 'Cliente';
                $empleadoNombre = $empleadoPersona ? trim(($empleadoPersona->nombres ?? '') . ' ' . ($empleadoPersona->apellidos ?? '')) : 'Sin asignar';
                $metodoPago = $pago->metodo_pago === 'simulado'
                    ? 'Pago confirmado'
                    : ucfirst(str_replace('_', ' ', (string) $pago->metodo_pago));
                $estadoPago = $pago->estado_gateway ?: ($pago->estado === 'A' ? 'Aprobado' : 'Pagado');
            @endphp
            <tr>
                <td>{{ $pago->series ?? ('Pago #' . $pago->id_pago) }}</td>
                <td>{{ $pago->numero_operacion ?? ('PG-' . $pago->id_pago) }}</td>
                <td>{{ trim((string) $pago->fecha . ' ' . substr((string) $pago->hora, 0, 5)) }}</td>
                <td>{{ $reserva->fecha ?? '' }}</td>
                <td>{{ $reserva ? substr((string) $reserva->hora, 0, 5) : '' }}</td>
                <td>{{ $clienteNombre ?: 'Cliente' }}</td>
                <td>{{ $reserva->mascota->nombre ?? 'Mascota' }}</td>
                <td>{{ $empleadoNombre ?: 'Sin asignar' }}</td>
                <td>{{ $metodoPago }}</td>
                <td>{{ $estadoPago }}</td>
                <td class="money">{{ number_format((float) $pago->monto, 2, '.', '') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="center">No hay pagos confirmados en este periodo.</td>
            </tr>
        @endforelse
        <tr><td colspan="11"></td></tr>
        <tr>
            <td colspan="4" class="section">Resumen por empleado</td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <th colspan="2">Empleado</th>
            <th>Pagos</th>
            <th>Total</th>
            <th colspan="7"></th>
        </tr>
        @forelse($ingresosPorEmpleado as $item)
            <tr>
                <td colspan="2">{{ $item['empleado'] }}</td>
                <td class="center">{{ $item['pagos'] }}</td>
                <td class="money">{{ number_format((float) $item['total'], 2, '.', '') }}</td>
                <td colspan="7"></td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="center">Sin ingresos por empleado en este periodo.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
