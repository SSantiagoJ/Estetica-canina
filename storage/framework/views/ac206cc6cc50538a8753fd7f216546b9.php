

<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pago.css')); ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container mt-4">
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de Mascotas</li>
            <li>Selección de Servicio</li>
            <li class="active">Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion text-center mb-4">Resumen y Pago</h2>

    <div class="row">
        <!-- Columna izquierda -->
        <div class="col-md-8">
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">👤 Datos del Cliente</h5>
                <p><strong>Nombre:</strong> <?php echo e(Auth::user()->persona->nombres); ?> <?php echo e(Auth::user()->persona->apellidos); ?></p>
                <p><strong>Correo:</strong> <?php echo e(Auth::user()->correo); ?></p>
            </div>

            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">🐾 Mascotas Seleccionadas</h5>
                <?php $__currentLoopData = $mascotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-2 mb-2 bg-light">
                        <p><strong>Nombre:</strong> <?php echo e($m->nombre); ?></p>
                        <p><strong>Especie:</strong> <?php echo e($m->especie); ?></p>
                        <p><strong>Raza:</strong> <?php echo e($m->raza); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="card shadow-sm p-3">
                <h5 class="mb-3">🧴 Servicios Seleccionados</h5>
                <div class="row">
                    <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="<?php echo e(asset('images/razas/'.$s->imagen_referencial)); ?>" class="card-img-top" alt="<?php echo e($s->nombre_servicio); ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo e($s->nombre_servicio); ?></h6>
                                    <p class="card-text">S/ <?php echo e(number_format($s->costo, 2)); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $adicionales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="<?php echo e(asset('images/razas/'.$a->imagen_referencial)); ?>" class="card-img-top" alt="<?php echo e($a->nombre_servicio); ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo e($a->nombre_servicio); ?></h6>
                                    <p class="card-text">S/ <?php echo e(number_format($a->costo, 2)); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <h5>💰 Total a Pagar</h5>
                <?php
                    $total = $servicios->sum('costo') + $adicionales->sum('costo');
                ?>
                <h3 class="text-success mt-3">S/ <?php echo e(number_format($total, 2)); ?></h3>
                <p class="text-muted">Monto total</p>

                <!-- 🟡 CONTENEDOR DONDE IRÁ EL BOTÓN -->
                <div id="paypal-button-container" class="mt-4"></div>

                <a href="<?php echo e(route('reservas.seleccionServicio')); ?>" class="btn btn-outline-secondary mt-3">⬅ Volver</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<!-- SDK de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=ARTxzEbR-GgKPnQdy64P9D3zeGlcj9zRJgCTy8ewKh3ZSyhr-lsh20yrYCfP2j-Jr8rAc9ysyLyRB3Xc&currency=USD"></script>

<script>
window.addEventListener('load', function() {
    if (typeof paypal === 'undefined') {
        console.error("⚠️ El SDK de PayPal no se cargó correctamente.");
        alert("El SDK de PayPal no se cargó. Revisa tu conexión o el Client ID.");
        return;
    }

    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'rect',
            label: 'paypal'
        },

        // ✅ Crea el pedido en PayPal
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    description: 'Pago PetSpa',
                    amount:{
                        currency_code:'USD',
                        value:"<?php echo e(number_format($total / 3.80, 2, '.', '')); ?>" // Convierte S/ a USD aprox.
                    }
                }]
            });
        },

        // ✅ Cuando el usuario aprueba el pago
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {

                // 1️⃣ Primero: guardar la reserva en tu BD llamando a finalizar()
                fetch("<?php echo e(route('reservas.finalizar')); ?>", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json", //    
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    },
                    body: JSON.stringify({ metodo_pago: "paypal" })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("❌ Ocurrió un error al guardar la reserva.");
                        return;
                    }

                    // 2️⃣ Segundo: redirigir a guardarPago()
                    alert('✅ Pago completado correctamente por ' + details.payer.name.given_name);

                    // Espera un instante para que el servidor termine de guardar todo
                    setTimeout(() => {
                        window.location.href = "<?php echo e(route('reservas.guardarPago')); ?>";
                    }, 800);
                })
                .catch(err => {
                    console.error("Error al crear la reserva:", err);
                    alert("❌ Ocurrió un error al crear la reserva en el servidor.");
                });
            });
        },

        // ⚠️ Manejo de errores y cancelaciones
        onCancel: function() {
            alert("⚠️ El pago fue cancelado por el usuario.");
        },
        onError: function(err) {
            console.error("Error en PayPal:", err);
            alert("❌ Ocurrió un error al procesar el pago.");
        }
    }).render('#paypal-button-container');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/reservas/pago.blade.php ENDPATH**/ ?>