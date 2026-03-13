<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Negocio;
use App\Models\Categoria;
use App\Models\Anuncio;
use App\Models\Foto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\RegisterMail;
use App\Models\Producto;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('publico.home');
    }

    public function pageCategorias()
    {
        return view('publico.categorias');
    }
    //agregado apara anuncios
    public function pageAnuncios()
    {
        return view('publico.anuncios');
        //return '<h1>Anuncios</h1>';
    }


    public function registro()
    {
        return view('publico.registro');
    }

    public function categoriasActive()
    {
        $categorias = $this->getPublicCategoryCounts();
        return response()->json($categorias, 200);
    }

    public function categoriasAndNegocios(Request $request)
    {
        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $cuenta = $input['cuenta'];

        $categorias = $this->getPublicCategoryCounts();
        //$negocios1 = DB::select('SELECT negocios.id, negocios.nnegocio FROM negocios join users on negocios.user_id = users.id left join user_paquete on users.id = user_paquete.user_id where estadonegocio = "activo" order by user_paquete.orden desc');

        $total =Round(DB::table('negocios')
        ->where('negocios.estadonegocio','=','activo')
        ->count());

        //consulta para negocios con compras y sin compras de paquetes
        $negociosres = DB::table('negocios')
        ->join('users', 'users.id', '=', 'negocios.user_id')
        ->leftjoin('user_paquete', 'user_paquete.user_id', '=', 'users.id')
        ->where('negocios.estadonegocio','=','activo')
        ->skip($input['page'] * $input['pages'])->take($input['pages'])
        ->select('negocios.*')
        ->orderBy('user_paquete.orden', 'desc')
        ->get();
        foreach ($negociosres as $negocio) {
            $nego = Negocio::find($negocio->id);
            $negocio->categorias = $nego->categorias()->get();
            $negocio->cuenta = $cuenta;
            $cuenta++;
        }
        //fin consulta

        $rows = 0;
        $first_page = 0;
        $actual_page = $page;
        $next_page = null;
        $pre_page = null;

        $last_page = round($total/$pages);

        if($page>$last_page){
            $last_page = null;
        }

        if($page==0){
            $next_page = 1;
            $pre_page = null;
        }

        if($page>0&&$page<$total){
            $next_page = $page + 1;
            $pre_page = $page - 1;
        }

        if($page>=$last_page){
            $next_page = null;
        }

            $result = array(
                'categorias' => $categorias,
                'negocios' =>  $negociosres,
                'pagination' => array(
                    'first_page' => $first_page,
                    'actual_page' => $actual_page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'pre_page' => $pre_page,
                    'pages' => $pages,
                    'last_page' => $last_page,
                    'cuenta' => $cuenta
                )
            );

            return response()->json(['status'=>'ok','result'=>$result], 200);
    }

    public function negocios($param)
    {
        $categoria = Categoria::where('url', $param)->first();
        $negocios = $categoria->negocios()->get();

        $res = [
            "negocios" => $negocios,
            "categoria" => $categoria
        ];

        return response()->json($res, 200);
    }

    public function negociosCat($idcat)
    {
        $negocios = DB::table('negocios')
            ->join('categoria_negocio', 'categoria_negocio.negocio_id', '=', 'negocios.id')
            ->where('categoria_negocio.categoria_id', $idcat)
            ->select('negocios.*')->get();

        //$result = [];

        foreach ($negocios as $negocio) {
            $nego = Negocio::find($negocio->id);
            $negocio->categorias =  $nego->categorias()->get();
        }

        return response()->json($negocios, 200);
    }

    public function homeNegociosCategoria($param)
    {
        return view('publico.negocios');
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nombres' => 'required',
                'email' => 'required|email',
                'clave' => 'required',
                'clave_confirmation' => 'required|same:clave',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = new User();
        $user->name = $request->nombres;
        $user->email = $request->email;
        $user->rol = "client";
        $user->estadou = "inactivo";
            $hash = Hash::make(time());
            $user->remember_token = str_replace("/", "", $hash);
        $user->password = Hash::make($request->clave);
        $user->save();

/*         $negocio = new Negocio();
        $negocio->nnegocio = $user->email;
        $negocio->dir = '--';
        $negocio->delivery = 0;
        $negocio->estadonegocio = "inactivo";
        $negocio->compra = 0;
        $negocio->user_id = $user->id;
        $negocio->save(); */

        $correo = new RegisterMail;
        $correo->subject = "BIENVENIDO A WEB OFERTAS!";
        $correo->nombres = $request->nombres;
        $correo->email = $request->email;
            $correo->hash = $user->remember_token;

        Mail::to($request->email)->send($correo);

        return response()->json([
            'success' => true
        ], 200);
    }

    public function verify($hash)
    {
        $user = User::where( 'remember_token', '=', $hash)->first();
        if($user){
            $user->estadou = "activo";
            $user->remember_token = "";
            $user->save();

            $negocio = new Negocio();
            $negocio->nnegocio = $user->email;
            $negocio->ciudad = "cochabamba";
            $negocio->dir = "--";
            $negocio->delivery = 0;
            $negocio->estadonegocio = "inactivo";
            $negocio->compra = 0;
            $negocio->user_id = $user->id;
            $negocio->save();

            //return redirect('/login');
            return redirect()->route('login')->with('success','Felicidades, ahora puedes ingresar a tu cuenta!');

        }else{
            abort(404);
        }
    }
    //paginacion
    public function categoriasAndNegocios2(Request $request)
    {
        $categorias = $this->getPublicCategoryCounts();

        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $cuenta = $input['cuenta'];

        $total =Round(DB::table('negocios')->where('negocios.estadonegocio','=','activo')->count());
        $negocios = Negocio::all()->skip($input['page']*$input['pages'])->take($input['pages']);
        foreach ($negocios as $negocio) {
            $negocio->categorias = $negocio->categorias()->get();
            $negocio->cuenta = $cuenta;
            $cuenta++;
        }

        $first_page = 0;
        $actual_page = $page;
        $next_page = null;

        //$total = count($negocios);

        $pre_page = null;

        //$last_page = round($total/$pages);
        $last_page = round($total/$pages) + $total%$pages;
        if($page>$last_page){
            $last_page = null;
        }

        if($page==0){
            $next_page = 1;
            $pre_page = null;
        }

        if($page>0&&$page<$total){
            $next_page = $page + 1;
            $pre_page = $page - 1;
        }

        if($page>=$last_page){
            $next_page = null;
        }

        $result = array(
            'negocios' => $negocios,
            'categorias' => $categorias,
            'pagination' => array(
                'first_page' => $first_page,
                'actual_page' => $actual_page,
                'next_page' => $next_page,
                'total' => $total,
                'pre_page' => $pre_page,
                'pages' => $pages,
                'last_page' => $last_page,
                'cuenta' => $cuenta
            )
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }
    public function prueba()
    {
        //$hoy = date('Y-m-d');
        return date('Y-m-d', strtotime('+6 months'));
    }
    //para la paginacion de los anuncios
    public function negociosAndAnuncios(Request $request)
    {
        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $cuenta = $input['cuenta'];

        $categorias = $this->getPublicCategoryCounts();

        $total =Round(DB::table('anuncios')
        ->where('anuncios.estadoanuncio','=','activo')
        ->count());

        //consulta para anuncios con compras y sin compras de paquetes
        $anunciosres = DB::table('anuncios')
        ->join('negocios', 'negocios.id', '=', 'anuncios.negocio_id')
        ->where('anuncios.estadoanuncio','=','activo')
        ->skip($input['page'] * $input['pages'])->take($input['pages'])
        ->select('anuncios.*', 'negocios.nnegocio','negocios.logo')
        ->orderBy('anuncios.id', 'asc')
        ->get();
        foreach ($anunciosres as $row) {
            $nego = Negocio::find($row->negocio_id);
            $row->categorias = $nego->categorias()->get();
            $row->negocio = $nego;
            $row->cuenta = $cuenta;
            $row->fotos = Foto::where('anuncio_id', '=',  $row->id)->get();
            // FIX: Cargar producto para el carrito
            $row->producto = Producto::find($row->producto_id);
            $row->paquete = DB::table('anuncio_paquete')
                ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
                ->where('anuncio_paquete.anuncio_id', $row->id)
                ->where('anuncio_paquete.estadocompra', 'activo')
                ->whereIn('anuncio_paquete.tipo', ['esquinero', 'seccion'])
                ->orderByRaw("CASE WHEN anuncio_paquete.tipo = 'esquinero' THEN 0 ELSE 1 END")
                ->select('paquetes.label as label', 'paquetes.color as color', 'paquetes.titulo as titulo')
                ->first();

            $cuenta++;
        }
        //fin consulta

        $rows = 0;
        $first_page = 0;
        $actual_page = $page;
        $next_page = null;
        $pre_page = null;

        $last_page = round($total/$pages);

        if($page>$last_page){
            $last_page = null;
        }

        if($page==0){
            $next_page = 1;
            $pre_page = null;
        }

        if($page>0&&$page<$total){
            $next_page = $page + 1;
            $pre_page = $page - 1;
        }

        if($page>=$last_page){
            $next_page = null;
        }

            $result = array(
                'categorias' => $categorias,
                'anuncios' =>  $anunciosres,
                'pagination' => array(
                    'first_page' => $first_page,
                    'actual_page' => $actual_page,
                    'next_page' => $next_page,
                    'total' => $total,
                    'pre_page' => $pre_page,
                    'pages' => $pages,
                    'last_page' => $last_page,
                    'cuenta' => $cuenta
                )
            );

            return response()->json(['status'=>'ok','result'=>$result], 200);
    }

    public function recomendados()
    {
        $anuncios = $this->mapAnunciosPaquete($this->anunciosPorTipoPaquete('seccion', 10));

        return response()->json($anuncios, 200);
    }

    public function destacados()
    {
        $anuncios = $this->anunciosPorTipoPaquete('esquinero', 30);

        if ($anuncios->isEmpty()) {
            $anuncios = $this->anunciosPorTipoPaquete('seccion', 30);
        }

        $anuncios = $this->mapAnunciosPaquete($anuncios);

        return response()->json($anuncios, 200);
    }

    private function anunciosPorTipoPaquete(string $tipo, int $limit)
    {
        return Anuncio::query()
            ->join('anuncio_paquete', function ($join) use ($tipo) {
                $join->on('anuncios.id', '=', 'anuncio_paquete.anuncio_id')
                    ->where('anuncio_paquete.tipo', $tipo)
                    ->where('anuncio_paquete.estadocompra', 'activo');
            })
            ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
            ->where('anuncios.estadoanuncio', 'activo')
            ->with(['negocio', 'fotos', 'producto'])
            ->select(
                'anuncios.*',
                'paquetes.label as paquete_label',
                'paquetes.color as paquete_color',
                'paquetes.titulo as paquete_titulo'
            )
            ->orderBy('anuncio_paquete.id', 'desc')
            ->limit($limit)
            ->get();
    }

    private function mapAnunciosPaquete($anuncios)
    {
        return $anuncios->map(function ($anuncio) {
            $anuncio->paquete = (object) [
                'label' => $anuncio->paquete_label,
                'color' => $anuncio->paquete_color,
                'titulo' => $anuncio->paquete_titulo,
            ];

            unset($anuncio->paquete_label);
            unset($anuncio->paquete_color);
            unset($anuncio->paquete_titulo);

            return $anuncio;
        });
    }

    public function resultados()
    {
        return view('publico.resultados');
    }

    public function buscar(Request $request)
    {
        $input = $request->all();
        $texto = trim((string)($input['texto'] ?? ''));
        $ciudad = trim((string)($input['ciudad'] ?? ''));
        $categoria = trim((string)($input['categoria'] ?? ''));
        $page = max((int)($input['page'] ?? 0), 0);
        $pages = max((int)($input['pages'] ?? 5), 1);

        if ($texto === '' && $ciudad === '' && $categoria === '') {
            return response()->json([
                'status' => 'ok',
                'result' => [
                    'anuncios' => [],
                    'pagination' => [
                        'first_page' => 0,
                        'actual_page' => 0,
                        'next_page' => null,
                        'total' => 0,
                        'pre_page' => null,
                        'pages' => $pages,
                        'last_page' => 0,
                        'cuenta' => 1,
                    ],
                ],
            ], 200);
        }

        $baseQuery = DB::table('anuncios')
            ->join('negocios', 'negocios.id', '=', 'anuncios.negocio_id')
            ->leftJoin('productos', 'productos.id', '=', 'anuncios.producto_id')
            ->where('anuncios.estadoanuncio', 'activo');

        if ($texto !== '') {
            $baseQuery->where(function ($q) use ($texto) {
                $q->where('anuncios.titulo', 'LIKE', '%' . $texto . '%')
                    ->orWhere('productos.nombre', 'LIKE', '%' . $texto . '%')
                    ->orWhere('negocios.nnegocio', 'LIKE', '%' . $texto . '%');
            });
        }

        if ($ciudad !== '') {
            $baseQuery->where('negocios.ciudad', 'LIKE', '%' . $ciudad . '%');
        }

        if ($categoria !== '') {
            $baseQuery->whereExists(function ($sub) use ($categoria) {
                $sub->select(DB::raw(1))
                    ->from('categoria_negocio as cn')
                    ->join('categorias as c', 'c.id', '=', 'cn.categoria_id')
                    ->whereColumn('cn.negocio_id', 'anuncios.negocio_id')
                    ->where('c.cname', 'LIKE', '%' . $categoria . '%');
            });
        }

        $total = (clone $baseQuery)->distinct()->count('anuncios.id');
        $totalPages = $total > 0 ? (int) ceil($total / $pages) : 0;
        $lastPageIndex = $totalPages > 0 ? ($totalPages - 1) : 0;
        if ($page > $lastPageIndex) {
            $page = $lastPageIndex;
        }
        $cuenta = ($page * $pages) + 1;

        $anuncios = (clone $baseQuery)
            ->select('anuncios.id', 'anuncios.titulo', 'anuncios.negocio_id', 'anuncios.producto_id')
            ->distinct()
            ->orderByDesc('anuncios.id')
            ->skip($page * $pages)
            ->take($pages)
            ->get();

        foreach ($anuncios as $anuncio) {
            $negocio = Negocio::find($anuncio->negocio_id);
            $anuncio->negocio = $negocio;
            $anuncio->producto = Producto::find($anuncio->producto_id);
            $anuncio->categorias = $negocio->categorias()->get();
            $anuncio->fotos = Foto::where('anuncio_id', $anuncio->id)->get();
            $anuncio->paquete = DB::table('anuncio_paquete')
                ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
                ->where('anuncio_paquete.anuncio_id', $anuncio->id)
                ->where('anuncio_paquete.tipo', 'esquinero')
                ->where('anuncio_paquete.estadocompra', 'activo')
                ->select('paquetes.label as label', 'paquetes.color as color', 'paquetes.titulo as titulo')
                ->first();

            $anuncio->cuenta = $cuenta;
            $cuenta++;
        }

        $first_page = 0;
        $actual_page = $page;
        $next_page = ($totalPages > 0 && $page < $lastPageIndex) ? ($page + 1) : null;
        $pre_page = ($totalPages > 0 && $page > 0) ? ($page - 1) : null;
        $last_page = $totalPages;

        $result = array(
            'anuncios' =>  $anuncios,
            'pagination' => array(
                'first_page' => $first_page,
                'actual_page' => $actual_page,
                'next_page' => $next_page,
                'total' => $total,
                'pre_page' => $pre_page,
                'pages' => $pages,
                'last_page' => $last_page,
                'cuenta' => $cuenta
            )
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }

    public function detalle()
    {
        return view('publico.detalle');
    }

    public function detalleanuncio($id)
    {
        $anuncio = Anuncio::find($id);
        if (!$anuncio) {
            return response()->json(['errors' => array(['code' => 404, 'message' => 'No se encuentra objeto:'+$id])], 404);
        }
        $negocio = Negocio::find($anuncio->negocio_id);
        $anuncio->negocio = $negocio;
        $anuncio->producto = Producto::find($anuncio->producto_id);
        $anuncio->negocio = Negocio::find($anuncio->negocio_id);
        $anuncio->categorias = $negocio->categorias()->get();
        $anuncio->fotos = Foto::where('anuncio_id', $anuncio->id)->get();
        $anuncio->paquetes = DB::table('anuncio_paquete')
            ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
            ->where('anuncio_paquete.anuncio_id', $anuncio->id)
            ->select('paquetes.label as label', 'paquetes.color as color', 'anuncio_paquete.tipo', 'paquetes.titulo as titulo')
            ->get();

        return response()->json(['status'=>'ok', 'result'=>$anuncio], 200);
    }

    public function anunciosPorCategoria(Request $request, $category_url)
    {
        $categoria = Categoria::where('url', $category_url)->firstOrFail();

        // Get all active businesses associated with this category
        $negocioIds = DB::table('categoria_negocio')
                        ->where('categoria_id', $categoria->id)
                        ->pluck('negocio_id');

        // Get active ads from these businesses
        $anuncios = Anuncio::whereIn('negocio_id', $negocioIds)
                            ->where('estadoanuncio', 'activo')
                            ->with(['negocio', 'fotos', 'producto']) // FIX: Eager load producto
                            ->paginate(9); // Paginate 9 items per page

        // Manually load 'paquetes' for each anuncio as it's a custom DB query
        foreach ($anuncios as $anuncio) {
            $anuncio->paquete = DB::table('anuncio_paquete')
                ->join('paquetes', 'paquetes.id', '=', 'anuncio_paquete.paquete_id')
                ->where('anuncio_paquete.anuncio_id', $anuncio->id)
                ->where('anuncio_paquete.estadocompra', 'activo')
                ->where('anuncio_paquete.tipo', 'esquinero')
                ->select('paquetes.label as label', 'paquetes.color as color', 'paquetes.titulo as titulo')
                ->first();
        }

        return view('publico.anuncios_por_categoria', [
            'anuncios' => $anuncios,
            'categoria' => $categoria,
        ]);
    }

    private function getPublicCategoryCounts()
    {
        return DB::table('categorias as c')
            ->leftJoin('categoria_negocio as cn', 'cn.categoria_id', '=', 'c.id')
            ->leftJoin('negocios as n', function ($join) {
                $join->on('n.id', '=', 'cn.negocio_id')
                    ->where('n.estadonegocio', '=', 'activo');
            })
            ->leftJoin('anuncios as a', function ($join) {
                $join->on('a.negocio_id', '=', 'n.id')
                    ->where('a.estadoanuncio', '=', 'activo');
            })
            ->where('c.cstate', '=', 'active')
            ->groupBy('c.id', 'c.cname', 'c.icon', 'c.url')
            ->orderByDesc('cuantos')
            ->orderBy('c.cname')
            ->select(
                'c.id',
                'c.cname',
                'c.icon',
                'c.url',
                DB::raw('COUNT(DISTINCT a.id) as cuantos')
            )
            ->get();
    }
}
