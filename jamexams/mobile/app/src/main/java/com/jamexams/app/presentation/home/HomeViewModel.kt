package com.jamexams.app.presentation.home

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.repository.AuthRepository
import com.jamexams.app.util.TokenManager
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import javax.inject.Inject

data class HomeUiState(
    val userName: String?     = null,
    val daysRemaining: Int    = 0,
    val isLoggedOut: Boolean  = false
)

@HiltViewModel
class HomeViewModel @Inject constructor(
    private val authRepository: AuthRepository,
    private val tokenManager: TokenManager
) : ViewModel() {

    private val _uiState = MutableStateFlow(HomeUiState())
    val uiState: StateFlow<HomeUiState> = _uiState.asStateFlow()

    init {
        loadProfile()
    }

    private fun loadProfile() {
        viewModelScope.launch {
            val name = tokenManager.getUserName()
            _uiState.update { it.copy(userName = name) }

            when (val result = authRepository.getProfile()) {
                is com.jamexams.app.domain.model.Result.Success -> {
                    _uiState.update {
                        it.copy(
                            userName      = result.data.name,
                            daysRemaining = result.data.daysRemaining
                        )
                    }
                }
                else -> Unit
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            authRepository.logout()
            _uiState.update { it.copy(isLoggedOut = true) }
        }
    }
}
