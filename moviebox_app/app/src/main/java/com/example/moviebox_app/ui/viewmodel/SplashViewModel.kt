package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class SplashViewModel : ViewModel() {

    private val _featured = MutableStateFlow<ApiMovie?>(null)
    val featured: StateFlow<ApiMovie?> = _featured.asStateFlow()

    init {
        viewModelScope.launch {
            try {
                val movies = ApiRepository().getMovies(page = 1)
                _featured.value = movies.firstOrNull { it.featured } ?: movies.firstOrNull()
            } catch (_: Exception) { }
        }
    }
}
