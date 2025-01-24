<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id', 
        'sender_id', 
        'content', 
        'is_read',
        'user_id_1',
        'user_id_2',
        'last_message_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::random(30);
            }
        });
    }

    // Message-specific relationships and methods
    public function parentConversation()
    {
        return $this->belongsTo(Message::class, 'conversation_id', 'id')
                    ->whereNull('conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Content encryption/decryption
    public function getContentAttribute($value)
    {
        if (!$value) return null;
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt message: ' . $e->getMessage());
            return 'Error: Unable to decrypt message';
        }
    }

    public function setContentAttribute($value)
    {
        if (!$value) {
            $this->attributes['content'] = null;
            return;
        }

        try {
            $this->attributes['content'] = Crypt::encryptString($value);
        } catch (\Exception $e) {
            Log::error('Failed to encrypt message: ' . $e->getMessage());
            throw new \Exception('Failed to encrypt message. Please try again.');
        }
    }

    // Conversation-specific relationships and methods
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')
                    ->whereNotNull('conversation_id');
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

    // Scope for conversations
    public static function conversation()
    {
        return static::query()->whereNull('conversation_id');
    }

    // Scope for regular messages
    public function scopeRegularMessage($query)
    {
        return $query->whereNotNull('conversation_id');
    }

    // Helper method to determine if this is a conversation record
    public function isConversation()
    {
        return $this->conversation_id === null;
    }

    // Helper method to create a new conversation
    public static function createConversation($userId1, $userId2)
    {
        $conversation = new static();
        $conversation->user_id_1 = $userId1;
        $conversation->user_id_2 = $userId2;
        $conversation->last_message_at = now();
        $conversation->save();

        return $conversation;
    }

    // Helper method to find a conversation between two users
    public static function findConversation($userId1, $userId2)
    {
        return static::conversation()
            ->where(function ($query) use ($userId1, $userId2) {
                $query->where('user_id_1', $userId1)
                      ->where('user_id_2', $userId2);
            })
            ->orWhere(function ($query) use ($userId1, $userId2) {
                $query->where('user_id_1', $userId2)
                      ->where('user_id_2', $userId1);
            })
            ->first();
    }
}