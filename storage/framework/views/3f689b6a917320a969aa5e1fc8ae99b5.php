<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Pago</title>
    <link rel="stylesheet" href="<?php echo e(public_path('css/boleta.css')); ?>" type="text/css">
</head>
<body>
  <div class="boleta-header">
    
    
    <h2>🐾 PetSpa - Boleta de Pago</h2>
  </div>

  <div class="boleta-info">
    <p><strong>N° de Boleta:</strong> <?php echo e($pago->series); ?></p>
    <p><strong>Fecha:</strong> <?php echo e($pago->fecha); ?></p>
    <p><strong>Hora:</strong> <?php echo e($pago->hora); ?></p>
    <p><strong>Cliente:</strong> <?php echo e($cliente->persona->nombres); ?> <?php echo e($cliente->persona->apellidos); ?></p>
    <p><strong>Correo:</strong> <?php echo e($pago->usuario_creacion); ?></p>
    <p><strong>Método de pago:</strong> <?php echo e(ucfirst($pago->metodo_pago)); ?></p>
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
      <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $det): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($det->servicio->nombre_servicio ?? 'Servicio'); ?></td>
          <td><?php echo e(number_format($det->precio_unitario, 2)); ?></td>
          <td><?php echo e(number_format($det->igv, 2)); ?></td>
          <td><?php echo e(number_format($det->total, 2)); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3">Monto Pagado</th>
        <td>S/ <?php echo e(number_format($pago->monto, 2)); ?></td>
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
<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/reservas/boleta.blade.php ENDPATH**/ ?>