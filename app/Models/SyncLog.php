<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    protected $fillable = [
        'user_id',
        'strava_account_id',
        'sync_type',
        'status',
        'started_at',
        'completed_at',
        'activities_imported',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sync_type' => 'string',
            'status' => 'string',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stravaAccount(): BelongsTo
    {
        return $this->belongsTo(StravaAccount::class);
    }
}
