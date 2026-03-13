<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PaqueteController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->middleware('auth');
    }
    /*public function index()
    {
        return view('paquete.panel');
    }*/
    public function index()
    {
        return view('admin.paquetes');
    }

    /*public function comprar(Request $request){
        
        $usuario = User::find(auth()->id());
        $paquete = $usuario->paquete()->get();

        return response()->json(['status'=>'ok','result'=>$paquete[0]], 200);
    }*/
    //AGREGADO
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'titulo' => 'required|unique:paquetes,titulo',
            'descripcion' => 'required',
            'costo' => 'required',
            'tiempo' => 'required',
            'orden' => 'required',
            //'fecha_limite' => 'required',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Nuevo Paquete Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $paquete = new Paquete();
        $paquete->titulo = $input['titulo'];
        $paquete->descripcion = $input['descripcion'];
        //AGREGADO
        $paquete->costo = $input['costo'];
        $paquete->tiempo = $input['tiempo'];
        //$paquete->tiempo = '6';
        //$calcularfecha = date('Y-m-d', strtotime('+'.$paquete->tiempo.' months'));
        //$paquete->fecha_limite = $calcularfecha;
        $paquete->estado = "activo";
        $paquete->orden = $input['orden'];
        $paquete->tipopaquete = $input['tipopaquete'];
        $paquete->label = $input['label'];
        $paquete->color = $input['color'];

        $paquete->save();

        $response = [
            'success' => true,
            'data' => $paquete,
            'message' => 'Paquete registrado con exito.'
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
        //$calcularfecha = date('Y-m-d', strtotime('+'.$paquete->tiempo.' months'));
        //$paquete->fecha_limite = $calcularfecha;
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
            'data' => $paquete,
            'message' => 'Paquete actualizado con exito.'
        ];

        return response()->json($response, 200);
    }
    public function adminPaquetes()
    {
        $paquetes = Paquete::all();
        return response()->json($paquetes, 200);
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

        $paquete = Paquete::find($id);
        if (!$paquete) {
            return response()->json([
                'success' => false,
                'message' => 'Paquete no encontrado.',
            ], 404);
        }

        $paquete->estado = $request->input('state');
        $paquete->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $paquete->id,
                'estado' => $paquete->estado,
            ],
            'message' => 'Estado del paquete actualizado.'
        ], 200);
    }
}
