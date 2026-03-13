<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        /* $this->middleware('auth'); */
        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categorias = DB::select('SELECT c.id, c.cname, c.icon, c.url, (SELECT COUNT(cn.id) from categoria_negocio cn WHERE cn.categoria_id = c.id) as cuantos FROM categorias c where c.cstate="active" ORDER BY cuantos DESC ');

        return response()->json($categorias, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    public function show()
    {
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




}
