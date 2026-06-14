<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'strava_activity_id',
        'name',
        'sport_type',
        'started_at',
        'distance',
        'elapsed_time',
        'moving_time',
        'elevation_gain',
        'average_speed',
        'max_speed',
        'average_heartrate',
        'max_heartrate',
        'average_watts',
        'calories',
        'polyline',
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'raw_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
