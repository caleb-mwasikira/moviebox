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

sealed interface FeaturedItem {
    data class Movie(val movie: ApiMovie)   : FeaturedItem
    data class Series(val series: ApiSeries) : FeaturedItem

    val uuid: String get() = when (this) {
        is Movie  -> movie.uuid
        is Series -> series.uuid
    }
    val title: String get() = when (this) {
        is Movie  -> movie.title
        is Series -> series.title
    }
    val thumbnailUrl: String? get() = when (this) {
        is Movie  -> movie.thumbnailUrl
        is Series -> series.thumbnailUrl
    }
    val year: Int? get() = when (this) {
        is Movie  -> movie.year
        is Series -> series.year
    }
    fun genreList(): List<String> = when (this) {
        is Movie  -> movie.genreList()
        is Series -> series.genreList()
    }
}

data class HomeData(
    val featured: FeaturedItem?,
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

            // Prefer an explicitly featured movie; fall back to first series, then first movie
            val featured: FeaturedItem? = when {
                movies.any { it.featured } -> FeaturedItem.Movie(movies.first { it.featured })
                series.isNotEmpty()        -> FeaturedItem.Series(series.first())
                movies.isNotEmpty()        -> FeaturedItem.Movie(movies.first())
                else                       -> null
            }

            // Exclude the featured movie from the scrollable rows (series are unaffected)
            val excludeUuid = (featured as? FeaturedItem.Movie)?.movie?.uuid
            val rest = if (excludeUuid != null) movies.filter { it.uuid != excludeUuid } else movies

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
