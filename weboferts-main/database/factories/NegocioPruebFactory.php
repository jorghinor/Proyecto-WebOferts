<?php

namespace Database\Factories;

use App\Models\Negocio;
use Illuminate\Database\Eloquent\Factories\Factory;

class NegocioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Negocio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nnegocio' => $this->faker->text($maxNbChars = 400),
            'nit' => $this->faker->text($maxNbChars = 50),
            'dir' => $this->faker->text($maxNbChars = 600),
            'gmaps' => $this->faker->text($maxNbChars = 600),
            'latitude' => $this->faker->double('amount', 20, 2),
            'longitud' => $this->faker->double('amount', 20, 2),
            'telefonos' => $this->faker->text($maxNbChars = 50),
            'celular' => $this->faker->text($maxNbChars = 50),
            'delivery' => $this->faker->tinyInteger('delivery'),
            'web' => $this->faker->text($maxNbChars = 500),
            'logo' => $this->faker->text($maxNbChars = 250),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
