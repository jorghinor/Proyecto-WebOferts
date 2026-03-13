<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory');
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $page = $input['page'] ?? 0;
        $pages = $input['pages'] ?? 10;
        $texto = isset($input['texto']) ? trim($input['texto']) : '';

        $baseQuery = Producto::with('negocio');

        if ($texto !== '') {
            $baseQuery->where('nombre', 'like', '%' . $texto . '%');
        }

        $total = (clone $baseQuery)->count();

        $productos = $baseQuery
            ->orderBy('stock', 'asc') // Ordenar por stock más bajo primero (alerta)
            ->skip($page * $pages)
            ->take($pages)
            ->get();

        $last_page = ceil($total / $pages);

        return response()->json([
            'status' => 'ok',
            'result' => [
                'data' => $productos,
                'pagination' => [
                    'actual_page' => $page,
                    'total' => $total,
                    'last_page' => $last_page,
                    'pages' => $pages
                ]
            ]
        ], 200);
    }

    public function movements($id)
    {
        $movements = ProductMovement::where('producto_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(50) // Últimos 50 movimientos
            ->get();

        return response()->json(['status' => 'ok', 'data' => $movements]);
    }

    public function addStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($id);

            // Sumar stock
            $producto->increment('stock', $request->quantity);

            // Registrar movimiento
            ProductMovement::create([
                'producto_id' => $producto->id,
                'type' => 'restock',
                'quantity' => $request->quantity,
                'stock_balance' => $producto->stock,
                'reference' => $request->reason, // Ej: "Compra Proveedor X"
                'user_id' => Auth::id()
            ]);

            DB::commit();
            return response()->json(['status' => 'ok', 'message' => 'Stock actualizado correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadPdf()
    {
        $productos = Producto::with('negocio')->orderBy('nombre')->get();
        $pdf = PDF::loadView('pdf.inventory', compact('productos'));
        return $pdf->download('reporte-inventario-'.date('Y-m-d').'.pdf');
    }
}
