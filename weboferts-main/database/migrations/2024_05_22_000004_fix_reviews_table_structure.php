<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixReviewsTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Borrar la tabla si existe para asegurar que empezamos de cero
        Schema::dropIfExists('reviews');

        // Crear la tabla con la estructura correcta
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable para invitados
            $table->string('reviewer_name')->nullable(); // Nombre del invitado
            $table->unsignedBigInteger('negocio_id');
            $table->integer('rating'); // 1 a 5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('negocio_id')->references('id')->on('negocios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
