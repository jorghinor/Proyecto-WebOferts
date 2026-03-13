<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .date { float: right; font-size: 12px; color: #555; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .low-stock { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <span class="date">Generado: {{ date('d/m/Y H:i') }}</span>
        <div class="title">Reporte de Inventario Actual</div>
        <div>WebOfertas Bolivia</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Negocio</th>
                <th class="text-right">Precio</th>
                <th class="text-center">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->id }}</td>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->negocio ? $producto->negocio->nnegocio : 'N/A' }}</td>
                <td class="text-right">{{ number_format($producto->precio_regular, 2) }} Bs.</td>
                <td class="text-center {{ $producto->stock <= 5 ? 'low-stock' : '' }}">
                    {{ $producto->stock }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px; color: #777;">
        <p>Total de productos listados: {{ count($productos) }}</p>
    </div>
</body>
</html>
