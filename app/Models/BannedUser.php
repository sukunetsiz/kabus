<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reason',
        'banned_until',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
