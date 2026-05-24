package com.example.moviebox_app.data.api

import retrofit2.http.GET
import retrofit2.http.Path
import retrofit2.http.Query

interface ApiService {

    @GET("movies")
    suspend fun getMovies(
        @Query("page")  page: Int    = 1,
        @Query("q")     query: String? = null,
        @Query("genre") genre: String? = null,
    ): ApiMovieList

    @GET("movies/{uuid}")
    suspend fun getMovie(@Path("uuid") uuid: String): ApiMovieDetail

    @GET("series")
    suspend fun getSeries(@Query("page") page: Int = 1): ApiSeriesList

    @GET("series/{uuid}")
    suspend fun getSeriesDetail(@Path("uuid") uuid: String): ApiSeriesDetail
}
