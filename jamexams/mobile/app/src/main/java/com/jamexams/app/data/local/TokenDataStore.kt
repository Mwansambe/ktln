package com.jamexams.app.data.local

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "jamexams_prefs")

/**
 * TokenDataStore - Securely stores the Sanctum API token using Jetpack DataStore.
 * Also stores basic user info for offline display.
 */
@Singleton
class TokenDataStore @Inject constructor(
    @ApplicationContext private val context: Context
) {
    companion object {
        val TOKEN_KEY       = stringPreferencesKey("api_token")
        val USER_ID_KEY     = stringPreferencesKey("user_id")
        val USER_NAME_KEY   = stringPreferencesKey("user_name")
        val USER_EMAIL_KEY  = stringPreferencesKey("user_email")
        val EXPIRES_AT_KEY  = stringPreferencesKey("expires_at")
        val FCM_TOKEN_KEY   = stringPreferencesKey("fcm_token")
    }

    val token: Flow<String?> = context.dataStore.data.map { it[TOKEN_KEY] }
    val userId: Flow<String?> = context.dataStore.data.map { it[USER_ID_KEY] }
    val userName: Flow<String?> = context.dataStore.data.map { it[USER_NAME_KEY] }
    val userEmail: Flow<String?> = context.dataStore.data.map { it[USER_EMAIL_KEY] }
    val expiresAt: Flow<String?> = context.dataStore.data.map { it[EXPIRES_AT_KEY] }

    suspend fun saveAuthData(token: String, userId: String, name: String, email: String, expiresAt: String?) {
        context.dataStore.edit { prefs ->
            prefs[TOKEN_KEY]      = token
            prefs[USER_ID_KEY]    = userId
            prefs[USER_NAME_KEY]  = name
            prefs[USER_EMAIL_KEY] = email
            if (expiresAt != null) prefs[EXPIRES_AT_KEY] = expiresAt
        }
    }

    suspend fun saveFcmToken(fcmToken: String) {
        context.dataStore.edit { prefs -> prefs[FCM_TOKEN_KEY] = fcmToken }
    }

    suspend fun clearAll() {
        context.dataStore.edit { it.clear() }
    }
}
