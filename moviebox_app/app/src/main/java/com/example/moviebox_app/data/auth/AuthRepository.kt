package com.example.moviebox_app.data.auth

import com.example.moviebox_app.data.api.ApiClient

class AuthRepository {

    private val service = ApiClient.service

    suspend fun login(email: String, password: String): AuthResponse =
        service.login(LoginRequest(email, password))

    suspend fun register(
        name: String,
        email: String,
        password: String,
        passwordConfirmation: String,
    ): AuthResponse = service.register(RegisterRequest(name, email, password, passwordConfirmation))

    suspend fun me(): UserProfile = service.me()

    suspend fun logout() = service.logout()

    suspend fun forgotPassword(email: String): MessageResponse =
        service.forgotPassword(ForgotPasswordRequest(email))

    suspend fun resetPassword(
        email: String,
        otp: String,
        password: String,
        passwordConfirmation: String,
    ): AuthResponse = service.resetPassword(ResetPasswordRequest(email, otp, password, passwordConfirmation))
}
