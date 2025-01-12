<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'name',
        'email',
        'rsvp_status',
        'role',
    ];

    /**
     * Relationship: Attendee belongs to a meeting.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Relationship: Attendee belongs to a user (optional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
