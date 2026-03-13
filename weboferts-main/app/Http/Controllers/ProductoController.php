<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\User;
use App\Models\Anuncio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.productos');
    }
    public function adminProductos()
    {
        $productos = DB::table('productos as p')
            ->leftJoin('negocios as n', 'n.id', '=', 'p.negocio_id')
            ->select(
                'p.id',
                'p.nombre',
                'p.descripcion',
                'p.precio_regular',
                'p.precio_oferta',
                'p.estado_prod',
                'p.tipoproducto',
                'p.negocio_id',
                'p.created_at',
                'p.updated_at',
                DB::raw('n.nnegocio as negocio_nombre'),
                DB::raw('(SELECT COUNT(*) FROM anuncios a WHERE a.producto_id = p.id) as anuncios_count'),
                DB::raw('(SELECT f.url
                    FROM anuncios a
                    INNER JOIN fotos f ON f.anuncio_id = a.id
                    WHERE a.producto_id = p.id
                    ORDER BY a.id DESC, f.id DESC
                    LIMIT 1) as foto_url')
            )
            ->orderByDesc('p.id')
            ->get();

        return response()->json($productos, 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'nombre' => 'required|string|max:190|unique:productos,nombre,' . $id . ',id',
            'descripcion' => 'nullable|string',
            'precio_regular' => 'nullable|numeric',
            'precio_oferta' => 'nullable|numeric',
            'tipoproducto' => 'required|string|max:50',
            'estado_prod' => 'required|in:activo,inactivo',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'success' => false,
                'data' => 'Editar Producto Validation Error.',
                'message' => $validatedData->errors(),
            ], 422);
        }

        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $producto->nombre = $request->input('nombre');
        $producto->descripcion = $request->input('descripcion');
        $producto->precio_regular = $request->input('precio_regular');
        $producto->precio_oferta = $request->input('precio_oferta');
        $producto->tipoproducto = $request->input('tipoproducto');
        $producto->estado_prod = $request->input('estado_prod');
        $producto->save();

        return response()->json([
            'success' => true,
            'data' => $producto,
            'message' => 'Producto actualizado con exito.'
        ], 200);
    }

    public function changeState(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'state' => 'required|in:activo,inactivo',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'success' => false,
                'data' => 'Change State Validation Error.',
                'message' => $validatedData->errors()
            ], 422);
        }

        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $producto->estado_prod = $request->input('state');
        $producto->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $producto->id,
                'estado_prod' => $producto->estado_prod,
            ],
            'message' => 'Estado del producto actualizado.'
        ], 200);
    }
    //AGREGADO
    /*public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'nombre' => 'required|unique:productos,nombre',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Nuevo Producto Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $producto = new Producto();
        $producto->nombre = $input['nombre'];
        $producto->descripcion = $input['descripcion'];
        $producto->precio_regular = $input['precio_regular'];
        $producto->precio_oferta = $input['precio_oferta'];
        $producto->estado_prod = "activo";
        $producto->tipoproducto = $input['tipoproducto'];
        $producto->negocio_id = $input['n_id'];
        $producto->save();

        $response = [
            'success' => true,
            'data' => $producto,
            'message' => 'Producto registrado con exito.'
        ];

        return response()->json($response, 200);
    }
    public function update(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'titulo' => 'required|unique:paquetes,titulo,' . $id . ',id',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Editar Paquete Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $paquete = Paquete::find($id);
        $paquete->titulo = $input['titulo'];
        $paquete->descripcion = $input['descripcion'];
        $paquete->costo = $input['costo'];
        $paquete->tiempo = $input['tiempo'];
        if ($input['estado']) {
            $paquete->estado = "activo";
        } else {
            $paquete->estado = "inactivo";
        }
        $paquete->orden = $input['orden'];
        $paquete->tipopaquete = $input['tipopaquete'];
        $paquete->label = $input['label'];
        $paquete->color = $input['color'];
        $paquete->save();

        $response = [
            'success' => true,
            'data' => $paquete->web,
            'message' => 'Paquete actualizado con exito.'
        ];

        return response()->json($response, 200);
    }*/
}
