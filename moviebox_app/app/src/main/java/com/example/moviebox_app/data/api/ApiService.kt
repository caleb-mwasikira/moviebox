package com.example.moviebox_app.data.api

import com.example.moviebox_app.data.auth.AuthResponse
import com.example.moviebox_app.data.auth.ForgotPasswordRequest
import com.example.moviebox_app.data.auth.LoginRequest
import com.example.moviebox_app.data.auth.MessageResponse
import com.example.moviebox_app.data.auth.RegisterRequest
import com.example.moviebox_app.data.auth.ResetPasswordRequest
import com.example.moviebox_app.data.auth.UserProfile
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query

interface ApiService {

    @POST("auth/register")
    suspend fun register(@Body body: RegisterRequest): AuthResponse

    @POST("auth/login")
    suspend fun login(@Body body: LoginRequest): AuthResponse

    @POST("auth/logout")
    suspend fun logout(): MessageResponse

    @GET("auth/me")
    suspend fun me(): UserProfile

    @POST("auth/forgot-password")
    suspend fun forgotPassword(@Body body: ForgotPasswordRequest): MessageResponse

    @POST("auth/reset-password")
    suspend fun resetPassword(@Body body: ResetPasswordRequest): AuthResponse

    @GET("search")
    suspend fun search(
        @Query("q")     query: String? = null,
        @Query("genre") genre: String? = null,
    ): SearchResult

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

    @GET("subscriptions/packages")
    suspend fun getSubscriptionPackages(): ApiPackageList

    @GET("subscriptions/current")
    suspend fun getCurrentSubscription(): ApiCurrentSubWrapper

    @POST("subscriptions/initiate")
    suspend fun initiateSubscription(@Body body: SubscriptionInitiateRequest): SubscriptionInitiateResponse

    @GET("subscriptions/status")
    suspend fun getSubscriptionStatus(@Query("checkout_request_id") checkoutRequestId: String): SubscriptionStatusResponse
}
