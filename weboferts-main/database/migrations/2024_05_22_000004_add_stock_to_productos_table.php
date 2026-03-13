<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockToProductosTable extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(100)->after('precio_oferta');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
}
