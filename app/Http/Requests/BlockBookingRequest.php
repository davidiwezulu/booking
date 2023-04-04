<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Rules\BlockBookingRule;
use App\Rules\HourMinuteRule;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BlockBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $booking_date = ['required', 'date'];
        try {
            if (request()?->get('reserve_type') === Booking::DAY_RESERVATION) {
                $booking_date[] = new BlockBookingRule;
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {}

        return [
            'booking_date' => $booking_date,
            'booking_time' => ['required_if:reserve_type,time', new HourMinuteRule]
        ];
    }
}
