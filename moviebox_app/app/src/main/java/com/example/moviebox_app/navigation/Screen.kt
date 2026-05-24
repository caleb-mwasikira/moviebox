package com.example.moviebox_app.navigation

sealed class Screen(val route: String) {
    data object Splash         : Screen("splash")
    data object Login          : Screen("login")
    data object Register       : Screen("register")
    data object ForgotPassword : Screen("forgot-password")
    data object Home           : Screen("home")
    data object Search         : Screen("search")
    data object Downloads      : Screen("downloads")
    data object Browse         : Screen("browse/{type}") {
        fun movies()  = "browse/movies"
        fun series()  = "browse/series"
    }
    data object Profile        : Screen("profile")
    data object MovieDetail    : Screen("movie/{uuid}") {
        fun createRoute(uuid: String) = "movie/$uuid"
    }
    data object SeriesDetail   : Screen("series/{uuid}") {
        fun createRoute(uuid: String) = "series/$uuid"
    }
    data object VideoPlayer    : Screen("player/{url}") {
        fun createRoute(url: String) = "player/${android.net.Uri.encode(url)}"
    }
}
