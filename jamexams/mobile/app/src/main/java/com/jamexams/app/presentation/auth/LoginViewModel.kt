package com.jamexams.app.presentation.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.google.firebase.messaging.FirebaseMessaging
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.repository.AuthRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import kotlinx.coroutines.tasks.await
import javax.inject.Inject

/**
 * LoginViewModel - Manages login UI state using MVVM + StateFlow.
 *
 * States: Idle → Loading → Success/Error
 * On error codes ACCESS_EXPIRED / ACCOUNT_NOT_ACTIVATED → show specific messages.
 */

sealed class LoginUiState {
    object Idle    : LoginUiState()
    object Loading : LoginUiState()
    data class Success(val message: String) : LoginUiState()
    data class Error(val message: String, val code: String? = null) : LoginUiState()
}

@HiltViewModel
class LoginViewModel @Inject constructor(
    private val authRepository: AuthRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow<LoginUiState>(LoginUiState.Idle)
    val uiState: StateFlow<LoginUiState> = _uiState.asStateFlow()

    private val _email    = MutableStateFlow("")
    private val _password = MutableStateFlow("")

    val email: StateFlow<String>    = _email.asStateFlow()
    val password: StateFlow<String> = _password.asStateFlow()

    fun onEmailChange(value: String)    { _email.value    = value }
    fun onPasswordChange(value: String) { _password.value = value }

    fun login() {
        if (_email.value.isBlank() || _password.value.isBlank()) {
            _uiState.value = LoginUiState.Error("Please enter email and password.")
            return
        }

        viewModelScope.launch {
            _uiState.value = LoginUiState.Loading

            // Get FCM token for push notifications
            val fcmToken = try {
                FirebaseMessaging.getInstance().token.await()
            } catch (e: Exception) {
                null
            }

            when (val result = authRepository.login(_email.value.trim(), _password.value, fcmToken)) {
                is Result.Success -> {
                    _uiState.value = LoginUiState.Success("Welcome back!")
                }
                is Result.Error -> {
                    _uiState.value = LoginUiState.Error(result.message, result.code)
                }
                else -> {}
            }
        }
    }

    fun resetState() {
        _uiState.value = LoginUiState.Idle
    }
}
