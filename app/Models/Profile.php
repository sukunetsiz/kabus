<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'description',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile picture URL.
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return route('profile.picture', ['filename' => $this->profile_picture]);
        }
        
        return asset('images/default-profile-picture.png');
    }
}