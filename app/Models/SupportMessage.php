<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class SupportMessage extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'support_request_id',
        'user_id',
        'message',
        'is_admin_reply'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Generate UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Sanitize message content
            $model->message = self::sanitizeMessage($model->message);
        });

        static::updating(function (Model $model) {
            // Also sanitize on updates
            $model->message = self::sanitizeMessage($model->message);
        });
    }

    public function supportRequest()
    {
        return $this->belongsTo(SupportRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sanitize the message content to prevent XSS and other injection attacks
     *
     * @param string $message
     * @return string
     */
    private static function sanitizeMessage(string $message): string
    {
        // Remove HTML and PHP tags
        $message = strip_tags($message);
        
        // Convert special characters to HTML entities
        $message = htmlspecialchars($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove any null bytes
        $message = str_replace(chr(0), '', $message);
        
        // Remove any potentially dangerous Unicode characters
        $message = preg_replace('/[^\P{C}\n]+/u', '', $message);
        
        // Normalize line endings
        $message = str_replace(["\r\n", "\r"], "\n", $message);
        
        // Remove multiple consecutive newlines
        $message = preg_replace("/\n{3,}/", "\n\n", $message);
        
        // Trim whitespace
        $message = trim($message);

        return $message;
    }

    /**
     * Get the sanitized message for display
     *
     * @return Illuminate\Support\HtmlString
     */
    public function getFormattedMessageAttribute(): HtmlString
    {
        // Convert newlines to <br> tags for display while maintaining security
        $message = nl2br($this->message);
        return new HtmlString($message);
    }
}