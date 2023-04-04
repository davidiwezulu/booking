<?php

namespace Database\Factories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->phoneNumber,
            'vehicle_make' => $this->faker->colorName,
            'vehicle_model' => $this->faker->colorName,
            'start_time' => Carbon::parse('2023-04-05 09:00'),
            'end_time' => Carbon::parse('2023-04-05 09:30'),
        ];
    }
}
