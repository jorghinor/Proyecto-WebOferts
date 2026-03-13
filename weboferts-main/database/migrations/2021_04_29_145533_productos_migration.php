<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductosMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->float('precio_regular',8,2)->nullable();
            $table->float('precio_oferta',8,2)->nullable();
            $table->string('estado_prod',15);
            $table->string('tipoproducto',25);

            $table->unsignedBigInteger('negocio_id');
            $table->foreign('negocio_id')->references('id')->on('negocios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
