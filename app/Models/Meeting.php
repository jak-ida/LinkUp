<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'host_id',
        'co_host_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'type',
        'venue',
        'meeting_link',
        'state',
    ];

    /**
     * Relationship: A meeting belongs to a host (user).
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }
    /**
     * Relationship: A meeting belongs to a co-host (user).
     */
    public function co_host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_host_id');
    }

    /**
     * Relationship: A meeting has many attendees.
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Relationship: A meeting sends many notifications.
     */
    public function notification(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relationship: A meeting has many agendas.
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class, 'meeting_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('host_id', $userId)
            ->orWhere('co_host_id', $userId) // Check co_host directly since it's a belongsTo relationship
            ->orWhereHas('attendees', function ($q) use ($userId) {
                $q->where('user_id', $userId); // Ensure `user_id` exists in the attendees table
            });
    }
}
