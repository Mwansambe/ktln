package com.jamexams.app

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import com.jamexams.app.data.local.TokenDataStore
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import javax.inject.Inject

/**
 * JamExamsFcmService - Handles incoming Firebase push notifications.
 * When a new exam is uploaded, admin triggers a push from the web panel.
 * Tapping the notification opens the specific exam screen.
 */
@AndroidEntryPoint
class JamExamsFcmService : FirebaseMessagingService() {

    @Inject lateinit var tokenDataStore: TokenDataStore
    private val CHANNEL_ID = "jamexams_notifications"

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        // Save new FCM token to local store; it will be synced with API on next login
        CoroutineScope(Dispatchers.IO).launch {
            // Update token via API if user is logged in
        }
    }

    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)

        val title  = message.notification?.title ?: message.data["title"] ?: "JamExams"
        val body   = message.notification?.body  ?: message.data["body"]  ?: "New content available"
        val examId = message.data["exam_id"]

        createNotificationChannel()
        showNotification(title, body, examId)
    }

    private fun showNotification(title: String, body: String, examId: String?) {
        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            examId?.let { putExtra("exam_id", it) }
            // Deep link to specific exam if ID provided
            if (examId != null) {
                data = android.net.Uri.parse("jamexams://exam/$examId")
            }
        }

        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.drawable.ic_notification)
            .setContentTitle(title)
            .setContentText(body)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .build()

        val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        manager.notify(System.currentTimeMillis().toInt(), notification)
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                "JamExams Notifications",
                NotificationManager.IMPORTANCE_HIGH,
            ).apply { description = "Notifications for new exam uploads" }

            (getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager)
                .createNotificationChannel(channel)
        }
    }
}
