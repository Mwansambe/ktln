package com.jamexams.app.util

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.firstOrNull
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "jamexams_prefs")

/**
 * TokenManager - Securely stores and retrieves auth token using DataStore.
 * DataStore is the modern replacement for SharedPreferences.
 */
@Singleton
class TokenManager @Inject constructor(
    @ApplicationContext private val context: Context
) {
    companion object {
        private val TOKEN_KEY       = stringPreferencesKey("auth_token")
        private val USER_ID_KEY     = stringPreferencesKey("user_id")
        private val USER_NAME_KEY   = stringPreferencesKey("user_name")
        private val USER_EMAIL_KEY  = stringPreferencesKey("user_email")
        private val USER_ROLE_KEY   = stringPreferencesKey("user_role")
        private val FCM_TOKEN_KEY   = stringPreferencesKey("fcm_token")
    }

    val tokenFlow: Flow<String?> = context.dataStore.data.map { it[TOKEN_KEY] }

    suspend fun getToken(): String? = context.dataStore.data.map { it[TOKEN_KEY] }.firstOrNull()

    suspend fun saveToken(token: String) {
        context.dataStore.edit { it[TOKEN_KEY] = token }
    }

    suspend fun saveUserInfo(id: String, name: String, email: String, role: String) {
        context.dataStore.edit {
            it[USER_ID_KEY]    = id
            it[USER_NAME_KEY]  = name
            it[USER_EMAIL_KEY] = email
            it[USER_ROLE_KEY]  = role
        }
    }

    suspend fun getUserName(): String? = context.dataStore.data.map { it[USER_NAME_KEY] }.firstOrNull()
    suspend fun getUserEmail(): String? = context.dataStore.data.map { it[USER_EMAIL_KEY] }.firstOrNull()

    suspend fun saveFcmToken(fcmToken: String) {
        context.dataStore.edit { it[FCM_TOKEN_KEY] = fcmToken }
    }

    suspend fun getFcmToken(): String? = context.dataStore.data.map { it[FCM_TOKEN_KEY] }.firstOrNull()

    suspend fun clearAll() {
        context.dataStore.edit { it.clear() }
    }

    suspend fun isLoggedIn(): Boolean = getToken() != null
}
