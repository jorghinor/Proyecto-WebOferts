<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPaquete extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'paquete_id' => 'required',
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Paquete Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $user = new User;
        $user->email = $input['email'];
        $user->name = $input['titulo'];
        $user->password = Hash::make($input['clave']);
        $user->remember_token = Hash::make(time());
        $user->rol = "client";
        $user->estadou = "activo";
        $user->save();


        $paquete = new Paquete();
        $paquete->titulo = $input['titulo'];
        $paquete->descripcion = $input['descripcion'];
        //AGREGADO
        $paquete->costo = $input['costo'];
        $paquete->tiempo = $input['tiempo'];
        $paquete->estado = "active";
        $paquete->orden = $input['orden'];
        $paquete->fecha_limite = $input['fecha_limite'];

        $paquete->save();

        $response = [
            'success' => true,
            'data' => $paquete,
            'message' => 'Paquete registrado con exito.'
        ];

        return response()->json($response, 200);
    }

}
