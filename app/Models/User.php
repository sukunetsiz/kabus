<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'last_login',
        'mnemonic',
        'password_reset_token',
        'password_reset_expires_at',
        'reference_id',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mnemonic',
        'password_reset_token',
        'reference_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login' => 'datetime',
            'mnemonic' => 'encrypted',
            'password_reset_expires_at' => 'datetime',
            'reference_id' => 'encrypted',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the mnemonic attribute.
     *
     * @param  string  $value
     * @return string|null
     */
    public function getMnemonicAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Set the mnemonic attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setMnemonicAttribute($value): void
    {
        $this->attributes['mnemonic'] = Crypt::encryptString($value);
    }

    /**
     * Get the reference_id attribute.
     *
     * @param  string  $value
     * @return string|null
     */
    public function getReferenceIdAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Set the reference_id attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setReferenceIdAttribute($value): void
    {
        $this->attributes['reference_id'] = Crypt::encryptString($value);
    }

    /**
     * Get all conversations for the user.
     */
    public function conversations()
    {
        return Message::conversation()
            ->where(function($query) {
                $query->where('user_id_1', $this->id)
                    ->orWhere('user_id_2', $this->id);
            });
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')->regularMessage();
    }

    /**
     * Check if the user has reached the conversation limit.
     */
    public function hasReachedConversationLimit()
    {
        return $this->conversations()->count() >= 16;
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's vendor profile.
     */
    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    /**
     * Get the PGP key associated with the user.
     */
    public function pgpKey()
    {
        return $this->hasOne(PgpKey::class);
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a vendor.
     *
     * @return bool
     */
    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }

    /**
     * Get the ban information for the user.
     */
    public function bannedUser()
    {
        return $this->hasOne(BannedUser::class);
    }

    /**
     * Check if the user is currently banned.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->bannedUser && $this->bannedUser->banned_until > now();
    }

    /**
     * Get the return addresses for the user.
     */
    public function returnAddresses()
    {
        return $this->hasMany(ReturnAddress::class);
    }

    /**
     * The attributes that should be cast on the pivot.
     *
     * @var array
     */
    protected $pivotCasts = [
        'read' => 'boolean',
    ];

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->belongsToMany(Notification::class)
            ->withTimestamps()
            ->withPivot('read')
            ->orderBy('notification_user.created_at', 'desc');
    }

    /**
     * Get the wishlisted products for the user.
     */
    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps()
            ->orderBy('wishlists.created_at', 'desc');
    }

    /**
     * Check if a product is in the user's wishlist.
     *
     * @param string $productId
     * @return bool
     */
    public function hasWishlisted(string $productId): bool
    {
        return $this->wishlist()->where('products.id', $productId)->exists();
    }

    /**
     * Get the user who referred this user.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get users who were referred by this user.
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Get the count of unread notifications for the user
     *
     * @return int
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()
            ->wherePivot('read', false)
            ->count();
    }
}
