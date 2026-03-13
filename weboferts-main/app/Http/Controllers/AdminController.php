<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Negocio;
use App\Models\Foto;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Review; // Importar Review
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('admin.dashboard');
    }

    public function anuncios()
    {
        return view('admin.anuncios');
    }

    // --- GESTIÓN DE PEDIDOS ---

    public function pedidos()
    {
        return view('admin.pedidos');
    }

    public function allpedidos(Request $request)
    {
        $input = $request->all();
        $page = $input['page'] ?? 0;
        $pages = $input['pages'] ?? 10;
        $texto = isset($input['texto']) ? trim($input['texto']) : '';

        $baseQuery = Pedido::with('detalles');

        if ($texto !== '') {
            $baseQuery->where(function($q) use ($texto) {
                $q->where('nombre_cliente', 'like', '%' . $texto . '%')
                  ->orWhere('id', 'like', '%' . $texto . '%');
            });
        }

        $total = (clone $baseQuery)->count();

        $pedidos = $baseQuery
            ->orderByDesc('created_at')
            ->skip($page * $pages)
            ->take($pages)
            ->get();

        $last_page = ceil($total / $pages);

        return response()->json([
            'status' => 'ok',
            'result' => [
                'data' => $pedidos,
                'pagination' => [
                    'actual_page' => $page,
                    'total' => $total,
                    'last_page' => $last_page,
                    'pages' => $pages
                ]
            ]
        ], 200);
    }

    public function storePedido(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_cliente' => 'required|string',
            'total' => 'required|numeric',
            'estado' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pedido = Pedido::create([
            'nombre_cliente' => $request->nombre_cliente,
            'telefono_cliente' => $request->telefono_cliente,
            'total' => $request->total,
            'estado' => $request->estado,
            'metodo_pago' => 'manual'
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Pedido creado correctamente', 'data' => $pedido]);
    }

    public function updatePedido(Request $request, $id)
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return response()->json(['message' => 'Pedido no encontrado'], 404);

        $pedido->estado = $request->estado;
        if($request->has('nombre_cliente')) $pedido->nombre_cliente = $request->nombre_cliente;
        if($request->has('telefono_cliente')) $pedido->telefono_cliente = $request->telefono_cliente;
        if($request->has('total')) $pedido->total = $request->total;

        $pedido->save();

        return response()->json(['status' => 'ok', 'message' => 'Pedido actualizado correctamente']);
    }

    public function deletePedido($id)
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return response()->json(['message' => 'Pedido no encontrado'], 404);

        $pedido->delete();
        return response()->json(['status' => 'ok', 'message' => 'Pedido eliminado correctamente']);
    }

    // --- GESTIÓN DE RESEÑAS ---

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function allReviews(Request $request)
    {
        $input = $request->all();
        $page = $input['page'] ?? 0;
        $pages = $input['pages'] ?? 10;
        $texto = isset($input['texto']) ? trim($input['texto']) : '';

        // Eager load negocio y user para mostrar nombres
        $baseQuery = Review::with(['negocio', 'user']);

        if ($texto !== '') {
            $baseQuery->where(function($q) use ($texto) {
                $q->where('comment', 'like', '%' . $texto . '%')
                  ->orWhere('reviewer_name', 'like', '%' . $texto . '%')
                  ->orWhereHas('negocio', function($sq) use ($texto) {
                      $sq->where('nnegocio', 'like', '%' . $texto . '%');
                  });
            });
        }

        $total = (clone $baseQuery)->count();

        $reviews = $baseQuery
            ->orderByDesc('created_at')
            ->skip($page * $pages)
            ->take($pages)
            ->get();

        $last_page = ceil($total / $pages);

        return response()->json([
            'status' => 'ok',
            'result' => [
                'data' => $reviews,
                'pagination' => [
                    'actual_page' => $page,
                    'total' => $total,
                    'last_page' => $last_page,
                    'pages' => $pages
                ]
            ]
        ], 200);
    }

    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'negocio_id' => 'required|exists:negocios,id',
            'reviewer_name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review = Review::create([
            'negocio_id' => $request->negocio_id,
            'reviewer_name' => $request->reviewer_name,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'user_id' => null // Creado por admin manualmente
        ]);

        // Recargar relaciones para devolver el objeto completo
        $review->load(['negocio', 'user']);

        return response()->json(['status' => 'ok', 'message' => 'Reseña creada correctamente', 'data' => $review]);
    }

    public function updateReview(Request $request, $id)
    {
        $review = Review::find($id);
        if (!$review) return response()->json(['message' => 'Reseña no encontrada'], 404);

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'reviewer_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->rating = $request->rating;
        $review->comment = $request->comment;
        if ($request->has('reviewer_name') && !$review->user_id) {
            $review->reviewer_name = $request->reviewer_name;
        }
        $review->save();

        return response()->json(['status' => 'ok', 'message' => 'Reseña actualizada correctamente']);
    }

    public function deleteReview($id)
    {
        $review = Review::find($id);
        if (!$review) return response()->json(['message' => 'Reseña no encontrada'], 404);

        $review->delete();
        return response()->json(['status' => 'ok', 'message' => 'Reseña eliminada correctamente']);
    }

    // --- GESTIÓN DE ANUNCIOS (EXISTENTE) ---

    public function allanuncios(Request $request)
    {
        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $texto = isset($input['texto']) ? trim($input['texto']) : '';
        $estado = isset($input['estado']) ? trim($input['estado']) : 'todos';

        $baseQuery = Anuncio::with('negocio', 'fotos', 'producto');
        if ($texto !== '') {
            $baseQuery->where('titulo', 'like', '%' . $texto . '%');
        }
        if ($estado === 'activo' || $estado === 'inactivo') {
            $baseQuery->where('estadoanuncio', $estado);
        }

        $total = (clone $baseQuery)->count();

        $anuncios = $baseQuery
            ->orderByDesc('id')
            ->skip($page * $pages)
            ->take($pages)
            ->get();

        $cuenta = ($page * $pages) + 1;
        foreach ($anuncios as $anuncio) {
            $anuncio->paquetes = DB::table('anuncio_paquete')
                ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
                ->where('anuncio_paquete.anuncio_id', $anuncio->id)
                ->where('anuncio_paquete.estadocompra', 'activo')
                ->where('anuncio_paquete.tipo', 'esquinero')
                ->select('paquetes.label as label', 'paquetes.color as color', 'anuncio_paquete.tipo', 'paquetes.titulo as titulo')
                ->get();
            $anuncio->paquetes_activos = DB::table('anuncio_paquete')
                ->where('anuncio_paquete.anuncio_id', $anuncio->id)
                ->where('anuncio_paquete.estadocompra', 'activo')
                ->pluck('paquete_id')
                ->values();
            $anuncio->cuenta = $cuenta++;
        }

        $last_page = ceil($total / $pages);

        $result = array(
            'data' => $anuncios,
            'pagination' => array(
                'first_page' => 0,
                'actual_page' => $page,
                'next_page' => ($page + 1) < $last_page ? $page + 1 : null,
                'total' => $total,
                'pre_page' => $page > 0 ? $page - 1 : null,
                'pages' => $pages,
                'last_page' => $last_page,
            ),
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }

    public function changestate(Request $request, $anuncio_id)
    {
        $anuncio = Anuncio::find($anuncio_id);
        if (!$anuncio) {
            return response()->json(['errors' => array(['code' => 404, 'message' => 'No se encuentra objeto'])], 404);
        }

        $validatedData = Validator::make($request->all(), [
            'negocio' => 'required',
            'state' => 'required'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Change State Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();
        if($input['state']){
            $anuncio->estadoanuncio = 'activo';
        }else{
            $anuncio->estadoanuncio = 'inactivo';
        }
        $anuncio->save();

        return response()->json(['status'=>'ok'], 200);
    }

    public function dashboardStats(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $hasDateFilter = !empty($dateFrom) && !empty($dateTo);

        $applyRange = function ($query, $column) use ($hasDateFilter, $dateFrom, $dateTo) {
            if ($hasDateFilter) {
                $query->whereDate($column, '>=', $dateFrom)
                    ->whereDate($column, '<=', $dateTo);
            }
            return $query;
        };

        // --- ESTADÍSTICAS DE VENTAS (CARRITO) ---

        // Total vendido en dinero
        $ventasTotales = DB::table('pedidos')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'created_at');
            })
            ->sum('total');

        // Cantidad de pedidos
        $cantidadPedidos = DB::table('pedidos')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'created_at');
            })
            ->count();

        // Top productos vendidos (Real, desde detalle_pedidos)
        $topProductosVendidosReal = DB::table('detalle_pedidos as dp')
            ->join('pedidos as p', 'p.id', '=', 'dp.pedido_id')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'p.created_at');
            })
            ->select(
                'dp.nombre_producto as producto',
                DB::raw('SUM(dp.cantidad) as cantidad_vendida'),
                DB::raw('SUM(dp.subtotal) as total_generado')
            )
            ->groupBy('dp.nombre_producto')
            ->orderByDesc('cantidad_vendida')
            ->limit(5)
            ->get();

        // Ventas Diarias (Para gráfica 3D)
        $ventasDiarias = DB::table('pedidos')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'created_at');
            })
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total_venta')
            )
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->limit(7) // Últimos 7 días con ventas
            ->get();


        // --- ESTADÍSTICAS DE PAQUETES (ANTERIORES) ---

        // Negocios "rentables" = negocios con inversion en paquetes activos de anuncios.
        $negociosRentables = DB::table('anuncio_paquete as ap')
            ->join('anuncios as a', 'a.id', '=', 'ap.anuncio_id')
            ->where('ap.estadocompra', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'ap.created_at');
            })
            ->distinct('a.negocio_id')
            ->count('a.negocio_id');

        // Productos "mas vendidos/promocionados" (proxy real disponible) = productos con anuncios activos.
        $productosPromocionados = DB::table('anuncios')
            ->where('estadoanuncio', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'created_at');
            })
            ->distinct('producto_id')
            ->count('producto_id');

        // Anuncios destacados = anuncios con paquete tipo esquinero activo.
        $anunciosDestacados = DB::table('anuncio_paquete')
            ->where('tipo', 'esquinero')
            ->where('estadocompra', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'created_at');
            })
            ->distinct('anuncio_id')
            ->count('anuncio_id');

        $topNegociosRentables = DB::table('anuncio_paquete as ap')
            ->join('anuncios as a', 'a.id', '=', 'ap.anuncio_id')
            ->join('negocios as n', 'n.id', '=', 'a.negocio_id')
            ->where('ap.estadocompra', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'ap.created_at');
            })
            ->select(
                'n.id',
                'n.nnegocio',
                DB::raw('COUNT(DISTINCT a.id) as anuncios_activos'),
                DB::raw('ROUND(SUM(ap.costo), 2) as inversion_total')
            )
            ->groupBy('n.id', 'n.nnegocio')
            ->orderByDesc('inversion_total')
            ->limit(5)
            ->get();

        $topProductosPromocionados = DB::table('anuncios as a')
            ->join('productos as p', 'p.id', '=', 'a.producto_id')
            ->join('negocios as n', 'n.id', '=', 'a.negocio_id')
            ->where('a.estadoanuncio', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'a.created_at');
            })
            ->select(
                'p.id',
                DB::raw('p.nombre as producto'),
                DB::raw('n.nnegocio as negocio'),
                DB::raw('COUNT(a.id) as anuncios_activos')
            )
            ->groupBy('p.id', 'p.nombre', 'n.nnegocio')
            ->orderByDesc('anuncios_activos')
            ->limit(5)
            ->get();

        $topAnunciosDestacados = DB::table('anuncios as a')
            ->join('negocios as n', 'n.id', '=', 'a.negocio_id')
            ->join('anuncio_paquete as ap', function ($join) {
                $join->on('ap.anuncio_id', '=', 'a.id')
                    ->where('ap.tipo', 'esquinero')
                    ->where('ap.estadocompra', 'activo');
            })
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'ap.created_at');
            })
            ->select(
                'a.id',
                'a.titulo',
                DB::raw('n.nnegocio as negocio'),
                DB::raw('ROUND(SUM(ap.costo), 2) as inversion_destacado')
            )
            ->groupBy('a.id', 'a.titulo', 'n.nnegocio')
            ->orderByDesc('inversion_destacado')
            ->limit(5)
            ->get();

        // Top 5 productos "mas vendidos" (estimado real disponible)
        // Basado en cantidad de paquetes activos comprados para anuncios de cada producto.
        $topProductosVendidos = DB::table('anuncio_paquete as ap')
            ->join('anuncios as a', 'a.id', '=', 'ap.anuncio_id')
            ->join('productos as p', 'p.id', '=', 'a.producto_id')
            ->join('negocios as n', 'n.id', '=', 'a.negocio_id')
            ->where('ap.estadocompra', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'ap.created_at');
            })
            ->select(
                'p.id',
                DB::raw('p.nombre as producto'),
                DB::raw('n.nnegocio as negocio'),
                DB::raw('COUNT(ap.id) as ventas_estimadas'),
                DB::raw('ROUND(SUM(ap.costo), 2) as inversion_total')
            )
            ->groupBy('p.id', 'p.nombre', 'n.nnegocio')
            ->orderByDesc('ventas_estimadas')
            ->orderByDesc('inversion_total')
            ->limit(5)
            ->get();

        // Top 5 anuncios mas rentables por inversion total en paquetes activos.
        $topAnunciosRentables = DB::table('anuncio_paquete as ap')
            ->join('anuncios as a', 'a.id', '=', 'ap.anuncio_id')
            ->join('negocios as n', 'n.id', '=', 'a.negocio_id')
            ->where('ap.estadocompra', 'activo')
            ->when($hasDateFilter, function ($q) use ($applyRange) {
                $applyRange($q, 'ap.created_at');
            })
            ->select(
                'a.id',
                'a.titulo',
                DB::raw('n.nnegocio as negocio'),
                DB::raw('ROUND(SUM(ap.costo), 2) as rentabilidad_total')
            )
            ->groupBy('a.id', 'a.titulo', 'n.nnegocio')
            ->orderByDesc('rentabilidad_total')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                // Nuevas métricas de ventas
                'ventas_totales' => $ventasTotales,
                'cantidad_pedidos' => $cantidadPedidos,
                'top_productos_vendidos_real' => $topProductosVendidosReal,
                'ventas_diarias' => $ventasDiarias, // Nueva métrica para gráfica

                // Métricas anteriores
                'negocios' => $negociosRentables,
                'anuncios' => $anunciosDestacados,
                'productos' => $productosPromocionados,
                'total' => $negociosRentables + $anunciosDestacados + $productosPromocionados,
                'labels' => [
                    'negocios' => 'Negocios rentables',
                    'anuncios' => 'Anuncios destacados',
                    'productos' => 'Productos promocionados',
                ],
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
                'top_negocios_rentables' => $topNegociosRentables,
                'top_productos_promocionados' => $topProductosPromocionados,
                'top_anuncios_destacados' => $topAnunciosDestacados,
                'top_productos_vendidos' => $topProductosVendidos,
                'top_anuncios_rentables' => $topAnunciosRentables,
            ],
        ], 200);
    }
}
