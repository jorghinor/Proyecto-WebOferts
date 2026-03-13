<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaquetesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('paquetes')->insert([
            [
                'tipopaquete' => 'Basico',
                'titulo' => 'Paquete Básico',
                'costo' => 10.00,
                'orden' => 1,
                'tiempo' => 30,
                'estado' => 'activo', // Added the missing 'estado' field
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipopaquete' => 'Premium',
                'titulo' => 'Paquete Premium',
                'costo' => 25.00,
                'orden' => 2,
                'tiempo' => 90,
                'estado' => 'activo', // Added the missing 'estado' field
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
