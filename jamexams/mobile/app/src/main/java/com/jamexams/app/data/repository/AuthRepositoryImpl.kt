package com.jamexams.app.data.repository

import com.jamexams.app.data.api.ApiService
import com.jamexams.app.data.local.TokenDataStore
import com.jamexams.app.data.model.LoginRequest
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.User
import com.jamexams.app.domain.repository.AuthRepository
import kotlinx.coroutines.flow.first
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class AuthRepositoryImpl @Inject constructor(
    private val api: ApiService,
    private val tokenDataStore: TokenDataStore
) : AuthRepository {

    override suspend fun login(email: String, password: String, fcmToken: String?): Result<Pair<String, User>> {
        return try {
            val response = api.login(LoginRequest(email, password, fcmToken))
            if (response.isSuccessful && response.body()?.status == "success") {
                val authData = response.body()!!.data!!
                val user = authData.user

                // Persist token and user info
                tokenDataStore.saveAuthData(
                    token     = authData.token,
                    userId    = user.id,
                    name      = user.name,
                    email     = user.email,
                    expiresAt = user.expiresAt
                )

                Result.Success(Pair(authData.token, user.toDomain()))
            } else {
                val body = response.body()
                val code = body?.code
                val msg  = body?.message ?: "Login failed"
                Result.Error(msg, code)
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error. Please check your connection.")
        }
    }

    override suspend fun logout(): Result<Unit> {
        return try {
            api.logout()
            tokenDataStore.clearAll()
            Result.Success(Unit)
        } catch (e: Exception) {
            tokenDataStore.clearAll()
            Result.Success(Unit)
        }
    }

    override suspend fun getProfile(): Result<User> {
        return try {
            val response = api.getProfile()
            if (response.isSuccessful && response.body()?.status == "success") {
                Result.Success(response.body()!!.data!!.toDomain())
            } else {
                val body = response.body()
                Result.Error(body?.message ?: "Failed to get profile", body?.code)
            }
        } catch (e: Exception) {
            Result.Error(e.message ?: "Network error")
        }
    }

    override suspend fun updateFcmToken(fcmToken: String): Result<Unit> {
        return try {
            api.updateFcmToken(com.jamexams.app.data.model.FcmTokenRequest(fcmToken))
            Result.Success(Unit)
        } catch (e: Exception) {
            Result.Error(e.message ?: "Failed to update FCM token")
        }
    }

    override suspend fun isLoggedIn(): Boolean {
        return tokenDataStore.token.first() != null
    }

    override suspend fun clearSession() {
        tokenDataStore.clearAll()
    }
}

// Extension: Map data model → domain model
fun com.jamexams.app.data.model.UserData.toDomain() = User(
    id            = id,
    name          = name,
    email         = email,
    phone         = phone,
    avatar        = avatar,
    isActive      = isActive,
    activatedAt   = activatedAt,
    expiresAt     = expiresAt,
    daysRemaining = daysRemaining,
    roles         = roles,
)
