<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\Negocio;

class NegociosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create a specific, active admin user for login
        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@weboferts.com';
        $admin->rol = "admin";
        $admin->estadou = "activo"; // Make this user active
        $admin->password = Hash::make('password'); // Use a simple, known password
        $admin->save();

        DB::table('negocios')->insert([
            'nnegocio' => 'WebOferts Admin',
            'dir' => $faker->streetAddress(),
            'ciudad' => $faker->city(), // Add the missing 'ciudad' field
            'delivery' => 1,
            'estadonegocio' => "activo",
            'logo' => $faker->imageUrl(460, 460, 'business', true),
            'user_id' => $admin->id
        ]);

        // Create other random users and negocios
        foreach (range(1, 20) as $index) { // Reduced the number to make seeding faster

            $user = new User();
            $user->name = $faker->firstName().' '.$faker->lastName;
            $user->email = $faker->freeEmail;
            $user->rol = "client";
            $user->estadou = "inactivo"; // Keep these inactive as per original logic
            $user->remember_token = Hash::make(time());
            $user->password = Hash::make('111111');
            $user->save();

            DB::table('negocios')->insert([
                'nnegocio' => $faker->company().' '.$user->id,
                'dir' => $faker->streetAddress(),
                'ciudad' => $faker->city(), // Add the missing 'ciudad' field
                'delivery' => 0,
                'estadonegocio' => "activo",
                'logo' => $faker->imageUrl(460, 460, 'animals', true),
                'user_id' => $user->id
            ]);
        }
    }
}
