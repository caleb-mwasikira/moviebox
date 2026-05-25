package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSeries
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class SplashViewModel : ViewModel() {

    private val localRepo = MovieBoxApplication.localRepo

    private val _featured = MutableStateFlow<FeaturedItem?>(null)
    val featured: StateFlow<FeaturedItem?> = _featured.asStateFlow()

    init {
        viewModelScope.launch {
            val cachedMovies = localRepo.getMovies()
            val cachedSeries = localRepo.getSeries()
            _featured.value = buildFeaturedItem(cachedMovies, cachedSeries)

            try {
                val repo = ApiRepository()
                val movies = repo.getMovies(page = 1)
                val series = repo.getSeries(page = 1)
                localRepo.saveMovies(movies)
                localRepo.saveSeries(series)
                _featured.value = buildFeaturedItem(movies, series)
            } catch (_: Exception) { }
        }
    }

    private fun buildFeaturedItem(movies: List<ApiMovie>, series: List<ApiSeries>): FeaturedItem? =
        when {
            movies.any { it.featured } -> FeaturedItem.Movie(movies.first { it.featured })
            series.isNotEmpty()        -> FeaturedItem.Series(series.first())
            movies.isNotEmpty()        -> FeaturedItem.Movie(movies.first())
            else                       -> null
        }
}
