<?php

namespace App\Http\Requests;

use App\Rules\HourMinuteRule;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        return [
            'booking_date' => 'required|date',
            'booking_time' => ['required', new HourMinuteRule],
            'vehicle_model' => 'required|string',
            'vehicle_make' => 'required|string',
            'phone' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
