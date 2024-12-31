<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
        'vacation_mode'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vacation_mode' => 'boolean',
    ];

    /**
     * Get the user that owns the vendor profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}