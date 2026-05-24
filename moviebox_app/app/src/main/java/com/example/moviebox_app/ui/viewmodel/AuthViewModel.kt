package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.auth.ApiUser
import com.example.moviebox_app.data.auth.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

sealed interface AuthUiState {
    data object Idle    : AuthUiState
    data object Loading : AuthUiState
    data class  Success(val user: ApiUser) : AuthUiState
    data class  Error(val message: String) : AuthUiState
}

class AuthViewModel : ViewModel() {

    private val _state = MutableStateFlow<AuthUiState>(AuthUiState.Idle)
    val state: StateFlow<AuthUiState> = _state.asStateFlow()

    private val repo = AuthRepository()

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _state.value = AuthUiState.Loading
            try {
                val resp = repo.login(email.trim(), password)
                MovieBoxApplication.sessionManager.saveSession(resp.token, resp.user)
                _state.value = AuthUiState.Success(resp.user)
            } catch (e: Exception) {
                _state.value = AuthUiState.Error(e.message ?: "Login failed")
            }
        }
    }

    fun register(name: String, email: String, password: String, passwordConfirmation: String) {
        viewModelScope.launch {
            _state.value = AuthUiState.Loading
            try {
                val resp = repo.register(name.trim(), email.trim(), password, passwordConfirmation)
                MovieBoxApplication.sessionManager.saveSession(resp.token, resp.user)
                _state.value = AuthUiState.Success(resp.user)
            } catch (e: Exception) {
                _state.value = AuthUiState.Error(e.message ?: "Registration failed")
            }
        }
    }

    fun forgotPassword(email: String, onSent: () -> Unit) {
        viewModelScope.launch {
            _state.value = AuthUiState.Loading
            try {
                repo.forgotPassword(email.trim())
                _state.value = AuthUiState.Idle
                onSent()
            } catch (e: Exception) {
                _state.value = AuthUiState.Error(e.message ?: "Failed to send reset code")
            }
        }
    }

    fun resetPassword(
        email: String,
        otp: String,
        password: String,
        passwordConfirmation: String,
    ) {
        viewModelScope.launch {
            _state.value = AuthUiState.Loading
            try {
                val resp = repo.resetPassword(email.trim(), otp.trim(), password, passwordConfirmation)
                MovieBoxApplication.sessionManager.saveSession(resp.token, resp.user)
                _state.value = AuthUiState.Success(resp.user)
            } catch (e: Exception) {
                _state.value = AuthUiState.Error(e.message ?: "Password reset failed")
            }
        }
    }

    fun resetState() { _state.value = AuthUiState.Idle }
}
