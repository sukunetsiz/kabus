<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use HasFactory;

    protected $table = 'vendor_payment_subaddresses';

    protected $fillable = [
        'address',
        'address_index',
        'user_id',
        'total_received',
        'expires_at',
    ];

    protected $casts = [
        'total_received' => 'decimal:12',
        'expires_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}