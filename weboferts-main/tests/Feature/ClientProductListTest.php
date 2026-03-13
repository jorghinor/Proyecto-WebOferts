<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\Foto;
use App\Models\Negocio;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientProductListTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_product_list_uses_authenticated_users_business_relation()
    {
        User::factory()->create([
            'rol' => 'admin',
            'estadou' => 'activo',
        ]);

        $client = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Productos Cliente',
            'ciudad' => 'cochabamba',
            'nit' => '789456',
            'dir' => 'Av. Cliente 123',
            'telefonos' => '4455667',
            'celular' => '70001000',
            'delivery' => 1,
            'web' => 'productos-cliente',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Salteña Especial',
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 12,
            'precio_oferta' => 10,
            'estado_prod' => 'activo',
            'tipoproducto' => 'comida',
            'negocio_id' => $negocio->id,
        ]);

        $anuncio = Anuncio::create([
            'titulo' => 'Promo Salteña',
            'descripcion' => 'Anuncio del producto',
            'estadoanuncio' => 'activo',
            'codigo' => 'SALT001',
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);

        Foto::create([
            'url' => '/storage/images/test-producto.jpg',
            'anuncio_id' => $anuncio->id,
        ]);

        $response = $this->actingAs($client)->post('/user/lista/productos');

        $response->assertOk()
            ->assertJsonPath('result.negocio.id', $negocio->id)
            ->assertJsonPath('result.productos.0.id', $producto->id)
            ->assertJsonPath('result.productos.0.imagen', '/storage/images/test-producto.jpg');
    }

    public function test_client_product_list_prefers_products_own_image_when_available()
    {
        $client = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Productos con Imagen Propia',
            'ciudad' => 'cochabamba',
            'nit' => '123999',
            'dir' => 'Av. Imagen 321',
            'telefonos' => '4455000',
            'celular' => '70002000',
            'delivery' => 1,
            'web' => 'productos-imagen',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto con imagen propia',
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 18,
            'precio_oferta' => 15,
            'estado_prod' => 'activo',
            'tipoproducto' => 'comida',
            'imagen' => '/storage/images/propia.webp',
            'negocio_id' => $negocio->id,
        ]);

        $anuncio = Anuncio::create([
            'titulo' => 'Promo con foto legacy',
            'descripcion' => 'Anuncio del producto',
            'estadoanuncio' => 'activo',
            'codigo' => 'LEGACY001',
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);

        Foto::create([
            'url' => '/storage/images/fallback.jpg',
            'anuncio_id' => $anuncio->id,
        ]);

        $response = $this->actingAs($client)->post('/user/lista/productos');

        $response->assertOk()
            ->assertJsonPath('result.productos.0.imagen', '/storage/images/propia.webp');
    }

    public function test_client_product_list_returns_empty_when_client_has_no_business()
    {
        $client = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $response = $this->actingAs($client)->post('/user/lista/productos');

        $response->assertOk()
            ->assertJsonPath('result.negocio', null)
            ->assertJsonCount(0, 'result.productos');
    }
}
