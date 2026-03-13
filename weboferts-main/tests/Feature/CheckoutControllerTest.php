<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_checkout_saves_customer_email_and_sends_confirmation_mail()
    {
        Mail::fake();

        $payload = [
            'cart' => [
                [
                    'titulo' => 'Combo Promo',
                    'precio' => 25.50,
                    'cantidad' => 2,
                ],
            ],
            'cliente' => [
                'nombre' => 'Jorge Cliente',
                'email' => 'jorge@example.com',
                'telefono' => '77777777',
            ],
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->postJson(route('checkout.process'), $payload);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('pedidos', [
            'nombre_cliente' => 'Jorge Cliente',
            'email_cliente' => 'jorge@example.com',
            'telefono_cliente' => '77777777',
            'metodo_pago' => 'efectivo',
        ]);

        $pedido = Pedido::firstOrFail();

        $this->assertCount(1, $pedido->detalles);

        Mail::assertSent(OrderPlaced::class, function (OrderPlaced $mail) use ($pedido) {
            return $mail->hasTo('jorge@example.com')
                && $mail->pedido->id === $pedido->id;
        });
    }
}