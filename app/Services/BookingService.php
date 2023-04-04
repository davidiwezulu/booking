<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    private array $availableHours = [9, 10, 11, 12, 13, 14, 15, 16, 17];

    public function fetchAvailableHours(string $date): array
    {
        $foundHours = [];
        $date = Carbon::parse($date)->format('Y-m-d');
        Booking::query()
            ->whereDate('start_time', $date)
            ->each(function ($booking) use (&$foundHours) {
                $foundHours[] = Carbon::parse($booking->start_time)->format('H:i');
            });
        foreach ($this->availableHours as $key => $availableHour) {
            if (
                in_array("$availableHour:00", $foundHours, true)
                && in_array("$availableHour:30", $foundHours, true)
            ) {
                unset($this->availableHours[$key]);
            }
        }

        return $this->availableHours;
    }

    public function fetchBlockedDates(): \Illuminate\Support\Collection
    {
        return Booking::query()
            ->where('is_blocked_all_day', true)
            ->where('start_time', '>=', Carbon::now())
            ->pluck('start_time')
            ->map(static fn($startTime) => Carbon::parse($startTime)->format('Y-n-j'));
    }

    public function checkDateAndTime($date, $hour): bool
    {
        $dateTime = Carbon::parse($date . ' ' . $hour)->format('Y-m-d H:i:s');
        if (Booking::where('start_time', $dateTime)->exists()) {
            return false;
        }
        return in_array((int)Carbon::parse($hour)->format('H'), $this->fetchAvailableHours($date), true);
    }

    public function createBooking(array $data): bool
    {
        $data['start_time'] = Carbon::parse($data['booking_date'] . ' ' . $data['booking_time'])->format('Y-m-d H:i:s');
        $data['end_time'] = Carbon::parse($data['start_time'])->addMinutes(30)->format('Y-m-d H:i:s');
        $booking = (new Booking)->fill($data);
        if (auth()->check()) {
            $booking->user_id = auth()->id();
        }
        return $booking->save();
    }

    public function fetchBookings(): Collection
    {
        return Booking::query()
            ->where('user_id', auth()->id())
            ->where('is_blocked', '!=', 1)
            ->where('is_blocked_all_day', '!=', 1)
            ->get();
    }

    public function reserveAllDay(array $data): bool
    {
        $data['is_blocked'] = true;
        $data['is_blocked_all_day'] = true;
        return $this->createBooking(array_merge($data, $this->populateAdminBookingData()));
    }

    public function reserveTime(array $data): bool
    {
        $data['is_blocked'] = true;
        return $this->createBooking(array_merge($data, $this->populateAdminBookingData()));
    }

    private function populateAdminBookingData(): array
    {
        return [
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'vehicle_make' => 'Rerserved',
            'vehicle_model' => 'Rerserved',
            'phone' => 'Rerserved',
        ];
    }

}
