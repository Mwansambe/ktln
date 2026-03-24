package com.jamexams.app.service

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import com.jamexams.app.MainActivity
import com.jamexams.app.R
import com.jamexams.app.util.TokenManager
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import javax.inject.Inject

/**
 * JamExamsFcmService - Handles incoming Firebase Cloud Messaging push notifications.
 *
 * When admin uploads a new exam, this service:
 * 1. Receives the FCM message
 * 2. Displays a system notification
 * 3. When tapped, opens the specific exam screen
 */
@AndroidEntryPoint
class JamExamsFcmService : FirebaseMessagingService() {

    @Inject
    lateinit var tokenManager: TokenManager

    companion object {
        const val CHANNEL_ID   = "jamexams_notifications"
        const val CHANNEL_NAME = "New Exams"
    }

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        // Save new FCM token locally; sync to server when user is logged in
        CoroutineScope(Dispatchers.IO).launch {
            tokenManager.saveFcmToken(token)
        }
    }

    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)

        val title   = message.notification?.title ?: "New Exam Available"
        val body    = message.notification?.body  ?: "A new exam has been uploaded."
        val examId  = message.data["exam_id"]?.toIntOrNull()

        showNotification(title, body, examId)
    }

    private fun showNotification(title: String, body: String, examId: Int?) {
        createNotificationChannel()

        // Intent to open app (optionally to a specific exam)
        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
            examId?.let { putExtra("exam_id", it) }
        }

        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.drawable.ic_notification)
            .setContentTitle(title)
            .setContentText(body)
            .setStyle(NotificationCompat.BigTextStyle().bigText(body))
            .setAutoCancel(true)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setContentIntent(pendingIntent)
            .build()

        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(System.currentTimeMillis().toInt(), notification)
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Notifications for new exam paper uploads"
            }
            val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            manager.createNotificationChannel(channel)
        }
    }
}
