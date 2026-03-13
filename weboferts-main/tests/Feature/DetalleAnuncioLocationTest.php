<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\Negocio;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetalleAnuncioLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_detalleanuncio_returns_business_location_fields()
    {
        $user = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Mapa Seguro',
            'ciudad' => 'Cochabamba',
            'nit' => '654321',
            'dir' => 'Av. América 123',
            'gmaps' => 'https://www.google.com/maps/search/?api=1&query=-17.393500,-66.157000',
            'latitude' => -17.3935,
            'longitud' => -66.1570,
            'telefonos' => '4455667',
            'celular' => '70000011',
            'delivery' => 1,
            'web' => 'mapa-seguro',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $user->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Combo mapa',
            'descripcion' => 'Producto con ubicación',
            'precio_regular' => 30,
            'precio_oferta' => 25,
            'stock' => 5,
            'estado_prod' => 'activo',
            'tipoproducto' => 'uno',
            'negocio_id' => $negocio->id,
        ]);

        $anuncio = Anuncio::create([
            'titulo' => 'Anuncio con mapa',
            'descripcion' => 'Detalle con ubicación exacta',
            'estadoanuncio' => 'activo',
            'codigo' => 'MAPA-001',
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);

        $this->getJson('/detalleanuncio/' . $anuncio->id)
            ->assertOk()
            ->assertJsonPath('result.negocio.id', $negocio->id)
            ->assertJsonPath('result.negocio.gmaps', 'https://www.google.com/maps/search/?api=1&query=-17.393500,-66.157000')
            ->assertJsonPath('result.negocio.latitude', -17.3935)
            ->assertJsonPath('result.negocio.longitud', -66.157);
    }
}
