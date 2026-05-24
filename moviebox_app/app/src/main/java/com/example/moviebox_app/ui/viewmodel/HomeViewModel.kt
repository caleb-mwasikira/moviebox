package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
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

data class HomeData(
    val featured: ApiMovie?,
    val trending: List<ApiMovie>,
    val nowShowing: List<ApiMovie>,
    val popular: List<ApiMovie>,
    val series: List<ApiSeries>,
)

class HomeViewModel(private val repo: ApiRepository = ApiRepository()) : ViewModel() {

    private val _state = MutableStateFlow<UiState<HomeData>>(UiState.Loading)
    val state: StateFlow<UiState<HomeData>> = _state.asStateFlow()

    private val exceptionHandler = CoroutineExceptionHandler { _, throwable ->
        _state.value = UiState.Error(throwable.message ?: "Failed to load content")
    }

    init { load() }

    fun load() {
        viewModelScope.launch(exceptionHandler) {
            _state.value = UiState.Loading
            val movies = repo.getMovies(page = 1)
            val series = repo.getSeries(page = 1)

            val featured = movies.firstOrNull { it.featured } ?: movies.firstOrNull()
            val rest     = movies.filter { it.uuid != featured?.uuid }

            _state.value = UiState.Success(
                HomeData(
                    featured   = featured,
                    trending   = rest.take(5),
                    nowShowing = rest.drop(5).take(4),
                    popular    = rest.drop(9),
                    series     = series,
                )
            )
        }
    }
}
