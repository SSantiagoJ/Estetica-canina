<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recordatorio de Cita - PetSpa</title>
<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background-color: #f5f7fa;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 600px;
        background: #ffffff;
        margin: 40px auto;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .header {
        background: linear-gradient(135deg, #3b82f6, #06b6d4);
        color: white;
        text-align: center;
        padding: 25px 20px;
    }
    .header img {
        width: 70px;
        margin-bottom: 10px;
    }
    .header h1 {
        margin: 0;
        font-size: 24px;
        letter-spacing: 1px;
    }
    .content {
        padding: 30px 25px;
        text-align: center;
    }
    .content h2 {
        color: #1e293b;
        font-size: 22px;
        margin-bottom: 10px;
    }
    .content p {
        font-size: 16px;
        margin: 10px 0;
        line-height: 1.6;
    }
    .btn {
        display: inline-block;
        padding: 12px 25px;
        background-color: #3b82f6;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        margin-top: 20px;
        transition: background 0.3s ease;
    }
    .btn:hover {
        background-color: #2563eb;
    }
    .footer {
        background-color: #f1f5f9;
        text-align: center;
        padding: 15px;
        font-size: 13px;
        color: #64748b;
    }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="PetSpa Logo">
        <h1>PetSpa 🐾</h1>
    </div>
    <div class="content">
        <h2>¡Hola {{ $nombre }}!</h2>
        <p>Tu cita con tu mascota está programada para:</p>
        <p><strong>{{ $fecha }} a las {{ $hora }}</strong></p>
        <p>Te recomendamos llegar unos minutos antes para que tu mascota esté tranquila y lista para su atención 💙</p>
        <a href="http://localhost/bandeja-reservas" class="btn">Ver mi reserva</a>
    </div>
    <div class="footer">
        © {{ date('Y') }} PetSpa. Todos los derechos reservados.<br>
        Este es un mensaje automático, por favor no responder.
    </div>
</div>

</body>
</html>
