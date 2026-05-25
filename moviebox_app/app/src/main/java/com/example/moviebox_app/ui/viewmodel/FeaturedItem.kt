package com.example.moviebox_app.ui.viewmodel

import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiSeries

sealed interface FeaturedItem {
    data class Movie(val movie: ApiMovie)    : FeaturedItem
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
