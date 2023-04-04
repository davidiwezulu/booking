<?php

namespace Database\Factories;

use App\Models\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleMakeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleMake::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }

}
