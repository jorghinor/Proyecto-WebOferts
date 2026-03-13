<?php

namespace Tests\Feature;

use App\Models\Anuncio;
use App\Models\AnuncioPaquete;
use App\Models\Foto;
use App\Models\Negocio;
use App\Models\Paquete;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAnuncioEditSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_replace_anuncio_photo_without_creating_a_second_photo_record()
    {
        $admin = User::factory()->create(['rol' => 'admin', 'estadou' => 'activo']);
        $anuncio = $this->createAnuncio();

        Storage::put('public/images/existing.webp', 'old-image');
        Storage::put('public/images/thumbnail/existing.webp', 'old-thumb');

        $foto = Foto::create([
            'url' => '/storage/images/existing.webp',
            'anuncio_id' => $anuncio->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/files/anuncio', [
            'a_id' => $anuncio->id,
            'imagen' => UploadedFile::fake()->image('nuevo.jpg', 600, 600),
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $foto->refresh();

        $this->assertDatabaseCount('fotos', 1);
        $this->assertNotSame('/storage/images/existing.webp', $foto->url);
        $this->assertStringStartsWith('/storage/images/', $foto->url);
        $this->assertFalse(Storage::exists('public/images/existing.webp'));
        $this->assertFalse(Storage::exists('public/images/thumbnail/existing.webp'));
    }

    public function test_admin_lista_anuncios_returns_active_package_ids_for_editor()
    {
        $admin = User::factory()->create(['rol' => 'admin', 'estadou' => 'activo']);
        $anuncio = $this->createAnuncio('Anuncio editor');
        $paquete = Paquete::create([
            'titulo' => 'Paquete editor',
            'descripcion' => 'Paquete de prueba',
            'costo' => 35,
            'tiempo' => 7,
            'estado' => 'activo',
            'orden' => 1,
            'label' => 'Editor',
            'color' => '#3366ff',
            'tipopaquete' => 'seccion',
        ]);

        AnuncioPaquete::create([
            'tipo' => $paquete->tipopaquete,
            'costo' => $paquete->costo,
            'orden' => $paquete->orden,
            'fechafin' => now()->addDays($paquete->tiempo),
            'tiempo' => $paquete->tiempo,
            'estadocompra' => 'activo',
            'codigopago' => 'TEST-ADMIN',
            'label' => $paquete->label,
            'color' => $paquete->color,
            'anuncio_id' => $anuncio->id,
            'paquete_id' => $paquete->id,
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/lista/anuncios', [
            'page' => 0,
            'pages' => 15,
            'texto' => 'Anuncio editor',
            'estado' => 'todos',
        ]);

        $response->assertOk()
            ->assertJsonPath('result.data.0.id', $anuncio->id)
            ->assertJsonPath('result.data.0.producto.id', $anuncio->producto_id)
            ->assertJsonPath('result.data.0.paquetes_activos.0', $paquete->id);
    }

    private function createAnuncio(string $titulo = 'Anuncio admin'): Anuncio
    {
        $client = User::factory()->create(['rol' => 'client', 'estadou' => 'activo']);

        $negocio = Negocio::create([
            'nnegocio' => 'Negocio Admin',
            'ciudad' => 'Cochabamba',
            'nit' => '123456',
            'dir' => 'Av. Prueba 123',
            'gmaps' => null,
            'latitude' => null,
            'longitud' => null,
            'telefonos' => '4455667',
            'celular' => '70000001',
            'delivery' => 1,
            'web' => 'negocio-admin',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $producto = Producto::create([
            'nombre' => 'Producto admin',
            'descripcion' => 'Producto de prueba',
            'precio_regular' => 25,
            'precio_oferta' => 20,
            'stock' => 10,
            'estado_prod' => 'activo',
            'tipoproducto' => 'uno',
            'negocio_id' => $negocio->id,
        ]);

        return Anuncio::create([
            'titulo' => $titulo,
            'descripcion' => 'Descripción de prueba',
            'estadoanuncio' => 'activo',
            'codigo' => 'ADM-' . strtoupper(substr(md5($titulo), 0, 8)),
            'negocio_id' => $negocio->id,
            'producto_id' => $producto->id,
        ]);
    }
}