<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            'nombres' => 'required',
            'email' => 'required|unique:users,email',
            'clave' => 'min:6|required_with:clave_confirmation|same:clave_confirmation',
            'clave_confirmation' => 'min:6'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Registration Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $input = $request->all();

        $user = new User;
        $user->email = $input['email'];
        $user->name = $input['nombres'];
        $user->password = Hash::make($input['clave']);
        $user->remember_token = Hash::make(time());
        $user->rol = "client";
        $user->estadou = "inactivo";
        $user->save();



        $response = [
            'success' => true,
            'data' => $user->id,
            'message' => 'Client registrado con exito.'
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
