<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Gracias por tu compra!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $pedido->nombre_cliente }}</strong>,</p>
            <p>Hemos recibido tu pedido correctamente. A continuación encontrarás los detalles:</p>

            <p><strong>Orden:</strong> #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Total:</strong> {{ number_format($pedido->total, 2) }} Bs.</p>

            <p>Adjunto a este correo encontrarás tu factura en formato PDF.</p>

            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/') }}" class="button">Volver a la tienda</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
