<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePunchClock extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'access_code',
        'schedules',
    ];

    protected $casts = [
        'description' => 'string',
        'access_code' => 'string',
        'schedules' => 'array',
    ];

    const DAYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
    ];
}
