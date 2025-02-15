<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use HasFactory;

    protected $table = 'vendor_payment_subaddresses';

    protected $fillable = [
        'identifier',
        'address',
        'address_index',
        'user_id',
        'total_received',
        'expires_at',
        'payment_completed',
        'application_text',
        'application_status',
        'application_images',
        'application_submitted_at',
        'admin_response_at',
        'refund_amount',
        'refund_address'
    ];

    protected $casts = [
        'total_received' => 'decimal:12',
        'refund_amount' => 'decimal:12',
        'expires_at' => 'datetime',
        'payment_completed' => 'boolean',
        'application_images' => 'json',
        'application_submitted_at' => 'datetime',
        'admin_response_at' => 'datetime'
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->identifier = \Str::random(30);
        });
    }

    public function getRouteKeyName()
    {
        return 'identifier';
    }

    public function isApplicationSubmitted()
    {
        return !is_null($this->application_status);
    }

    public function canSubmitApplication()
    {
        return $this->payment_completed && !$this->isApplicationSubmitted();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}