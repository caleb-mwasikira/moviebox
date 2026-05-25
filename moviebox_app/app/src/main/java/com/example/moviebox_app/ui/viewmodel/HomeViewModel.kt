package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSeries
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class HomeData(
    val featured: FeaturedItem?,
    val trending: List<ApiMovie>,
    val nowShowing: List<ApiMovie>,
    val popular: List<ApiMovie>,
    val series: List<ApiSeries>,
)

class HomeViewModel : ViewModel() {

    private val repo      = ApiRepository()
    private val localRepo = MovieBoxApplication.localRepo

    private val _state        = MutableStateFlow<UiState<HomeData>>(UiState.Loading)
    val state: StateFlow<UiState<HomeData>> = _state.asStateFlow()

    private val _isRefreshing = MutableStateFlow(false)
    val isRefreshing: StateFlow<Boolean> = _isRefreshing.asStateFlow()

    init { load() }

    fun load() {
        viewModelScope.launch {
            val cachedMovies = localRepo.getMovies()
            val cachedSeries = localRepo.getSeries()
            if (cachedMovies.isNotEmpty() || cachedSeries.isNotEmpty()) {
                _state.value = UiState.Success(buildHomeData(cachedMovies, cachedSeries))
            } else {
                _state.value = UiState.Loading
            }
            fetchAndUpdate()
        }
    }

    fun refresh() {
        if (_isRefreshing.value) return
        viewModelScope.launch {
            _isRefreshing.value = true
            fetchAndUpdate()
            _isRefreshing.value = false
        }
    }

    private suspend fun fetchAndUpdate() {
        try {
            val movies = repo.getMovies(page = 1)
            val series = repo.getSeries(page = 1)
            localRepo.saveMovies(movies)
            localRepo.saveSeries(series)
            _state.value = UiState.Success(buildHomeData(movies, series))
        } catch (e: Exception) {
            if (_state.value !is UiState.Success) {
                _state.value = UiState.Error(e.message ?: "Failed to load content")
            }
        }
    }

    private fun buildHomeData(movies: List<ApiMovie>, series: List<ApiSeries>): HomeData {
        val featured: FeaturedItem? = when {
            movies.any { it.featured } -> FeaturedItem.Movie(movies.first { it.featured })
            series.isNotEmpty()        -> FeaturedItem.Series(series.first())
            movies.isNotEmpty()        -> FeaturedItem.Movie(movies.first())
            else                       -> null
        }
        val excludeUuid = (featured as? FeaturedItem.Movie)?.movie?.uuid
        val rest = if (excludeUuid != null) movies.filter { it.uuid != excludeUuid } else movies
        return HomeData(
            featured   = featured,
            trending   = rest.take(5),
            nowShowing = rest.drop(5).take(4),
            popular    = rest.drop(9),
            series     = series,
        )
    }
}
