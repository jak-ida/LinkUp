<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Meeting;

class Notification extends Model
{
    protected $fillable = [
        'id',
        'meeting_id',
        'user_id',
        'notification_time'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

}
