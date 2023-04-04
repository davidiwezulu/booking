<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Tests\TestCase;
use  Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected Model $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Booking::factory(10)->create();
    }

    public function testFetchBookableHoursEndpoint(): void
    {
        $date = CarbonImmutable::now()->format('Y-m-d H:i');
        Booking::factory()->create([
            'start_time' => $date,
            'end_time' => CarbonImmutable::parse($date)->addMinutes(30)->format('Y-m-d H:i'),
        ]);
        $this->getJson('api/bookings/available/hours/'
            . CarbonImmutable::parse($date)->format('Y-m-d'))
            ->assertSuccessful()
            ->assertJson(static function (AssertableJson $json) {
                $json->where('0', 9);
                $json->where('1', 10);
                $json->where('2', 11);
                $json->where('3', 12);
                $json->where('4', 13);
                $json->where('5', 14);
                $json->where('6', 15);
                $json->where('7', 16);
                $json->where('8', 17);
            });
    }
}
