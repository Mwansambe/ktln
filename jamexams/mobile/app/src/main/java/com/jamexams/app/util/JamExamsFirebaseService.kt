package com.jamexams.app.util

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
import com.jamexams.app.data.local.TokenDataStore
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import javax.inject.Inject

/**
 * JamExamsFirebaseService
 * Handles incoming FCM push notifications.
 * When a new exam is uploaded, shows a notification that opens the exam screen.
 */
@AndroidEntryPoint
class JamExamsFirebaseService : FirebaseMessagingService() {

    @Inject lateinit var tokenDataStore: TokenDataStore

    companion object {
        const val CHANNEL_ID   = "jamexams_notifications"
        const val CHANNEL_NAME = "JamExams Updates"
        const val EXTRA_EXAM_ID = "exam_id"
    }

    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)

        val title  = message.notification?.title ?: "JamExams"
        val body   = message.notification?.body  ?: "New update available"
        val examId = message.data["exam_id"]

        showNotification(title, body, examId?.toIntOrNull())
    }

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        // Store new FCM token and sync with server
        CoroutineScope(Dispatchers.IO).launch {
            tokenDataStore.saveFcmToken(token)
            // Note: actual API sync happens on next login / app open
        }
    }

    private fun showNotification(title: String, body: String, examId: Int?) {
        createNotificationChannel()

        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
            examId?.let { putExtra(EXTRA_EXAM_ID, it) }
        }

        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE,
        )

        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.drawable.ic_notification) // Add a notification icon to your res/drawable
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
            val channel = NotificationChannel(CHANNEL_ID, CHANNEL_NAME, NotificationManager.IMPORTANCE_HIGH)
            val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            manager.createNotificationChannel(channel)
        }
    }
}
