package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSeries
import kotlinx.coroutines.CoroutineExceptionHandler
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class SeriesDetailViewModel(
    private val uuid: String,
    private val repo: ApiRepository = ApiRepository(),
) : ViewModel() {

    private val _state = MutableStateFlow<UiState<ApiSeries>>(UiState.Loading)
    val state: StateFlow<UiState<ApiSeries>> = _state.asStateFlow()

    private val exceptionHandler = CoroutineExceptionHandler { _, throwable ->
        _state.value = UiState.Error(throwable.message ?: "Failed to load series")
    }

    init { load() }

    fun load() {
        viewModelScope.launch(exceptionHandler) {
            _state.value = UiState.Loading
            _state.value = UiState.Success(repo.getSeriesDetail(uuid))
        }
    }

    class Factory(private val uuid: String) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T =
            SeriesDetailViewModel(uuid) as T
    }
}
