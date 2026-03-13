<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCategoryCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_category_counts_use_active_ads_instead_of_business_count()
    {
        $categoria = Categoria::create([
            'cname' => 'Camba',
            'icon' => 'mdi-food',
            'cstate' => 'active',
            'url' => 'camba',
        ]);

        $activeBusiness = $this->createBusiness('activo-negocio', 'activo');
        $activeBusiness->categorias()->attach($categoria->id);

        $inactiveBusiness = $this->createBusiness('inactivo-negocio', 'inactivo');
        $inactiveBusiness->categorias()->attach($categoria->id);

        $this->createActiveAd($activeBusiness, 'Majadito');
        $this->createActiveAd($activeBusiness, 'Api con buñuelo');

        $inactiveProduct = $this->createProduct($activeBusiness, 'Silpancho');
        Anuncio::create([
            'titulo' => 'Silpancho inactivo',
            'descripcion' => 'No debe contarse',
            'estadoanuncio' => 'inactivo',
            'codigo' => 'INACT-' . strtoupper(substr(md5('silpancho'), 0, 8)),
            'negocio_id' => $activeBusiness->id,
            'producto_id' => $inactiveProduct->id,
        ]);

        $this->createActiveAd($inactiveBusiness, 'Falso positivo');

        $response = $this->getJson('/home/categorias');

        $response->assertOk();

        $camba = collect($response->json())->firstWhere('url', 'camba');

        $this->assertNotNull($camba);
        $this->assertSame(2, (int) $camba['cuantos']);
    }

    private function createBusiness(string $slug, string $state): Negocio
    {
        $user = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        return Negocio::create([
            'nnegocio' => 'Negocio ' . $slug,
            'ciudad' => 'Cochabamba',
            'nit' => '123456',
            'dir' => 'Av. Aroma 123',
            'gmaps' => null,
            'latitude' => null,
            'longitud' => null,
            'telefonos' => '4455667',
            'celular' => '70000001',
            'delivery' => 1,
            'web' => $slug,
            'logo' => 'default',
            'estadonegocio' => $state,
            'compra' => 0,
            'user_id' => $user->id,
        ]);
    }

    private function createProduct(Negocio $negocio, string $name): Producto
    {
        return Producto::create([
            'nombre' => $name,
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 25,
            'precio_oferta' => 20,
            'stock' => 10,
            'estado_prod' => 'activo',
            'tipoproducto' => 'uno',
            'negocio_id' => $negocio->id,
        ]);
    }

    private function createActiveAd(Negocio $negocio, string $title): Anuncio
    {
        $producto = $this->createProduct($negocio, 'Producto ' . $title);

        return Anuncio::create([
            'titulo' => $title,
            'descripcion' => 'Descripción de prueba',
            'estadoanuncio' => 'activo',
            'codigo' => 'COD-' . strtoupper(substr(md5($title . $negocio->id), 0, 8)),
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);
    }
}