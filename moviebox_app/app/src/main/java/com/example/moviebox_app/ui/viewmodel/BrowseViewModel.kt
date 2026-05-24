package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSeries
import kotlinx.coroutines.CoroutineExceptionHandler
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

sealed interface BrowseContent {
    data class Movies(val items: List<ApiMovie>)  : BrowseContent
    data class Series(val items: List<ApiSeries>) : BrowseContent
}

class BrowseViewModel(
    val type: String,
    private val repo: ApiRepository = ApiRepository(),
) : ViewModel() {

    private val _state = MutableStateFlow<UiState<BrowseContent>>(UiState.Loading)
    val state: StateFlow<UiState<BrowseContent>> = _state.asStateFlow()

    private val exceptionHandler = CoroutineExceptionHandler { _, throwable ->
        _state.value = UiState.Error(throwable.message ?: "Failed to load content")
    }

    init { load() }

    fun load() {
        viewModelScope.launch(exceptionHandler) {
            _state.value = UiState.Loading
            _state.value = if (type == "series") {
                UiState.Success(BrowseContent.Series(repo.getSeries(page = 1)))
            } else {
                UiState.Success(BrowseContent.Movies(repo.getMovies(page = 1)))
            }
        }
    }

    companion object {
        fun factory(type: String): ViewModelProvider.Factory =
            object : ViewModelProvider.Factory {
                @Suppress("UNCHECKED_CAST")
                override fun <T : ViewModel> create(modelClass: Class<T>): T =
                    BrowseViewModel(type) as T
            }
    }
}
