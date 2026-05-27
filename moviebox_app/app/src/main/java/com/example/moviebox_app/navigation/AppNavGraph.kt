package com.example.moviebox_app.navigation

import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.WindowInsets
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.PlayerSession
import com.example.moviebox_app.data.api.ApiEpisode
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiSeries
import com.example.moviebox_app.data.resolveStreamingUrl
import com.example.moviebox_app.ui.components.BottomNavBar
import com.example.moviebox_app.ui.screens.BrowseScreen
import com.example.moviebox_app.ui.screens.DownloadsScreen
import com.example.moviebox_app.ui.screens.ForgotPasswordScreen
import com.example.moviebox_app.ui.screens.HomeScreen
import com.example.moviebox_app.ui.screens.LoginScreen
import com.example.moviebox_app.ui.screens.MovieDetailScreen
import com.example.moviebox_app.ui.screens.ProfileScreen
import com.example.moviebox_app.ui.screens.RegisterScreen
import com.example.moviebox_app.ui.screens.SearchScreen
import com.example.moviebox_app.ui.screens.SeriesDetailScreen
import com.example.moviebox_app.ui.screens.SplashScreen
import com.example.moviebox_app.ui.screens.SubscriptionScreen
import com.example.moviebox_app.ui.screens.VideoPlayerScreen

private val bottomNavRoutes = setOf(
    Screen.Home.route,
    Screen.Search.route,
    Screen.Downloads.route,
    Screen.Profile.route,
)

private fun populatePlayerSession(movie: ApiMovie) {
    val progress = MovieBoxApplication.progressStore.get(movie.uuid)
    PlayerSession.title = movie.title
    PlayerSession.subtitles = movie.subtitles ?: emptyList()
    PlayerSession.contentUuid = movie.uuid
    PlayerSession.episodes = emptyList()
    PlayerSession.currentEpisodeIdx = -1
    PlayerSession.initialPositionMs = if (progress != null && !progress.isFinished) progress.positionMs else 0L
}

private fun populatePlayerSessionEpisode(
    episode: ApiEpisode,
    allEpisodes: List<ApiEpisode>,
    series: ApiSeries,
) {
    val progress = MovieBoxApplication.progressStore.get(episode.uuid)
    val idx = allEpisodes.indexOfFirst { it.uuid == episode.uuid }.coerceAtLeast(0)
    PlayerSession.title = series.title
    PlayerSession.subtitles = episode.subtitles ?: emptyList()
    PlayerSession.contentUuid = episode.uuid
    PlayerSession.episodes = allEpisodes
    PlayerSession.currentEpisodeIdx = idx
    PlayerSession.initialPositionMs = if (progress != null && !progress.isFinished) progress.positionMs else 0L
}

@Composable
fun AppNavGraph() {
    val navController = rememberNavController()
    val backStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = backStackEntry?.destination?.route

    Scaffold(
        containerColor = Color.Transparent,
        contentWindowInsets = WindowInsets(0),
        bottomBar = {
            if (currentRoute in bottomNavRoutes) {
                BottomNavBar(
                    currentRoute = currentRoute,
                    onNavigate = { route ->
                        navController.navigate(route) {
                            popUpTo(navController.graph.findStartDestination().id) {
                                saveState = true
                            }
                            launchSingleTop = true
                            restoreState = true
                        }
                    },
                )
            }
        },
    ) { innerPadding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding),
        ) {
            NavHost(
                navController    = navController,
                startDestination = Screen.Splash.route,
            ) {
                composable(Screen.Splash.route) {
                    SplashScreen(
                        onAuthenticated = {
                            navController.navigate(Screen.Subscription.route) {
                                popUpTo(Screen.Splash.route) { inclusive = true }
                            }
                        },
                        onUnauthenticated = {
                            navController.navigate(Screen.Login.route) {
                                popUpTo(Screen.Splash.route) { inclusive = true }
                            }
                        },
                    )
                }

                composable(Screen.Subscription.route) {
                    SubscriptionScreen(
                        onActive = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                        onSkip = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                    )
                }

                composable(Screen.Login.route) {
                    LoginScreen(
                        onLoginSuccess   = {
                            navController.navigate(Screen.Subscription.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                        onRegisterClick  = { navController.navigate(Screen.Register.route) },
                        onForgotClick    = { navController.navigate(Screen.ForgotPassword.route) },
                    )
                }

                composable(Screen.Register.route) {
                    RegisterScreen(
                        onRegisterSuccess = {
                            navController.navigate(Screen.Subscription.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                        onLoginClick = { navController.popBackStack() },
                    )
                }

                composable(Screen.ForgotPassword.route) {
                    ForgotPasswordScreen(
                        onResetSuccess = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                        onBack = { navController.popBackStack() },
                    )
                }

                composable(Screen.Home.route) {
                    HomeScreen(
                        onMovieClick   = { uuid -> navController.navigate(Screen.MovieDetail.createRoute(uuid)) },
                        onSeriesClick  = { uuid -> navController.navigate(Screen.SeriesDetail.createRoute(uuid)) },
                        onSearchClick  = { navController.navigate(Screen.Search.route) },
                        onSeeAllMovies = { navController.navigate(Screen.Browse.movies()) },
                        onSeeAllSeries = { navController.navigate(Screen.Browse.series()) },
                    )
                }

                composable(Screen.Search.route) {
                    SearchScreen(
                        onMovieClick  = { uuid -> navController.navigate(Screen.MovieDetail.createRoute(uuid)) },
                        onSeriesClick = { uuid -> navController.navigate(Screen.SeriesDetail.createRoute(uuid)) },
                    )
                }

                composable(
                    route     = Screen.Browse.route,
                    arguments = listOf(navArgument("type") { type = NavType.StringType }),
                ) { backStack ->
                    val browseType = backStack.arguments?.getString("type") ?: "movies"
                    BrowseScreen(
                        type = browseType,
                        onItemClick = { uuid ->
                            if (browseType == "series") navController.navigate(Screen.SeriesDetail.createRoute(uuid))
                            else navController.navigate(Screen.MovieDetail.createRoute(uuid))
                        },
                        onBack = { navController.popBackStack() },
                    )
                }

                composable(Screen.Downloads.route) { DownloadsScreen() }

                composable(Screen.Profile.route) {
                    ProfileScreen(
                        onSignOut = {
                            navController.navigate(Screen.Login.route) {
                                popUpTo(0) { inclusive = true }
                            }
                        },
                        onManageSubscription = {
                            navController.navigate(Screen.Subscription.route)
                        },
                    )
                }

                composable(
                    route     = Screen.MovieDetail.route,
                    arguments = listOf(navArgument("uuid") { type = NavType.StringType }),
                ) { backStack ->
                    val uuid = backStack.arguments?.getString("uuid") ?: return@composable
                    MovieDetailScreen(
                        uuid           = uuid,
                        onBack         = { navController.popBackStack() },
                        onPlay         = { url, movie ->
                            resolveStreamingUrl(url)?.let { resolved ->
                                populatePlayerSession(movie)
                                navController.navigate(Screen.VideoPlayer.createRoute(resolved))
                            }
                        },
                        onWatchTrailer = { url, movie ->
                            resolveStreamingUrl(url)?.let { resolved ->
                                PlayerSession.title = movie.title
                                PlayerSession.subtitles = emptyList()
                                PlayerSession.contentUuid = ""
                                PlayerSession.episodes = emptyList()
                                PlayerSession.currentEpisodeIdx = -1
                                PlayerSession.initialPositionMs = 0L
                                navController.navigate(Screen.VideoPlayer.createRoute(resolved))
                            }
                        },
                    )
                }

                composable(
                    route     = Screen.SeriesDetail.route,
                    arguments = listOf(navArgument("uuid") { type = NavType.StringType }),
                ) { backStack ->
                    val uuid = backStack.arguments?.getString("uuid") ?: return@composable
                    SeriesDetailScreen(
                        uuid           = uuid,
                        onBack         = { navController.popBackStack() },
                        onWatchTrailer = { url, series ->
                            resolveStreamingUrl(url)?.let { resolved ->
                                PlayerSession.title = series.title
                                PlayerSession.subtitles = emptyList()
                                PlayerSession.contentUuid = ""
                                PlayerSession.episodes = emptyList()
                                PlayerSession.currentEpisodeIdx = -1
                                PlayerSession.initialPositionMs = 0L
                                navController.navigate(Screen.VideoPlayer.createRoute(resolved))
                            }
                        },
                        onEpisodePlay  = { episode, allEpisodes, series ->
                            resolveStreamingUrl(episode.streamingUrl)?.let { resolved ->
                                populatePlayerSessionEpisode(episode, allEpisodes, series)
                                navController.navigate(Screen.VideoPlayer.createRoute(resolved))
                            }
                        },
                    )
                }

                composable(
                    route     = Screen.VideoPlayer.route,
                    arguments = listOf(navArgument("url") { type = NavType.StringType }),
                ) { backStack ->
                    val url = backStack.arguments?.getString("url") ?: return@composable
                    VideoPlayerScreen(url = url, onBack = { navController.popBackStack() })
                }
            }
        }
    }
}
