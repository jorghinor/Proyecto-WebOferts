<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .logo { max-width: 150px; }
        .company-info { float: right; text-align: right; }
        .invoice-details { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2>WebOfertas Bolivia</h2>
            <p>Cochabamba, Bolivia</p>
            <p>info@webofertas.com</p>
        </div>
        <h1>Factura</h1>
    </div>

    <div class="invoice-details">
        <p><strong>Nro Orden:</strong> #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Cliente:</strong> {{ $pedido->nombre_cliente }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->telefono_cliente }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                <td>{{ $detalle->nombre_producto }}</td>
                <td class="text-right">{{ number_format($detalle->precio_unitario, 2) }} Bs.</td>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td class="text-right">{{ number_format($detalle->subtotal, 2) }} Bs.</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right total">{{ number_format($pedido->total, 2) }} Bs.</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #777;">
        <p>Gracias por su compra.</p>
    </div>
</body>
</html>
