<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReviewsTableForGuests extends Migration
{
    public function up()
    {
        // Esta migración ya no es necesaria porque se integró en la anterior.
        // Se deja vacía para evitar errores si el sistema intenta ejecutarla.
    }

    public function down()
    {
        // Nada que revertir
    }
}
