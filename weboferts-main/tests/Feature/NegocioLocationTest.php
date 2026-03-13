<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class NegocioLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_business_coordinates()
    {
        $admin = User::factory()->create([
            'rol' => 'admin',
            'estadou' => 'activo',
        ]);

        $categoria = Categoria::create([
            'cname' => 'Restaurantes',
            'icon' => 'mdi-store',
            'cstate' => 'active',
            'url' => 'restaurantes',
        ]);

        $response = $this->actingAs($admin)->post('/admin/negocios', [
            'nnegocio' => 'Negocio Coordenadas',
            'email' => 'negocio@example.com',
            'nit' => '987654',
            'dir' => 'Av. Siempre Viva 123',
            'gmaps' => 'https://www.google.com/maps/search/?api=1&query=-17.393500,-66.157000',
            'latitude' => -17.3935,
            'longitud' => -66.1570,
            'telefonos' => '4455667',
            'celular' => '70000001',
            'delivery' => true,
            'web' => 'negocio-coordenadas',
            'logo' => '',
            'clave' => 'secreto123',
            'clave_confirmation' => 'secreto123',
            'cats' => [$categoria->id],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $negocio = Negocio::where('nnegocio', 'Negocio Coordenadas')->firstOrFail();

        $this->assertEqualsWithDelta(-17.3935, (float) $negocio->latitude, 0.000001);
        $this->assertEqualsWithDelta(-66.1570, (float) $negocio->longitud, 0.000001);
    }

    public function test_client_can_update_own_business_coordinates()
    {
        $client = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $categoria = Categoria::create([
            'cname' => 'Farmacias',
            'icon' => 'mdi-medical-bag',
            'cstate' => 'active',
            'url' => 'farmacias',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Mi Negocio',
            'ciudad' => 'cochabamba',
            'nit' => '123456',
            'dir' => 'Calle Principal 456',
            'gmaps' => null,
            'latitude' => null,
            'longitud' => null,
            'telefonos' => '4234567',
            'celular' => '70000002',
            'delivery' => 0,
            'web' => 'mi-negocio',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $client->id,
        ]);

        $negocio->categorias()->attach($categoria->id);

        $response = $this->actingAs($client)->post('/user/updatenegocio/' . $negocio->id, [
            'nnegocio' => 'Mi Negocio',
            'ciudad' => 'cochabamba',
            'nit' => '123456',
            'dir' => 'Calle Principal 456',
            'gmaps' => 'https://www.google.com/maps/search/?api=1&query=-17.401234,-66.165432',
            'latitude' => -17.401234,
            'longitud' => -66.165432,
            'telefonos' => '4234567',
            'celular' => '70000002',
            'delivery' => true,
            'web' => 'mi-negocio',
            'cats' => [$categoria->id],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $negocio->refresh();

        $this->assertEqualsWithDelta(-17.401234, (float) $negocio->latitude, 0.000001);
        $this->assertEqualsWithDelta(-66.165432, (float) $negocio->longitud, 0.000001);
    }

    public function test_client_can_create_business_then_upload_logo()
    {
        $client = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $categoria = Categoria::create([
            'cname' => 'Panaderias',
            'icon' => 'mdi-baguette',
            'cstate' => 'active',
            'url' => 'panaderias',
        ]);

        $createResponse = $this->actingAs($client)->post('/user/updatenegocio/null', [
            'nnegocio' => 'Panaderia Central',
            'ciudad' => 'cochabamba',
            'nit' => '456789',
            'dir' => 'Av. Aroma 123',
            'gmaps' => 'https://www.google.com/maps/search/?api=1&query=-17.390000,-66.160000',
            'latitude' => -17.39,
            'longitud' => -66.16,
            'telefonos' => '4123456',
            'celular' => '70000003',
            'delivery' => true,
            'web' => 'panaderia-central',
            'cats' => [$categoria->id],
        ]);

        $createResponse->assertOk()->assertJson(['success' => true]);

        $negocioId = $createResponse->json('negocio_id');
        $this->assertNotNull($negocioId);

        $uploadResponse = $this->actingAs($client)->post('/user/negocio/file/' . $negocioId, [
            'imagen' => UploadedFile::fake()->image('logo.jpg', 500, 500),
            'type' => 'jpg',
        ]);

        $uploadResponse->assertOk()->assertJson(['success' => true]);

        $negocio = Negocio::findOrFail($negocioId);

        $this->assertNotEmpty($negocio->logo);
        $this->assertStringContainsString('/storage/images/', $negocio->logo);

        $filename = basename(parse_url($negocio->logo, PHP_URL_PATH));
        @unlink(base_path('storage/images/' . $filename));
        @unlink(base_path('storage/images/thumbnail/' . $filename));
    }

    public function test_client_cannot_upload_logo_to_another_users_business()
    {
        $owner = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $otherClient = User::factory()->create([
            'rol' => 'client',
            'estadou' => 'activo',
        ]);

        $negocio = Negocio::create([
            'nnegocio' => 'Negocio Privado',
            'ciudad' => 'cochabamba',
            'nit' => '741852',
            'dir' => 'Calle Reservada 999',
            'gmaps' => null,
            'latitude' => null,
            'longitud' => null,
            'telefonos' => '4000000',
            'celular' => '70000004',
            'delivery' => 0,
            'web' => 'negocio-privado',
            'logo' => 'default',
            'estadonegocio' => 'activo',
            'compra' => 0,
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherClient)->post('/user/negocio/file/' . $negocio->id, [
            'imagen' => UploadedFile::fake()->image('logo-ajeno.jpg', 500, 500),
            'type' => 'jpg',
        ]);

        $response->assertStatus(404);

        $negocio->refresh();
        $this->assertSame('default', $negocio->logo);
    }
}
