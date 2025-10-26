<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prueba de Pago PayPal</title>
</head>
<body style="font-family: Arial; text-align:center; margin-top:50px;">
  <h2>Botón de prueba PayPal Sandbox</h2>
  <p>Monto de prueba: <strong>$10.00 USD</strong></p>

  <!-- SDK de PayPal -->
  <script src="https://www.paypal.com/sdk/js?client-id=ARTxzEbR-GgKPnQdy64P9D3zeGlcj9zRJgCTy8ewKh3ZSyhr-lsh20yrYCfP2j-Jr8rAc9ysyLyRB3Xc&currency=USD"></script>

  <!-- Contenedor del botón -->
  <div id="paypal-button-container" style="width: 300px; margin: 0 auto;"></div>

  <script>
    paypal.Buttons({
      style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'rect',
        label: 'paypal'
      },
      // Crear la orden (configurar el monto)
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            description: "Pago de prueba PetSpa",
            amount: {
              value: '10.00' // 💵 monto de prueba
            }
          }]
        });
      },
      // Capturar el pago cuando el cliente lo apruebe
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          alert('✅ Pago completado por: ' + details.payer.name.given_name);
          console.log('Detalles:', details);
        });
      },
      onError: function(err) {
        console.error('❌ Error en el pago:', err);
        alert('Ocurrió un error al procesar el pago.');
      }
    }).render('#paypal-button-container');
  </script>
</body>
</html>
