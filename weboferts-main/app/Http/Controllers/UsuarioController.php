<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Negocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.usuarios');
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
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'clave' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->nombres,
            'email' => $request->email,
            'password' => Hash::make($request->clave),
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        // Crear un negocio asociado
        $negocio = new Negocio();
        $negocio->nnegocio = 'Negocio de ' . $user->name;
        $negocio->ciudad = "cochabamba"; // O un valor por defecto
        $negocio->dir = "--";
        $negocio->delivery = 0;
        $negocio->estadonegocio = "inactivo";
        $negocio->compra = 0;
        $negocio->user_id = $user->id;
        $negocio->save();

        $user->nnegocio = $negocio->nnegocio;

        return response()->json(['success' => true, 'data' => $user], 201);
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'estadou' => 'required|in:activo,inactivo',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'success' => false,
                'data' => 'Editar Usuario Validation Error.',
                'message' => $validatedData->errors()
            ], 422);
        }

        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->estadou = $request->input('estadou');
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'message' => 'Usuario actualizado con exito.'
        ], 200);
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
                'message' => $validatedData->errors()
            ], 422);
        }

        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $usuario->estadou = $request->input('state');
        $usuario->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $usuario->id,
                'estadou' => $usuario->estadou,
            ],
            'message' => 'Estado del usuario actualizado.'
        ], 200);
    }

    public function allUsers(Request $request)
    {
        $input = $request->all();
        $page = $input['page'];
        $pages = $input['pages'];
        $texto = isset($input['texto']) ? trim($input['texto']) : '';
        $estado = isset($input['estado']) ? trim($input['estado']) : 'todos';

        $query = DB::table('users')
            ->leftJoin('negocios', 'negocios.user_id', '=', 'users.id')
            ->where('users.rol', '=', 'client');

        if ($texto !== '') {
            $query->where(function ($q) use ($texto) {
                $q->where('users.name', 'like', '%' . $texto . '%')
                    ->orWhere('users.email', 'like', '%' . $texto . '%')
                    ->orWhere('negocios.nnegocio', 'like', '%' . $texto . '%');
            });
        }
        if ($estado === 'activo' || $estado === 'inactivo') {
            $query->where('users.estadou', '=', $estado);
        }

        $total = (clone $query)->count('users.id');

        $usuarios = $query
            ->orderByDesc('users.id')
            ->skip($input['page'] * $input['pages'])
            ->take($input['pages'])
            ->select('users.id', 'users.name', 'users.email', 'users.estadou', 'users.created_at', 'users.updated_at', 'negocios.nnegocio')
            ->get();

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
            'data' => $usuarios,
            'pagination' => array(
                'first_page' => $first_page,
                'actual_page' => $actual_page,
                'next_page' => $next_page,
                'total' => $total,
                'pre_page' => $pre_page,
                'pages' => $pages,
                'last_page' => $last_page
            )
        );

        return response()->json(['status' => 'ok', 'result' => $result], 200);
    }
}
