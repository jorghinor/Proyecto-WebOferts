<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero intentamos borrarla si existe para evitar conflictos
        Schema::dropIfExists('reviews');

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Opcional para invitados
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
