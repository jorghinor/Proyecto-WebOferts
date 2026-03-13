<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Puede ser null si es compra como invitado
            $table->string('nombre_cliente');
            $table->string('email_cliente')->nullable();
            $table->string('telefono_cliente')->nullable();
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente'); // pendiente, pagado, completado, cancelado
            $table->string('metodo_pago')->default('efectivo'); // tarjeta, efectivo, transferencia
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->string('nombre_producto'); // Guardamos el nombre por si el producto se borra
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_pedidos');
        Schema::dropIfExists('pedidos');
    }
}
