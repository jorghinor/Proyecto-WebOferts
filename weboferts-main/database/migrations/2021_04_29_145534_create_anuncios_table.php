<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnunciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anuncios', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('estadoanuncio',15);
            $table->string('codigo');

            $table->unsignedBigInteger('negocio_id');
            $table->foreign('negocio_id')->references('id')->on('negocios');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

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
        Schema::dropIfExists('anuncios');
    }
}
