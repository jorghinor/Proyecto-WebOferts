<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnuncioPaquetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anuncio_paquete', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('tipo', 20);
            $table->float('costo',8,2);
            $table->smallInteger('orden');
            $table->date('fechafin');
            $table->string('codigopago')->nullable();
            $table->string('label', 15);
            $table->smallInteger('tiempo');
            $table->string('color', 15);
            $table->string('estadocompra', 15);

            $table->unsignedBigInteger('anuncio_id');
            $table->foreign('anuncio_id')->references('id')->on('anuncios');

            $table->unsignedBigInteger('paquete_id')->unsigned();
            $table->foreign('paquete_id')->references('id')->on('paquetes');
            
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
        Schema::dropIfExists('anuncio_paquetes');
    }
}
