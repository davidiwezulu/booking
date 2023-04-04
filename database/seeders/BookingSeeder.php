<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Booking::factory()->create();
        Booking::factory()->create([
            'start_time' => Carbon::parse('2023-04-05 10:00'),
            'end_time' => Carbon::parse('2023-04-05 10:30'),
        ]);

        Booking::factory()->create([
            'start_time' => Carbon::parse('2023-04-05 10:30'),
            'end_time' => Carbon::parse('2023-04-05 11:00'),
        ]);

        //Blocked day from admin
        Booking::factory()->create([
            'start_time' => Carbon::parse('2023-04-06 10:30'),
            'end_time' => Carbon::parse('2023-04-06 11:00'),
            'is_blocked_all_day' => true,
        ]);
    }
}
