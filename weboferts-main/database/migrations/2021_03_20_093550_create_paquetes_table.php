<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaquetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paquetes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 190)->unique();
            $table->text('descripcion')->nullable();
            $table->float('costo',8,2);
            $table->smallInteger('tiempo');
            $table->string('estado', 15);
            $table->string('label', 15)->nullable();
            $table->string('color', 15)->nullable();
            $table->smallInteger('orden');
            $table->string('tipopaquete', 20);
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
        Schema::dropIfExists('paquetes');
    }
}
