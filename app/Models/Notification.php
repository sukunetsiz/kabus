<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Notification extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'message',
        'target_role',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type' => 'string',
    ];

    /**
     * The attributes that should be cast on the pivot.
     *
     * @var array
     */
    protected $pivotCasts = [
        'read' => 'boolean',
    ];

    /**
     * Validation rules for the model.
     */
    private static $rules = [
        'title' => 'required|string|min:3|max:255',
        'message' => 'required|string|min:10|max:5000',
        'target_role' => 'nullable|string|in:admin,vendor',
        'type' => 'required|string|in:bulk,message',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // Generate UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            // Validate the model
            $validator = Validator::make($model->toArray(), self::$rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Sanitize inputs
            $model->title = strip_tags($model->title);
            $model->message = strip_tags($model->message, '<p><br><strong><em><ul><li><ol>');
        });
    }

    /**
     * The users that belong to the notification.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('read');
    }

    /**
     * Get all users that should receive this notification based on target_role.
     */
    public function getTargetUsers()
    {
        try {
            $query = User::query();

            if ($this->target_role !== null) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', $this->target_role);
                });
            }

            return $query->select('id')->cursor();
        } catch (\Exception $e) {
            Log::error('Error getting target users for notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send the notification to target users.
     */
    public function sendToTargetUsers()
    {
        try {
            $users = $this->getTargetUsers();
            
            // Process users in chunks to prevent memory issues
            foreach ($users->chunk(1000) as $userChunk) {
                $userIds = $userChunk->pluck('id')->toArray();
                $this->users()->attach($userIds);
            }

            Log::info("Notification {$this->id} sent successfully to users");
        } catch (\Exception $e) {
            Log::error('Error sending notification to users: ' . $e->getMessage());
            throw $e;
        }
    }
}