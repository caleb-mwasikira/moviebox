package com.example.moviebox_app.data

import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiSeries
import com.example.moviebox_app.data.db.AppDatabase
import com.example.moviebox_app.data.db.MovieEntity
import com.example.moviebox_app.data.db.SeriesEntity

class LocalRepository(private val db: AppDatabase) {

    suspend fun getMovies(): List<ApiMovie> = db.movieDao().getAll().map { it.toApi() }

    suspend fun saveMovies(movies: List<ApiMovie>) {
        db.movieDao().deleteAll()
        db.movieDao().upsertAll(movies.map { it.toEntity() })
    }

    suspend fun getSeries(): List<ApiSeries> = db.seriesDao().getAll().map { it.toApi() }

    suspend fun saveSeries(series: List<ApiSeries>) {
        db.seriesDao().deleteAll()
        db.seriesDao().upsertAll(series.map { it.toEntity() })
    }
}

private fun ApiMovie.toEntity() = MovieEntity(
    uuid        = uuid,
    title       = title,
    description = description,
    genre       = genre,
    year        = year,
    duration    = duration,
    isFree      = isFree,
    featured    = featured,
    thumbnailUrl = thumbnailUrl,
    hasStream   = hasStream,
    streamingUrl = streamingUrl,
    trailerUrl  = trailerUrl,
    createdAt   = createdAt,
)

private fun MovieEntity.toApi() = ApiMovie(
    uuid        = uuid,
    title       = title,
    description = description,
    genre       = genre,
    year        = year,
    duration    = duration,
    isFree      = isFree,
    featured    = featured,
    thumbnailUrl = thumbnailUrl,
    hasStream   = hasStream,
    streamingUrl = streamingUrl,
    trailerUrl  = trailerUrl,
    createdAt   = createdAt,
)

private fun ApiSeries.toEntity() = SeriesEntity(
    uuid        = uuid,
    title       = title,
    description = description,
    genre       = genre,
    year        = year,
    featured    = featured,
    comingSoon  = comingSoon,
    thumbnailUrl = thumbnailUrl,
    trailerUrl  = trailerUrl,
    createdAt   = createdAt,
)

private fun SeriesEntity.toApi() = ApiSeries(
    uuid        = uuid,
    title       = title,
    description = description,
    genre       = genre,
    year        = year,
    featured    = featured,
    comingSoon  = comingSoon,
    thumbnailUrl = thumbnailUrl,
    trailerUrl  = trailerUrl,
    seasons     = null,
    createdAt   = createdAt,
)
