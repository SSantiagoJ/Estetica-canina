<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Promoción - PetSpa</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fa;color:#333;margin:0}
  .wrap{max-width:680px;background:#fff;margin:32px auto;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.08);overflow:hidden}
  .head{background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;text-align:center;padding:28px 20px}
  .head img{width:70px;margin-bottom:8px}
  .head h1{margin:0;font-size:22px}
  .content{padding:26px 22px}
  .pill{display:inline-block;background:#e0f2fe;color:#075985;border-radius:999px;padding:6px 12px;font-size:12px;margin-bottom:10px}
  h2{margin:8px 0 14px 0;color:#0f172a}
  p{line-height:1.55;margin:10px 0}
  .card{display:flex;gap:14px;align-items:center;background:#fafafa;border:1px solid #eef2f7;border-radius:12px;padding:12px;margin:10px 0}
  .card img{width:64px;height:64px;object-fit:cover;border-radius:10px;background:#fff;border:1px solid #e5e7eb}
  .srv-info{flex:1}
  .srv-title{margin:0;font-weight:700}
  .price{font-weight:700}
  .btn{display:inline-block;margin-top:16px;background:#0ea5e9;color:#fff;text-decoration:none;padding:12px 20px;border-radius:10px;font-weight:700}
  .btn:hover{background:#0284c7}
  .promo-banner{border-radius:12px;overflow:hidden;margin:12px 0;border:1px solid #e5e7eb}
  .promo-banner img{width:100%;display:block}
  .foot{background:#f1f5f9;text-align:center;color:#64748b;font-size:12px;padding:14px}
</style>
</head>
<body>
  <div class="wrap">
    <div class="head">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="PetSpa">
      <h1>PetSpa 🐾</h1>
    </div>

    <div class="content">
      <span class="pill">Nueva promoción</span>
      <h2><?php echo e($promo['nombre_promocion']); ?></h2>
      <p><?php echo e($promo['descripcion']); ?></p>

      <?php if(!empty($promo['imagen_ref'])): ?>
      <div class="promo-banner">
        <img src="<?php echo e($promo['imagen_ref']); ?>" alt="Promoción">
      </div>
      <?php endif; ?>

      <p>
        Vigencia: <strong><?php echo e($promo['fecha_inicio']); ?></strong> a <strong><?php echo e($promo['fecha_fin']); ?></strong>
        <?php if(!empty($promo['descuento'])): ?>, Descuento: <strong><?php echo e(rtrim(rtrim(number_format($promo['descuento'],2), '0'), '.')); ?>%</strong><?php endif; ?>
      </p>

      <h3>Servicios incluidos</h3>

      <?php $__empty_1 = true; $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card">
          <?php if(!empty($s['imagen_referencial'])): ?>
            <img src="<?php echo e($s['imagen_referencial']); ?>" alt="<?php echo e($s['nombre_servicio']); ?>">
          <?php endif; ?>
          <div class="srv-info">
            <p class="srv-title"><?php echo e($s['nombre_servicio']); ?></p>
            <p class="price">S/ <?php echo e(number_format($s['costo'],2)); ?></p>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>Esta promoción no tiene servicios asociados aún.</p>
      <?php endif; ?>

      <a class="btn" href="<?php echo e(url('/promociones')); ?>">Ver promoción y reservar</a>
    </div>

    <div class="foot">
      © <?php echo e(date('Y')); ?> PetSpa. Este es un mensaje automático, por favor no responder.
    </div>
  </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/emails/promocion_nueva.blade.php ENDPATH**/ ?>