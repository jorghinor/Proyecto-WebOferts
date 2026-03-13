<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class NegocioController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.negocios');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'nnegocio' => 'required|unique:negocios,nnegocio',
            'email' => 'required',
            'dir' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'clave' => 'min:6|required_with:clave_confirmation|same:clave_confirmation',
            'clave_confirmation' => 'min:6',
            'cats'=> 'required'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Nuevo Negocio Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $user = new User;
        $user->email = $input['email'];
        $user->name = $input['nnegocio'];
        $user->password = Hash::make($input['clave']);
        $user->remember_token = Hash::make(time());
        $user->rol = "client";
        $user->estadou = "activo";
        $user->save();


        $negocio = new Negocio();
        $negocio->nnegocio = $input['nnegocio'];
        $negocio->ciudad = 'cochabamba';
        $negocio->user_id = $user->id;
        $negocio->nit = $input['nit'];
        //$negocio->cstate = "active";
        //AGREGADO
        $negocio->dir = $input['dir'];
        $negocio->gmaps = $input['gmaps'];
        $negocio->latitude = ($input['latitude'] ?? '') !== '' ? (float) $input['latitude'] : null;
        $negocio->longitud = ($input['longitud'] ?? '') !== '' ? (float) $input['longitud'] : null;
        $negocio->telefonos = $input['telefonos'];
        $negocio->celular = $input['celular'];
        $negocio->estadonegocio = 'activo';


        if($input['delivery']){
            $negocio->delivery = 1;
        }else {
            $negocio->delivery = 0;
        }

        $negocio->web = $input['web'];
        if ($input['logo'] == '' or $input['logo'] == null) {
            $negocio->logo = "default";
        } else {
            $negocio->logo = $input['logo'];
        }
        $negocio->save();

        $negocio->categorias()->attach($input['cats']);

        $negocio->categorias = $negocio->categorias()->get();

        $response = [
            'success' => true,
            'data' => $negocio,
            'message' => 'Negocio registrado con exito.'
        ];

        return response()->json($response, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'nnegocio' => 'required|unique:negocios,nnegocio,' . $id . ',id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
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

        $negocio = Negocio::find($id);
        $negocio->nnegocio = $input['nnegocio'];
        $negocio->nit = $input['nit'];
        $negocio->dir = $input['dir'];
        $negocio->gmaps = $input['gmaps'];
        $negocio->latitude = ($input['latitude'] ?? '') !== '' ? (float) $input['latitude'] : null;
        $negocio->longitud = ($input['longitud'] ?? '') !== '' ? (float) $input['longitud'] : null;
        $negocio->telefonos = $input['telefonos'];
        $negocio->celular = $input['celular'];
        $negocio->delivery = $input['delivery'];
        $negocio->web =  $this->toAscii( strtolower($input['web']));

        // --- START OF FIX ---
        if (isset($input['logo']) && $input['logo'] != null) {
            $negocio->logo = $input['logo'];
        }
        $negocio->save();

        if (isset($input['cats'])) {
            $negocio->categorias()->sync($input['cats']);
        }
        // --- END OF FIX ---

        $response = [
            'success' => true,
            'data' => $negocio->web,
            'message' => 'Negocio actualizado con exito.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
                'message' => $validatedData->errors(),
            ], 422);
        }

        $negocio = Negocio::find($id);
        if (!$negocio) {
            return response()->json([
                'success' => false,
                'message' => 'Negocio no encontrado.',
            ], 404);
        }

        $negocio->estadonegocio = $request->input('state');
        $negocio->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $negocio->id,
                'estadonegocio' => $negocio->estadonegocio,
            ],
            'message' => 'Estado del negocio actualizado.',
        ], 200);
    }

    public function negocios($param)
    {
        return view('publico.negocios');
    }
    public function adminNegocios()
     {
        $negocios = Negocio::with('productos')->get();
        foreach ($negocios as $negocio) {
            $negocio->categorias = $negocio->categorias()->get();
        }

         //$negocios = Negocio::all();
         return response()->json($negocios, 200);
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

    public function adminNegocios2(Request $request){

        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $texto = isset($input['texto']) ? trim($input['texto']) : '';
        $estado = isset($input['estado']) ? trim($input['estado']) : 'todos';
        $cuenta = ($page * $pages) + 1;

        $query = Negocio::query();
        if ($texto !== '') {
            $query->where(function ($q) use ($texto) {
                $q->where('nnegocio', 'like', '%' . $texto . '%')
                    ->orWhere('dir', 'like', '%' . $texto . '%')
                    ->orWhere('nit', 'like', '%' . $texto . '%')
                    ->orWhere('telefonos', 'like', '%' . $texto . '%')
                    ->orWhere('celular', 'like', '%' . $texto . '%');
            });
        }
        if ($estado === 'activo' || $estado === 'inactivo') {
            $query->where('estadonegocio', $estado);
        }

        $total = (clone $query)->count();

        $negocios = $query
            ->orderByDesc('id')
            ->skip($page * $pages)
            ->take($pages)
            ->get();
        foreach ($negocios as $negocio) {
            $negocio->categorias = $negocio->categorias()->get();
            $negocio->cuenta = $cuenta;
            $cuenta++;
        }

        //$rows = 0;
        $first_page = 0;
        $actual_page = $page;
        $next_page = null;


        $pre_page = null;

        $total_pages = $pages > 0 ? (int) ceil($total / $pages) : 1;
        if ($total_pages < 1) {
            $total_pages = 1;
        }
        $last_page = $total_pages;
        $last_page_index = $total_pages - 1;
        if ($page < $last_page_index) {
            $next_page = $page + 1;
        }
        if ($page > 0) {
            $pre_page = $page - 1;
        }

        $result = array(
            'data' => $negocios,
            'pagination' => array(
                'first_page' => $first_page,
                'actual_page' => $actual_page,
                'next_page' => $next_page,
                'total' => $total,
                'pre_page' => $pre_page,
                'pages' => $pages,
                'last_page' => $last_page,
                'cuenta' => $cuenta
            ),
            'categorias'=> Categoria::where('cstate', 'active')->get()
        );

        return response()->json(['status'=>'ok','result'=>$result], 200);
    }

}
