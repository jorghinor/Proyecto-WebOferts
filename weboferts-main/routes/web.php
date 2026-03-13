<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\InventoryController; // Importar InventoryController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('mail', function () {
    return view('mails.register');
});

Route::get('registro', [App\Http\Controllers\HomeController::class, 'registro']);
Route::post('registro', [App\Http\Controllers\HomeController::class, 'register']);

Route::get('confirmation/{param}', [App\Http\Controllers\HomeController::class, 'verify']);


Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('home', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('categorias', [App\Http\Controllers\HomeController::class, 'pageCategorias']);
Route::get('recomendados', [App\Http\Controllers\HomeController::class, 'recomendados']);
Route::get('anuncio/detalle/{id}', [App\Http\Controllers\HomeController::class, 'detalle']);
Route::get('detalleanuncio/{id}', [App\Http\Controllers\HomeController::class, 'detalleanuncio']);

Route::get('destacados', [App\Http\Controllers\HomeController::class, 'destacados']);

Route::post('catsnego', [HomeController::class, 'categoriasAndNegocios']);
Route::get('negocioscat/{id}', [HomeController::class, 'negociosCat']);

Route::get('home/categorias', [HomeController::class, 'categoriasActive']);
//Route::get('/home', 'HomeController@index')->name('home');

Route::get('categorias/home/negocios/{param}', [HomeController::class, 'negocios']);
Route::get('home/negocios/{param}', [HomeController::class, 'homeNegociosCategoria']);
Route::get('anuncios', [HomeController::class, 'pageAnuncios']);
Route::get('resultados', [HomeController::class, 'resultados']);
Route::post('buscar', [HomeController::class, 'buscar']);

// New route for ads by category
Route::get('anuncios/categoria/{category_url}', [HomeController::class, 'anunciosPorCategoria']);

// Rutas para el Checkout (Proceso de Compra)
Route::post('/checkout/process', [CheckoutController::class, 'store'])->name('checkout.process');
Route::get('/checkout/receipt/{id}', [CheckoutController::class, 'showReceipt'])->name('checkout.receipt');
Route::get('/checkout/pdf/{id}', [CheckoutController::class, 'downloadPdf'])->name('checkout.pdf'); // Descargar PDF

// Rutas para Reseñas (Públicas)
Route::get('/reviews/{negocioId}', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']); // Sin middleware auth


Auth::routes(['register' => false]);


Route::prefix('admin')->group(function () {

    Route::get('/', [AdminController::class,'index'])->middleware('rol:admin');
    Route::resource('/categorias', CategoriaController::class)->middleware('rol:admin');
    Route::get('/catlist', [CategoriaController::class, 'adminCategorias'])->middleware('rol:admin');
    Route::put('/categorias/state/{id}', [CategoriaController::class, 'changeState'])->middleware('rol:admin');
    Route::resource('/usuarios', UsuarioController::class)->middleware('rol:admin');
    Route::post('/usuarios/list', [UsuarioController::class, 'allUsers'])->middleware('rol:admin');
    Route::put('/usuarios/state/{id}', [UsuarioController::class, 'changeState'])->middleware('rol:admin');
    Route::resource('/negocios', NegocioController::class)->middleware('rol:admin');
    Route::get('/neglist', [NegocioController::class, 'adminNegocios'])->middleware('rol:admin');
    Route::post('/lista/negocios', [NegocioController::class, 'adminNegocios2'])->middleware('rol:admin');
    Route::put('/negocios/state/{id}', [NegocioController::class, 'changeState'])->middleware('rol:admin');
    Route::get('/files/{filename}', [FileController::class, 'archivo'])->middleware('rol:admin');
    Route::get('/images/{filename}', [FileController::class,'displayImage'])->middleware('rol:admin');
    Route::post('/files',[FileController::class,'imageUpload']);
    Route::post('/files/anuncio', [FileController::class,'fotoUpload'])->middleware('rol:admin');
    Route::get('/files/delete/{filename}', [FileController::class,'deleteFile'])->middleware('rol:admin');
    Route::resource('/paquetes', PaqueteController::class)->middleware('rol:admin');
    Route::get('/paqlist', [PaqueteController::class, 'adminPaquetes'])->middleware('rol:admin');
    Route::put('/paquetes/state/{id}', [PaqueteController::class, 'changeState'])->middleware('rol:admin');
    Route::get('/productos', [ProductoController::class, 'index'])->middleware('rol:admin');
    Route::get('/prodlist', [ProductoController::class, 'adminProductos'])->middleware('rol:admin');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->middleware('rol:admin');
    Route::put('/productos/state/{id}', [ProductoController::class, 'changeState'])->middleware('rol:admin');
    Route::get('/anuncios', [AdminController::class, 'anuncios'])->middleware('rol:admin');
    Route::post('/anuncios', [AnuncioController::class, 'store'])->middleware('rol:admin'); // Nueva ruta POST
    Route::post('/lista/anuncios', [AdminController::class, 'allanuncios'])->middleware('rol:admin');
    Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats'])->middleware('rol:admin');
    Route::put('/anuncios/{id}', [AnuncioController::class, 'update'])->middleware('rol:admin');
    Route::put('/anuncios/state/{id}', [AnuncioController::class, 'changeState'])->middleware('rol:admin');

    // Rutas para Pedidos en Admin
    Route::get('/pedidos', [AdminController::class, 'pedidos'])->middleware('rol:admin');
    Route::post('/lista/pedidos', [AdminController::class, 'allpedidos'])->middleware('rol:admin');
    Route::post('/pedidos', [AdminController::class, 'storePedido'])->middleware('rol:admin'); // Crear
    Route::put('/pedidos/{id}', [AdminController::class, 'updatePedido'])->middleware('rol:admin');
    Route::delete('/pedidos/{id}', [AdminController::class, 'deletePedido'])->middleware('rol:admin');

    // Rutas para Reseñas en Admin
    Route::get('/reviews', [AdminController::class, 'reviews'])->middleware('rol:admin');
    Route::post('/lista/reviews', [AdminController::class, 'allReviews'])->middleware('rol:admin');
    Route::post('/reviews/create', [AdminController::class, 'storeReview'])->middleware('rol:admin'); // Crear
    Route::put('/reviews/{id}', [AdminController::class, 'updateReview'])->middleware('rol:admin'); // Editar
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->middleware('rol:admin');

    // Rutas para Inventario (Kardex)
    Route::get('/inventory', [InventoryController::class, 'index'])->middleware('rol:admin');
    Route::post('/lista/inventory', [InventoryController::class, 'list'])->middleware('rol:admin');
    Route::get('/inventory/{id}/movements', [InventoryController::class, 'movements'])->middleware('rol:admin');
    Route::post('/inventory/{id}/add', [InventoryController::class, 'addStock'])->middleware('rol:admin');
    Route::get('/inventory/pdf', [InventoryController::class, 'downloadPdf'])->middleware('rol:admin');
});

Route::prefix('user')->group(function () {
    Route::get('/', [ClientController::class,'index'])->middleware('rol:client');
    Route::get('/dashboard/stats', [ClientController::class, 'dashboardStats'])->middleware('rol:client'); // Nueva ruta
    Route::get('/negocio', [ClientController::class,'negocio'])->middleware('rol:client');
    Route::get('/minegocio', [ClientController::class,'minegocio'])->middleware('rol:client');
    Route::post('/updatenegocio/{id}', [ClientController::class,'actualizar'])->middleware('rol:client');
    Route::post('/negocio/file/{id}', [ClientController::class,'actualizarImagen'])->middleware('rol:client');
    /* COMPRAS */
    //Route::get('/', [PaqueteController::class,'index']);
    Route::get('/mipaquete', [ClientController::class,'mipaquete'])->middleware('rol:client');
    //Route::get('/compras', [ClientController::class,'comprar']);

    Route::post('/paquetes/add', [ClientController::class,'agregar'])->middleware('rol:client');
    Route::post('/files', [FileController::class,'imageUpload'])->middleware('rol:client');
    Route::post('/files/anuncio', [FileController::class,'fotoUpload'])->middleware('rol:client');
    Route::post('/anuncios/crear', [ClientController::class, 'createAnuncio'])->middleware('rol:client');
    Route::get('/anuncios', [ClientController::class, 'anuncios'])->middleware('rol:client');
    /*PRUEBA*/
    /*Route::get('/paquetelist', [ClientController::class, 'userPaquetes']);*/
    /* PAGINAR ANUNCIOS */
    Route::post('/lista/anuncios', [ClientController::class, 'clientAnuncios'])->middleware('rol:client');
     /* PARA PRODUCTOS */
    Route::post('/lista/productos', [ClientController::class, 'clientProductos'])->middleware('rol:client');
    Route::get('/productos', [ClientController::class,'productos'])->middleware('rol:client');
    Route::get('/prodlist', [ClientController::class, 'clientProductos'])->middleware('rol:client');
    Route::post('/productos/crear', [ClientController::class, 'createProducto'])->middleware('rol:client');
    Route::post('/productos/actualizar/{id}', [ClientController::class, 'updateProducto'])->middleware('rol:client');
    Route::put('/anuncios/{id}', [AnuncioController::class, 'update'])->middleware('rol:client');
});
//para paginar los anuncios
Route::post('negoanuncios', [HomeController::class, 'negociosAndAnuncios']);
