<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'ticket_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            if (empty($model->ticket_id)) {
                $model->ticket_id = self::generateUniqueTicketId();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(SupportMessage::class)->latestOfMany();
    }

    public static function generateUniqueTicketId()
    {
        do {
            $ticketId = Str::random(30);
        } while (static::where('ticket_id', $ticketId)->exists());

        return $ticketId;
    }

    public function getRouteKeyName()
    {
        return 'ticket_id';
    }
}
