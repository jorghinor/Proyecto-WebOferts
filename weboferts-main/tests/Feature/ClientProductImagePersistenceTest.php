<?php

namespace Tests\Feature;

use App\Models\Negocio;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientProductImagePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_product_with_own_image()
    {
        $client = User::factory()->create(['rol' => 'client', 'estadou' => 'activo']);

        $negocio = Negocio::create([
            'nnegocio' => 'Negocio Cliente',
            'ciudad' => 'cochabamba',
            'nit' => '741258',
            'dir' => 'Av. Cliente 100',
            'telefonos' => '4455888',
            'celular' => '70003000',
            'delivery' => 1,
            'web' => 'negocio-cliente',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $response = $this->actingAs($client)->post('/user/productos/crear', [
            'nombre' => 'Producto con foto',
            'descripcion' => 'Con imagen propia',
            'precio_regular' => 30,
            'precio_oferta' => 25,
            'tipoproducto' => 'comida',
            'imagen' => '/storage/images/producto-propio.webp',
            'n_id' => $negocio->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.imagen', '/storage/images/producto-propio.webp');

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto con foto',
            'imagen' => '/storage/images/producto-propio.webp',
            'negocio_id' => $negocio->id,
        ]);
    }

    public function test_client_can_update_product_image()
    {
        $client = User::factory()->create(['rol' => 'client', 'estadou' => 'activo']);

        $negocio = Negocio::create([
            'nnegocio' => 'Negocio Editor',
            'ciudad' => 'cochabamba',
            'nit' => '852963',
            'dir' => 'Av. Editor 200',
            'telefonos' => '4455999',
            'celular' => '70004000',
            'delivery' => 1,
            'web' => 'negocio-editor',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto Editable',
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 22,
            'precio_oferta' => 18,
            'estado_prod' => 'activo',
            'tipoproducto' => 'comida',
            'imagen' => '/storage/images/antes.webp',
            'negocio_id' => $negocio->id,
        ]);

        $response = $this->actingAs($client)->post('/user/productos/actualizar/' . $producto->id, [
            'nombre' => 'Producto Editable',
            'descripcion' => 'Producto actualizado',
            'precio_regular' => 24,
            'precio_oferta' => 19,
            'tipoproducto' => 'comida',
            'state' => true,
            'imagen' => '/storage/images/despues.webp',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.imagen', '/storage/images/despues.webp')
            ->assertJsonPath('data.estado_prod', 'activo');

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'imagen' => '/storage/images/despues.webp',
        ]);
    }
}