package com.example.moviebox_app.navigation

sealed class Screen(val route: String) {
    data object Splash       : Screen("splash")
    data object Home         : Screen("home")
    data object Search       : Screen("search")
    data object Downloads    : Screen("downloads")
    data object Profile      : Screen("profile")
    data object MovieDetail  : Screen("movie/{uuid}") {
        fun createRoute(uuid: String) = "movie/$uuid"
    }
    data object SeriesDetail : Screen("series/{uuid}") {
        fun createRoute(uuid: String) = "series/$uuid"
    }
}
