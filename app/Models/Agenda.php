<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model
{

    protected $fillable = [
        'id',
        'meeting_id',
        'title',
        'description',
        'presenter_id',
    ];

    public function agenda_items(): HasMany
    {
        return $this->hasMany(Agenda::class, 'presenter_id');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function presenter()
    {
        return $this->belongsTo(User::class, 'presenter_id');
    }
}
