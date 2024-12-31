<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['conversation_id', 'sender_id', 'content', 'is_read'];

    protected $dates = ['deleted_at'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getContentAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt message: ' . $e->getMessage());
            return 'Error: Unable to decrypt message';
        }
    }

    public function setContentAttribute($value)
    {
        try {
            $this->attributes['content'] = Crypt::encryptString($value);
        } catch (\Exception $e) {
            Log::error('Failed to encrypt message: ' . $e->getMessage());
            throw new \Exception('Failed to encrypt message. Please try again.');
        }
    }
}
