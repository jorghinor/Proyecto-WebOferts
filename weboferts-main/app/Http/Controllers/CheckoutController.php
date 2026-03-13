<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Anuncio;
use App\Models\ProductMovement; // Importar Modelo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Charge;
use PDF;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cliente.nombre' => 'required|string',
            'cliente.email' => 'required|email',
            'stripeToken' => 'required_if:metodo_pago,tarjeta'
        ]);

        try {
            DB::beginTransaction();

            $cart = $request->input('cart');
            $cliente = $request->input('cliente');
            $metodoPago = $request->input('metodo_pago', 'efectivo');

            $total = 0;
            $detalles = [];
            $productosParaActualizar = [];

            // 1. VALIDACIÓN DE STOCK Y PREPARACIÓN
            foreach ($cart as $item) {
                $anuncio = Anuncio::with('producto')->find($item['id']);

                if (!$anuncio || !$anuncio->producto) {
                    throw new \Exception("El producto '{$item['titulo']}' ya no está disponible.");
                }

                $producto = $anuncio->producto;

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock}");
                }

                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'nombre_producto' => $producto->nombre,
                    'precio_unitario' => $item['precio'],
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal
                ];

                $productosParaActualizar[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad']
                ];
            }

            $estadoPedido = 'pendiente';

            // 2. PROCESAR PAGO
            if ($metodoPago === 'tarjeta') {
                Stripe::setApiKey(config('services.stripe.secret'));

                try {
                    $charge = Charge::create([
                        'amount' => $total * 100,
                        'currency' => 'bob',
                        'source' => $request->stripeToken,
                        'description' => 'Pedido WebOfertas de ' . $cliente['nombre'],
                        'receipt_email' => $cliente['email'],
                        'metadata' => ['cliente' => $cliente['nombre']]
                    ]);

                    if ($charge->status == 'succeeded') {
                        $estadoPedido = 'pagado';
                    } else {
                        throw new \Exception('El pago no pudo ser procesado.');
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Error en el pago: ' . $e->getMessage()], 400);
                }
            }

            // 3. CREAR PEDIDO
            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'nombre_cliente' => $cliente['nombre'],
                'email_cliente' => $cliente['email'],
                'telefono_cliente' => $cliente['telefono'] ?? null,
                'total' => $total,
                'estado' => $estadoPedido,
                'metodo_pago' => $metodoPago
            ]);

            foreach ($detalles as $detalle) {
                $pedido->detalles()->create($detalle);
            }

            // 4. DESCONTAR STOCK Y REGISTRAR MOVIMIENTO (KARDEX)
            foreach ($productosParaActualizar as $item) {
                $prod = $item['producto'];
                $qty = $item['cantidad'];

                // Restar stock
                $prod->decrement('stock', $qty);

                // Registrar movimiento
                ProductMovement::create([
                    'producto_id' => $prod->id,
                    'type' => 'sale',
                    'quantity' => -$qty, // Negativo porque es salida
                    'stock_balance' => $prod->stock, // Stock después de la resta
                    'reference' => 'Pedido #' . str_pad($pedido->id, 6, '0', STR_PAD_LEFT),
                    'user_id' => Auth::id() // El cliente que compró
                ]);
            }

            DB::commit();

            // 5. ENVIAR CORREO
            try {
                Mail::to($pedido->email_cliente)->send(new OrderPlaced($pedido->load('detalles')));
            } catch (\Exception $mailException) {
                Log::error('No se pudo enviar el correo de confirmación.', [
                    'error' => $mailException->getMessage(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'order_id' => $pedido->id,
                'redirect_url' => route('checkout.receipt', ['id' => $pedido->id])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function showReceipt($id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($id);
        return view('publico.receipt', compact('pedido'));
    }

    public function downloadPdf($id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($id);
        $pdf = PDF::loadView('pdf.invoice', compact('pedido'));
        return $pdf->download('factura-webofertas-'.$pedido->id.'.pdf');
    }
}
