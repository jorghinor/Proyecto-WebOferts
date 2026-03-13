<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\AnuncioPaquete;
use App\Models\Negocio;
use App\Models\Paquete;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestacadosFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_destacados_returns_esquinero_ads_when_available()
    {
        $esquinero = $this->createActiveAnuncioWithPackage('esquinero', 'Esquinero premium');
        $this->createActiveAnuncioWithPackage('seccion', 'Sección visible');

        $response = $this->getJson('/destacados');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $esquinero->id)
            ->assertJsonPath('0.paquete.label', 'Esquinero premium');
    }

    public function test_destacados_falls_back_to_seccion_ads_when_no_esquinero_exists()
    {
        $seccion = $this->createActiveAnuncioWithPackage('seccion', 'Sección fallback');

        $response = $this->getJson('/destacados');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $seccion->id)
            ->assertJsonPath('0.paquete.label', 'Sección fallback');
    }

    private function createActiveAnuncioWithPackage(string $tipo, string $label): Anuncio
    {
        $user = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Negocio ' . $label,
            'ciudad' => 'Cochabamba',
            'nit' => '123456',
            'dir' => 'Av. Aroma 123',
            'gmaps' => null,
            'latitude' => null,
            'longitud' => null,
            'telefonos' => '4455667',
            'celular' => '70000001',
            'delivery' => 1,
            'web' => 'negocio-' . str_replace(' ', '-', strtolower($label)),
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $user->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto ' . $label,
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 25,
            'precio_oferta' => 20,
            'stock' => 10,
            'estado_prod' => 'activo',
            'tipoproducto' => 'uno',
            'negocio_id' => $negocio->id,
        ]);

        $anuncio = Anuncio::create([
            'titulo' => 'Anuncio ' . $label,
            'descripcion' => 'Descripción de prueba',
            'estadoanuncio' => 'activo',
            'codigo' => 'COD-' . strtoupper(substr(md5($label), 0, 8)),
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);

        $paquete = Paquete::create([
            'titulo' => 'Paquete ' . $label,
            'descripcion' => 'Paquete de prueba',
            'costo' => 30,
            'tiempo' => 1,
            'estado' => 'activo',
            'label' => $label,
            'color' => '#ff4aa0',
            'orden' => 1,
            'tipopaquete' => $tipo,
        ]);

        AnuncioPaquete::create([
            'tipo' => $tipo,
            'costo' => 30,
            'orden' => 1,
            'fechafin' => now()->addMonth()->toDateString(),
            'tiempo' => 1,
            'estadocompra' => 'activo',
            'codigopago' => null,
            'label' => $label,
            'color' => '#ff4aa0',
            'anuncio_id' => $anuncio->id,
            'paquete_id' => $paquete->id,
        ]);

        return $anuncio;
    }
}