@extends('template')

@section('section')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="font-weight-bold text-success">¡Pedido Realizado con Éxito!</h2>
                        <p class="text-muted">Gracias por tu compra. Aquí tienes tu recibo.</p>
                    </div>

                    <div class="border-bottom pb-3 mb-3">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="font-weight-bold">Orden #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</h5>
                                <p class="mb-0 text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-6 text-right">
                                <h5 class="font-weight-bold">Cliente</h5>
                                <p class="mb-0">{{ $pedido->nombre_cliente }}</p>
                                @if($pedido->telefono_cliente)
                                    <p class="mb-0 text-muted">{{ $pedido->telefono_cliente }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col" class="text-center">Cant.</th>
                                    <th scope="col" class="text-right">Precio</th>
                                    <th scope="col" class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->nombre_producto }}</td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-right">{{ number_format($detalle->precio_unitario, 2) }} Bs.</td>
                                    <td class="text-right font-weight-bold">{{ number_format($detalle->subtotal, 2) }} Bs.</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold pt-3">Total a Pagar:</td>
                                    <td class="text-right font-weight-bold pt-3 text-success h5">{{ number_format($pedido->total, 2) }} Bs.</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-5 text-center d-print-none">
                        <a href="{{ route('checkout.pdf', ['id' => $pedido->id]) }}" class="btn btn-primary btn-lg mr-2">
                            <i class="lni-printer"></i> Descargar PDF
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg">
                            Volver al Inicio
                        </a>
                    </div>

                    <div class="mt-4 text-center text-muted small d-none d-print-block">
                        <p>Este documento es un comprobante de pedido generado electrónicamente.</p>
                        <p>{{ config('app.name') }} - {{ url('/') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
