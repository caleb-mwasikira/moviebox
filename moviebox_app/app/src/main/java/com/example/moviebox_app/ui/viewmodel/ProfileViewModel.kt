package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.auth.AuthRepository
import com.example.moviebox_app.data.auth.UserProfile
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class ProfileViewModel : ViewModel() {

    private val _state = MutableStateFlow<UiState<UserProfile>>(UiState.Loading)
    val state: StateFlow<UiState<UserProfile>> = _state.asStateFlow()

    private val repo = AuthRepository()

    init { load() }

    fun load() {
        viewModelScope.launch {
            _state.value = UiState.Loading
            try {
                _state.value = UiState.Success(repo.me())
            } catch (e: Exception) {
                _state.value = UiState.Error(e.message ?: "Failed to load profile")
            }
        }
    }

    fun logout(onDone: () -> Unit) {
        viewModelScope.launch {
            try { repo.logout() } catch (_: Exception) { }
            MovieBoxApplication.sessionManager.clearSession()
            onDone()
        }
    }
}
