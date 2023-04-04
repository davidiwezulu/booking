<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Response;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\BlockBookingRequest;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class BookingController extends Controller
{
    public function fetchBookingHours(BookingService $bookingService, $date): Response
    {
        return response($bookingService->fetchAvailableHours($date), ResponseStatus::HTTP_OK);
    }

    public function fetchDisabledBookingDates(BookingService $bookingService): Response
    {
        return response($bookingService->fetchBlockedDates(), ResponseStatus::HTTP_OK);
    }

    public function store(BookingService $bookingService, BookingRequest $request): Response
    {
        $data = $request->validated();
        abort_if(
            !$bookingService->checkDateAndTime($data['booking_date'], $data['booking_time']),
            ResponseStatus::HTTP_FORBIDDEN
        );
        return response($bookingService->createBooking($data), ResponseStatus::HTTP_CREATED);
    }

    public function show(BookingService $bookingService): Response
    {
        $bookings = auth()->check() ? $bookingService->fetchBookings() : null;
        return response($bookings, ResponseStatus::HTTP_OK);
    }

    public function blockDate(BookingService $bookingService, BlockBookingRequest $request): Response
    {
        abort_if(!auth()->user()->is_admin, ResponseStatus::HTTP_FORBIDDEN);
        $data = $request->validated();
        if ($request->reserve_type === Booking::DAY_RESERVATION) {
            return response(
                $bookingService->reserveAllDay([
                    'booking_date' => $data['booking_date'],
                    'booking_time' => $data['booking_time'],
                ]),
                ResponseStatus::HTTP_CREATED
            );
        }

        if ($request->reserve_type === Booking::TIME_RESERVATION) {
            abort_if(
                !$bookingService->checkDateAndTime($data['booking_date'], $data['booking_time']),
                ResponseStatus::HTTP_FORBIDDEN
            );
            return response(
                $bookingService->reserveTime([
                    'booking_date' => $data['booking_date'],
                    'booking_time' => $data['booking_time'],
                ]),
                ResponseStatus::HTTP_CREATED);
        }

        abort(ResponseStatus::HTTP_FORBIDDEN);
    }
}
