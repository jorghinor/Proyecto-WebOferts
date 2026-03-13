<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected function authenticated(Request $request, $user)
    {
        if ($user->rol=='admin') {
            return redirect('/admin');
        }

        if ($user->rol=='client') {
            return redirect('/user');
        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => request()->email,
            'password' => request()->password,
            'estadou' => 'activo'
        ];
    }

    public function showLoginForm()
    {
        return view('publico.login');
    }

/*     public function logout()
    {
        $accessToken = auth()->user()->token();

        $refreshToken = DB::table('oauth_refresh_tokens')
        ->where('access_token_id', $accessToken->id)
        ->update([
            'revoked' => true
        ]);

        $accessToken->revoke();

        return response()->json(['status' => 200]);
    } */

}
