<?php

namespace App\Http\Controllers\API;

use JWTAuth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class JwtAuthController extends Controller
{
    public $token = true;

    public function register(Request $request)
    {

         $validator = Validator::make($request->all(),
                      [
                      'name' => 'required',
                      'email' => 'required|email',
                      'password' => 'required',
                      'c_password' => 'required|same:password',
                     ]);

         if ($validator->fails()) {

               return response()->json(['error'=>$validator->errors()], 401);

            }


        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->rol = "client"; // Set default role
        $user->estadou = "activo"; // Set user to active by default
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->token) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        if($user->estadou=='inactivo'){
            return response()->json(['error' => 'Email or password not exists'], 401);
        }

        $role = 2;
        if($user->rol=='admin') {
            $role = 1;
        }

        $miuser = array(
            'idu' => $user->id,
            'username' => $user->email,
            'rl'=>$role,
            'name' => $user->name,
        );

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            'type' => 'bearer',
            'user' => $miuser,
            'expires' => auth('api')->factory()->getTTL() * 60000,
        ]);
    }

/*     public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'logout'
        ], 200);
    } */

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }
}
