<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dispute extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    // Dispute status constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_VENDOR_PREVAILS = 'vendor_prevails';
    public const STATUS_BUYER_PREVAILS = 'buyer_prevails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'status',
        'reason',
        'resolved_at',
        'resolved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Set UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the order that owns the dispute.
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    /**
     * Get the admin user who resolved the dispute.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the messages for this dispute.
     */
    public function messages()
    {
        return $this->hasMany(DisputeMessage::class, 'dispute_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get the formatted status.
     */
    public function getFormattedStatus()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active Dispute',
            self::STATUS_VENDOR_PREVAILS => 'Vendor Prevails',
            self::STATUS_BUYER_PREVAILS => 'Buyer Prevails',
            default => 'Unknown Status'
        };
    }

    /**
     * Resolve the dispute with vendor prevailing.
     */
    public function resolveVendorPrevails($adminId)
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_VENDOR_PREVAILS;
        $this->resolved_at = now();
        $this->resolved_by = $adminId;
        $this->save();

        // Mark the order as completed
        $this->order->markAsCompleted();

        return true;
    }

    /**
     * Resolve the dispute with buyer prevailing.
     */
    public function resolveBuyerPrevails($adminId)
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_BUYER_PREVAILS;
        $this->resolved_at = now();
        $this->resolved_by = $adminId;
        $this->save();

        // Mark the order as cancelled
        $this->order->markAsCancelled();

        return true;
    }

    /**
     * Get all disputes for the admin.
     */
    public static function getAllDisputes()
    {
        return self::with(['order', 'order.user', 'order.vendor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active disputes for the admin.
     */
    public static function getActiveDisputes()
    {
        return self::where('status', self::STATUS_ACTIVE)
            ->with(['order', 'order.user', 'order.vendor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get resolved disputes for the admin.
     */
    public static function getResolvedDisputes()
    {
        return self::whereIn('status', [self::STATUS_VENDOR_PREVAILS, self::STATUS_BUYER_PREVAILS])
            ->with(['order', 'order.user', 'order.vendor', 'resolver'])
            ->orderBy('resolved_at', 'desc')
            ->get();
    }

    /**
     * Get all disputes for a user (as buyer).
     */
    public static function getUserDisputes($userId)
    {
        return self::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['order', 'order.vendor'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Get all disputes for a vendor.
     */
    public static function getVendorDisputes($vendorId)
    {
        return self::whereHas('order', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
        ->with(['order', 'order.user'])
        ->orderBy('created_at', 'desc')
        ->get();
    }
}

/**
 * DisputeMessage model represents individual messages within a dispute.
 */
class DisputeMessage extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'dispute_id',
        'user_id',
        'message',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Set UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the dispute that owns the message.
     */
    public function dispute()
    {
        return $this->belongsTo(Dispute::class, 'dispute_id');
    }

    /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the message is from an admin.
     */
    public function isFromAdmin()
    {
        return $this->user && $this->user->hasRole('admin');
    }

    /**
     * Check if the message is from the buyer.
     */
    public function isFromBuyer()
    {
        if (!$this->user || !$this->dispute || !$this->dispute->order) {
            return false;
        }

        return $this->user->id === $this->dispute->order->user_id;
    }

    /**
     * Check if the message is from the vendor.
     */
    public function isFromVendor()
    {
        if (!$this->user || !$this->dispute || !$this->dispute->order) {
            return false;
        }

        return $this->user->id === $this->dispute->order->vendor_id;
    }

    /**
     * Get the message type for UI display.
     */
    public function getMessageType()
    {
        if ($this->isFromAdmin()) {
            return 'admin';
        } elseif ($this->isFromBuyer()) {
            return 'buyer';
        } elseif ($this->isFromVendor()) {
            return 'vendor';
        } else {
            return 'unknown';
        }
    }
}