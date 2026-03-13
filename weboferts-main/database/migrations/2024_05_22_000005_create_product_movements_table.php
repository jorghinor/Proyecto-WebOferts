<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('product_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->string('type'); // sale, restock, adjustment
            $table->integer('quantity'); // Puede ser negativo para salidas
            $table->integer('stock_balance'); // Stock después del movimiento
            $table->string('reference')->nullable(); // Ej: "Pedido #10"
            $table->unsignedBigInteger('user_id')->nullable(); // Responsable del movimiento
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_movements');
    }
}
