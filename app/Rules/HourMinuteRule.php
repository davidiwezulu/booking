<?php

namespace App\Rules;

use Carbon\Carbon;
use App\Services\BookingService;
use Illuminate\Contracts\Validation\Rule;

class HourMinuteRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->isValidDateOrTime($value, 'H:i');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The :attribute must be a valid time.";
    }

    public function isValidDateOrTime($string, $format = 'Y-m-d H:i:s'): bool
    {
        try {
            $d = Carbon::createFromFormat($format, $string);
            return $d && $d->format($format) === $string;
        } catch (\Exception $e) {
            return false;
        }
    }

}
