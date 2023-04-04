<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'vehicle_make', 'vehicle_model', 'start_time', 'end_time',
        'is_blocked', 'is_blocked_all_day'
    ];

    public const TIME_RESERVATION = 'time';
    public const DAY_RESERVATION = 'day';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}
