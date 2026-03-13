<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio;
use App\Models\Paquete;
use App\Models\User;
use App\Models\Anuncio;
use App\Models\AnuncioPaquete;
use App\Models\Categoria;
use App\Models\Foto;
use App\Models\Producto;
use App\Models\Review; // Importar Review
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Image;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('client.panel');
    }

    public function dashboardStats()
    {
        $user = auth()->user();
        $negocio = $user->negocio;

        if (!$negocio) {
            return response()->json(['error' => 'Negocio no encontrado'], 404);
        }

        $anunciosActivos = Anuncio::where('negocio_id', $negocio->id)->where('estadoanuncio', 'activo')->count();
        $totalProductos = Producto::where('negocio_id', $negocio->id)->count();

        $reviews = Review::where('negocio_id', $negocio->id);
        $calificacionPromedio = round($reviews->avg('rating'), 1);

        // Simulación de anuncios más vistos
        $anunciosMasVistos = Anuncio::where('negocio_id', $negocio->id)
            ->orderBy('id', 'desc') // Simulación simple
            ->take(5)
            ->get(['titulo', 'id'])
            ->map(function($anuncio) {
                $anuncio->vistas = rand(50, 500); // Vistas aleatorias
                return $anuncio;
            });

        $ultimasResenas = $reviews->with('user:id,name')->orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'anuncios_activos' => $anunciosActivos,
            'total_productos' => $totalProductos,
            'calificacion_promedio' => $calificacionPromedio,
            'anuncios_mas_vistos' => $anunciosMasVistos,
            'ultimas_resenas' => $ultimasResenas,
        ]);
    }

    public function create()
    {
        //
    }

    public function negocio()
    {
        return view('client.negocio');
    }

    public function minegocio(){

        $negocio = Negocio::where('user_id',auth()->id())->first();

        if ($negocio) {
            $negocio->categorias = $negocio->categorias()->get();
        } else {
            // Si no se encuentra un negocio, creamos un objeto dummy para evitar errores
            // y aseguramos que 'categorias' sea una colección vacía.
            $negocio = (object) [
                'id' => null, // O un valor por defecto si es necesario
                'nnegocio' => 'No asignado', // Nombre por defecto
                'ciudad' => 'cochabamba',
                'categorias' => collect() // Colección vacía para las categorías
            ];
        }

        $result = [
            'negocio'=> $negocio,
            'categorias'=> Categoria::where('cstate', 'active')->get()
        ];

        return response()->json($result, 200);
    }
    public function actualizar(Request $request, $id)
    {
        $normalizedId = is_numeric($id) ? (int) $id : null;
        $existingNegocio = Negocio::where('user_id', auth()->id())->first();
        $ignoreId = $normalizedId ?: optional($existingNegocio)->id;

        $validatedData = Validator::make($request->all(), [
            'nnegocio' => ['required', Rule::unique('negocios', 'nnegocio')->ignore($ignoreId)],
            'ciudad' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'cats' => 'required'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Editar Negocio Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $idAsString = strtolower(trim((string) $id));
        $shouldCreate = empty($idAsString) || in_array($idAsString, ['null', 'undefined'], true);

        if ($shouldCreate) {
            $negocio = Negocio::firstOrNew(['user_id' => auth()->id()]);
        } else {
            $negocio = Negocio::where('id', $normalizedId)
                ->where('user_id', auth()->id())
                ->first();
            if (!$negocio) {
                return response()->json(['errors' => array(['code' => 404, 'message' => 'No se encuentra objeto'])], 404);
            }
        }

        $negocio->nnegocio = $input['nnegocio'];
        $negocio->ciudad = $input['ciudad'] ?? ($negocio->ciudad ?? 'cochabamba');
        $negocio->nit = $input['nit'] ?? null;
        $negocio->dir = $input['dir'] ?? null;
        $negocio->gmaps = $input['gmaps'] ?? null;
        $negocio->latitude = ($input['latitude'] ?? '') !== '' ? (float) $input['latitude'] : null;
        $negocio->longitud = ($input['longitud'] ?? '') !== '' ? (float) $input['longitud'] : null;
        $negocio->telefonos = $input['telefonos'] ?? null;
        $negocio->celular = $input['celular'] ?? null;
        $negocio->delivery = $input['delivery'] ?? 0;
        $negocio->web = $this->toAscii(strtolower((string) ($input['web'] ?? $input['nnegocio'])));
        $negocio->estadonegocio = $negocio->estadonegocio ?? 'active';

        $negocio->save(); // <-- Guardar el negocio primero para obtener un ID

        // Ahora que el negocio tiene un ID, podemos sincronizar las categorías
        $cats = $input['cats'];
        if (!is_array($cats)) {
            $cats = array_values(array_filter(explode(',', (string) $cats)));
        }
        $negocio->categorias()->sync($cats);

        $response = [
            'success' => true,
            'data' => $negocio->web,
            'negocio_id' => $negocio->id,
            'message' => 'Negocio actualizado con exito.'
        ];

        return response()->json($response, 200);
    }
    function toAscii($str, $replace = array(), $delimiter = '-')
    {
        if (!empty($replace)) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }
    //AGREGADO
    public function anuncios()
    {
        return view('client.anuncios');
    }
    public function mipaquete(){

        $paquetes = Paquete::where('estado', '=', 'activo')->get();
        $usuario = User::find(auth()->id());
        $negocio = $usuario->negocio()->first();
        $negocio->categorias = $negocio->categorias()->get();
        $negocio->productos = Producto::where('negocio_id', '=', $negocio->id)->get();

        $anuncios = Anuncio::where('negocio_id','=', $negocio->id)->get();

        foreach ($anuncios as $anuncio) {
            $anuncio->fotos = Foto::where('anuncio_id', '=', $anuncio->id)->get();
            $anuncio->paquetes = DB::table('anuncio_paquete')
            ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
            ->where('anuncio_paquete.anuncio_id', $anuncio->id)
            ->select('paquetes.label as label', 'paquetes.color as color', 'anuncio_paquete.tipo', 'paquetes.titulo as titulo')
            ->get();
        }

        $result = [
            "data" => [
                        "paquetes" => $paquetes,
                        "negocio" => $negocio,
                        "anuncios" => $anuncios
                      ],
            "status" => "ok"
        ];

        return response()->json($result, 200);
    }
    //public function agregar(Request $request, $id)
    public function agregar(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            //'paquete_id' => 'required'. $id . ',id',
            'paquete_id' => 'required',
            'negocio_id' => 'required',
            'codigo' => 'required',
            'precio' => 'required',
            'tiempo' => 'required',
            'orden' => 'required',
            //'fecha_fin' => 'required',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Comprar Paquete Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();
        $anuncio = new Anuncio();

        $anuncio->precio = $input['precio'];
        $calcularfecha = date('Y-m-d', strtotime('+'.$input['tiempo'].' months'));
        $anuncio->fecha_fin = $calcularfecha;
        $anuncio->estadoanuncio = "activo";
        $anuncio->titulo = "";
        $anuncio->tipo = "";
        //$anuncio->precio = $input['precio'];;
        $anuncio->descripcion = "";
        $anuncio->codigo = $input['codigo'];
        $anuncio->orden = $input['orden'];
        $anuncio->negocio_id = $input['negocio_id'];
        $anuncio->paquete_id = $input['paquete_id'];

        $anuncio->save();
        $response = [
            'success' => true,
            'data' => $anuncio->id,
            'message' => 'Paquete comprado con exito.'
        ];

        return response()->json($response, 200);
    }

    public function actualizarImagen(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,png,jpg,gif|max:5000'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Actualiza Imagen Negocio Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $negocio = Negocio::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$negocio) {
            return response()->json(['errors' => array(['code' => 404, 'message' => 'No se encuentra objeto'])], 404);
        }

        if ($files = $request->file('imagen')) {
            $input = $request->all();

            $imageUpload = Image::make($files);
            $originalPath = 'storage/images/';
            $thumbnailPath = 'storage/images/thumbnail/';

            if (!is_dir($originalPath)) {
                mkdir($originalPath, 0755, true);
            }

            if (!is_dir($thumbnailPath)) {
                mkdir($thumbnailPath, 0755, true);
            }

            $timeName = time().'.'.$input['type'];

            $imageUpload->save($originalPath.$timeName);
            $url = url('/').Storage::url('images/'.$timeName);

            $imageUpload->resize(270,280);
            $imageUpload = $imageUpload->save($thumbnailPath.$timeName);

            $negocio->logo = $url;
            $negocio->save();

            $response = [
                'success' => true,
                'imageName'=> $url,
                'message' => 'Image upload successs.'
            ];

            return response()->json($response, 200);
        }
    }

    public function createAnuncio(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'titulo' => 'required',
            'descripcion' => 'required',
            'n_id' => 'required',
            'p_id' => 'required',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Nuevo Anuncio Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $anuncio = new Anuncio;
        $anuncio->titulo = $input['titulo'];
        $anuncio->descripcion = $input['descripcion'];
        $anuncio->estadoanuncio = "inactivo";
        $anuncio->codigo = time();
        $anuncio->negocio_id = $input['n_id'];
        $anuncio->producto_id = $input['p_id'];
        $anuncio->save();

        if($input['paquetes']){
            $paquetes = Paquete::find( $input['paquetes'] );

            foreach ($paquetes as $paquete) {
                $anuncioPaquete = new AnuncioPaquete;
                $anuncioPaquete->tipo = $paquete->tipopaquete;
                $anuncioPaquete->costo = $paquete->costo;
                $anuncioPaquete->orden = $paquete->orden;
                $anuncioPaquete->tiempo = $paquete->tiempo;
                $anuncioPaquete->label = $paquete->label;
                $anuncioPaquete->color = $paquete->color;
                $anuncioPaquete->estadocompra = 'inactivo';
                $calcularfecha = date('Y-m-d', strtotime('+'.$paquete->tiempo.' months'));
                $anuncioPaquete->fechafin = $calcularfecha;
                $anuncioPaquete->paquete_id = $paquete->id;
                $anuncioPaquete->anuncio_id = $anuncio->id;
                $anuncioPaquete->save();
            }
            //$anuncio->paquetes()->attach($paquetes);
        }

        $response = [
            'success' => true,
            'data' => $anuncio->id,
            'message' => 'Anuncio registrado con exito.'
        ];

        return response()->json($response, 200);
    }
    //PARA PAGINAR ANUNCIOS ADMIN-CLIENT
    public function clientAnuncios(Request $request){

        $input = $request->all();

        $anuncios = Anuncio::all()->skip($input['page']*$input['pages'])->take($input['pages']);
        foreach ($anuncios as $anuncio) {
            $anuncio->fotos = Foto::where('anuncio_id', '=', $anuncio->id)->get();
        }

        $result = array(
            'anuncios' => $anuncios,
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }
    //PARA PRODUCTOS
    public function productos()
    {
        return view('client.productos');
    }
    public function clientProductos(){
        $usuario = auth()->user();
        $negocio = $usuario ? $usuario->negocio : null;

        if (!$negocio) {
            return response()->json([
                'status' => 'ok',
                'result' => [
                    'productos' => [],
                    'negocio' => null,
                ],
            ], 200);
        }

        $productos = Producto::where('negocio_id', '=', $negocio->id)->get();

        foreach ($productos as $producto) {
            $producto->imagen = $this->resolveProductoImage($producto);
        }

        $result = array(
            'productos' => $productos,
            'negocio' => $negocio,
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }
    public function createProducto(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'nombre' => 'required',
            'n_id' => 'required|exists:negocios,id',
            'imagen' => 'nullable|string|max:2048',
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
        $negocio = auth()->user()->negocio;

        if (!$negocio || (int) $negocio->id !== (int) $input['n_id']) {
            return response()->json([
                'success' => false,
                'data' => 'Nuevo Producto Validation Error.',
                'message' => ['negocio' => ['No autorizado para registrar productos en ese negocio.']]
            ], 403);
        }

        $producto = new Producto();
        $producto->nombre = ucfirst(strtolower($input['nombre']));
        $producto->descripcion = $input['descripcion'] ?? null;
        $producto->precio_regular = $input['precio_regular'] ?? 0;
        $producto->precio_oferta = $input['precio_oferta'] ?? 0;
        $producto->estado_prod = "activo";
        $producto->tipoproducto = $input['tipoproducto'] ?? null;
        $producto->negocio_id = $negocio->id;

        if ($this->productosHasImagenColumn() && !empty($input['imagen'])) {
            $producto->imagen = $input['imagen'];
        }

        $producto->save();
        $producto->imagen = $this->resolveProductoImage($producto);

        $response = [
            'success' => true,
            'data' => $producto,
            'message' => 'Producto registrado con exito.'
        ];

        return response()->json($response, 200);
    }
    public function updateProducto(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'nombre' => 'required',
            'imagen' => 'nullable|string|max:2048',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Editar Producto Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }

        $negocio = auth()->user()->negocio;
        if (!$negocio || (int) $producto->negocio_id !== (int) $negocio->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para editar este producto.'
            ], 403);
        }

        //$producto->nombre = ucfirst(strtolower($input['nombre']));
        $producto->nombre = $input['nombre'];
        $producto->descripcion = $input['descripcion'] ?? null;
        $producto->precio_regular = $input['precio_regular'] ?? 0;
        $producto->precio_oferta = $input['precio_oferta'] ?? 0;
        $producto->tipoproducto = $input['tipoproducto'] ?? null;

        if ($this->productosHasImagenColumn() && !empty($input['imagen'])) {
            $producto->imagen = $input['imagen'];
        }

        if (!empty($input['state'])) {
            $producto->estado_prod = "activo";
        } else {
            $producto->estado_prod = "inactivo";
        }
        $producto->save();
        $producto->imagen = $this->resolveProductoImage($producto);

        $response = [
            'success' => true,
            'data' => $producto,
            'message' => 'Producto actualizado con exito.'
        ];

        return response()->json($response, 200);
    }

    private function productosHasImagenColumn()
    {
        return Schema::hasColumn('productos', 'imagen');
    }

    private function resolveProductoImage(Producto $producto)
    {
        if ($this->productosHasImagenColumn() && !empty($producto->getAttribute('imagen'))) {
            return $producto->getAttribute('imagen');
        }

        $anuncio = Anuncio::where('producto_id', $producto->id)->first();
        if (!$anuncio) {
            return null;
        }

        $foto = Foto::where('anuncio_id', $anuncio->id)->first();

        return $foto ? $foto->url : null;
    }

}
