<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Referral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'referred_user_reference_id',
        'referred_user_id',
    ];

    /**
     * Get the user that owns the referral.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referred user.
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Get the referred_user_reference_id attribute.
     *
     * @param  string  $value
     * @return string|null
     */
    public function getReferredUserReferenceIdAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Set the referred_user_reference_id attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setReferredUserReferenceIdAttribute($value): void
    {
        $this->attributes['referred_user_reference_id'] = Crypt::encryptString($value);
    }
}
