<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'user_id_1', 'user_id_2', 'last_message_at'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::random(30);
        });
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function hasReachedMessageLimit()
    {
        return $this->messages()->count() >= 40;
    }
}
