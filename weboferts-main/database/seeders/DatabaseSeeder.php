<?php

namespace Database\Seeders;

use App\Models\Negocio;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            NegociosSeeder::class,
            CategoriasSeeder::class,
            PaquetesSeeder::class, // Added the new seeder
            AnunciosTableSeeder::class
        ]);
    }
}
