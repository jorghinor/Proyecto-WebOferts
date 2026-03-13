<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
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
        return view('admin.categorias');
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
            'cname' => 'required|unique:categorias,cname',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Nueva Categoria Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $categoria = new Categoria();
        $categoria->cname = $input['cname'];

        if ($input['icon'] == '' or $input['icon'] == null) {
            $categoria->icon = "default";
        } else {
            $categoria->icon = $input['icon'];
        }

        $categoria->cstate = "active";
        $categoria->url = $this->toAscii(strtolower($input['cname']));
        $categoria->save();

        $response = [
            'success' => true,
            'data' => $categoria,
            'message' => 'Categoria registrada con exito.'
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
            'cname' => 'required|unique:categorias,cname,' . $id . ',id',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Editar Categoria Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $categoria = Categoria::find($id);
        $categoria->cname = $input['cname'];
        $categoria->icon = $input['icon'];
        $categoria->url =  $this->toAscii(strtolower($input['cname']));

        if ($input['state']) {
            $categoria->cstate = "active";
        } else {
            $categoria->cstate = "inactive";
        }

        $categoria->save();

        $response = [
            'success' => true,
            'data' => $categoria->url,
            'message' => 'Categoria actualizada con exito.'
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
            'state' => 'required|in:active,inactive',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'success' => false,
                'data' => 'Change State Validation Error.',
                'message' => $validatedData->errors(),
            ], 422);
        }

        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria no encontrada.',
            ], 404);
        }

        $categoria->cstate = $request->input('state');
        $categoria->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $categoria->id,
                'cstate' => $categoria->cstate,
            ],
            'message' => 'Estado de la categoria actualizado.',
        ], 200);
    }

    public function adminCategorias()
    {
        $categorias = DB::select('SELECT c.id, c.cname, c.icon, c.url, cstate, (SELECT COUNT(cn.id) from categoria_negocio cn WHERE cn.categoria_id = c.id) as cuantos FROM categorias c ORDER BY cuantos DESC ');
        return response()->json($categorias, 200);
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
}
