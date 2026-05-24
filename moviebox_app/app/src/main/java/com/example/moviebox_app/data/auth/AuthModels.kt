package com.example.moviebox_app.data.auth

import com.google.gson.annotations.SerializedName

data class LoginRequest(
    val email: String,
    val password: String,
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    @SerializedName("password_confirmation") val passwordConfirmation: String,
)

data class ForgotPasswordRequest(
    val email: String,
)

data class ResetPasswordRequest(
    val email: String,
    val otp: String,
    val password: String,
    @SerializedName("password_confirmation") val passwordConfirmation: String,
)

data class ApiUser(
    val id: Int,
    val name: String,
    val email: String,
    @SerializedName("avatar_url") val avatarUrl: String?,
)

data class AuthResponse(
    val token: String,
    val user: ApiUser,
)

data class ApiSubscription(
    val `package`: String?,
    val status: String,
    @SerializedName("expires_at") val expiresAt: String?,
)

data class UserProfile(
    val id: Int,
    val name: String,
    val email: String,
    @SerializedName("avatar_url") val avatarUrl: String?,
    val subscription: ApiSubscription?,
)

data class MessageResponse(
    val message: String,
)
