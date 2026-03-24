<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Model
 * Stores push notification history sent to users.
 */
class PushNotification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications';

    protected $fillable = [
        'title', 'body', 'exam_id', 'sent_by',
        'recipient_count', 'sent_at', 'data',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'data' => 'array',
        'recipient_count' => 'integer',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
