<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\User;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * NotificationService
 * Sends Firebase Cloud Messaging (FCM) push notifications to active users.
 */
class NotificationService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    /**
     * Send notification when new exam is uploaded.
     */
    public function sendExamUploadedNotification(Exam $exam): void
    {
        $title = "New Exam Available! 📚";
        $body = "{$exam->title} - {$exam->subject->name} has been uploaded.";

        $data = [
            'type'    => 'NEW_EXAM',
            'exam_id' => (string) $exam->id,
        ];

        $this->sendToAllActiveUsers($title, $body, $data, $exam->id);
    }

    /**
     * Send custom notification to all active users.
     */
    public function sendToAllActiveUsers(string $title, string $body, array $data = [], ?int $examId = null): void
    {
        // Get FCM tokens of all active users
        $tokens = User::active()
                      ->whereNotNull('fcm_token')
                      ->pluck('fcm_token')
                      ->toArray();

        if (empty($tokens)) {
            Log::info('FCM: No active users with FCM tokens to notify.');
            return;
        }

        $recipientCount = 0;

        // FCM allows max 500 tokens per batch request
        $batches = array_chunk($tokens, 500);

        foreach ($batches as $batch) {
            $success = $this->sendFcmBatch($title, $body, $batch, $data);
            if ($success) {
                $recipientCount += count($batch);
            }
        }

        // Store notification history
        PushNotification::create([
            'title'           => $title,
            'body'            => $body,
            'exam_id'         => $examId,
            'sent_by'         => auth()->id(),
            'recipient_count' => $recipientCount,
            'sent_at'         => now(),
            'data'            => $data,
        ]);
    }

    /**
     * Send FCM batch request.
     */
    private function sendFcmBatch(string $title, string $body, array $tokens, array $data = []): bool
    {
        $serverKey = config('services.fcm.server_key');

        if (!$serverKey) {
            Log::warning('FCM: Server key not configured.');
            return false;
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body'  => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ])->post($this->fcmUrl, $payload);

        if (!$response->successful()) {
            Log::error('FCM send failed: ' . $response->body());
            return false;
        }

        return true;
    }
}
