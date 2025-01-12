<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'email',
        'phone'
    ];

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
