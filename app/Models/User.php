<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function meeting(): HasMany{
        return $this->hasMany(Meeting::class, 'host_id');
    }

    public function co_host(): HasMany{
        return $this->hasMany(Meeting::class, 'co_host_id');
    }

    public function attendee(): HasMany{
        return $this->hasMany(Attendee::class);
    }

    public function contacts(): HasMany{
        return $this->hasMany(Contact::class);
    }
   

    public function notification(): HasMany{
        return $this->hasMany(Notification::class);
    }

    public function presenter(){
        return $this->hasMany(Agenda::class, 'presenter_id');
    }
}
