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
      <h2>{{ $promo['nombre_promocion'] }}</h2>
      <p>{{ $promo['descripcion'] }}</p>

      @if(!empty($promo['imagen_ref']))
      <div class="promo-banner">
        <img src="{{ $promo['imagen_ref'] }}" alt="Promoción">
      </div>
      @endif

      <p>
        Vigencia: <strong>{{ $promo['fecha_inicio'] }}</strong> a <strong>{{ $promo['fecha_fin'] }}</strong>
        @if(!empty($promo['descuento'])), Descuento: <strong>{{ rtrim(rtrim(number_format($promo['descuento'],2), '0'), '.') }}%</strong>@endif
      </p>

      <h3>Servicios incluidos</h3>

      @forelse($servicios as $s)
        <div class="card">
          @if(!empty($s['imagen_referencial']))
            <img src="{{ $s['imagen_referencial'] }}" alt="{{ $s['nombre_servicio'] }}">
          @endif
          <div class="srv-info">
            <p class="srv-title">{{ $s['nombre_servicio'] }}</p>
            <p class="price">S/ {{ number_format($s['costo'],2) }}</p>
          </div>
        </div>
      @empty
        <p>Esta promoción no tiene servicios asociados aún.</p>
      @endforelse

      <a class="btn" href="{{ url('http://127.0.0.1:8000/dashboard') }}">Reservar</a>
    </div>

    <div class="foot">
      © {{ date('Y') }} PetSpa. Este es un mensaje automático, por favor no responder.
    </div>
  </div>
</body>
</html>
