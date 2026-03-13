<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNegociosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nnegocio', 190)->unique();
            $table->string('ciudad', 100); // Increased the size to 100
            $table->string('nit', 30)->nullable();
            $table->string('dir', 190);
            $table->string('gmaps')->nullable();
            $table->double('latitude', 20)->nullable();
            $table->double('longitud', 20)->nullable();
            $table->string('telefonos')->nullable();
            $table->string('celular')->nullable();
            //$table->string('delivery')->nullable();
            $table->boolean('delivery')->default(0);
            $table->string('web')->nullable();
            $table->string('logo')->nullable();
            $table->string('estadonegocio', 20);
            $table->boolean('compra')->default(0);

            $table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('negocios');
    }
}
