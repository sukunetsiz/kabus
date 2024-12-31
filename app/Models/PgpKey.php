<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PgpKey extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'public_key',
        'verified',
        'two_fa_enabled',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'two_fa_enabled' => 'boolean',
    ];

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
     * Get the user that owns the PGP key.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
