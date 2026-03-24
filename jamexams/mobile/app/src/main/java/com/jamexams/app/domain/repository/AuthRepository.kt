package com.jamexams.app.domain.repository

import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.User

interface AuthRepository {
    suspend fun login(email: String, password: String, fcmToken: String?): Result<Pair<String, User>>
    suspend fun logout(): Result<Unit>
    suspend fun getProfile(): Result<User>
    suspend fun updateFcmToken(fcmToken: String): Result<Unit>
    suspend fun isLoggedIn(): Boolean
    suspend fun clearSession()
}
