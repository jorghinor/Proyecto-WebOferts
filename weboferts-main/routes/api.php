<?php
use App\Http\Controllers\API\JWTAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\API\FrontController;

/* use App\Http\Controllers\API\ApiNegocioController;
use App\Http\Controllers\API\RegisterController; */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

//Route::apiResource('public', FrontController::class);


/* Route::apiResource('negocios', ApiNegocioController::class);


Route::apiResource('register', RegisterController::class); */
//nuevo jwt

/* Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);
  
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
});
 */
/* Route::post('register', 'App\Http\Controllers\UserController@register'); */
/* Route::post('login', 'App\Http\Controllers\UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');
}); */