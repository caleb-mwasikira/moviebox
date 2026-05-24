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
import com.example.moviebox_app.ui.components.BottomNavBar
import com.example.moviebox_app.ui.screens.DownloadsScreen
import com.example.moviebox_app.ui.screens.HomeScreen
import com.example.moviebox_app.ui.screens.MovieDetailScreen
import com.example.moviebox_app.ui.screens.ProfileScreen
import com.example.moviebox_app.ui.screens.SearchScreen
import com.example.moviebox_app.ui.screens.SeriesDetailScreen
import com.example.moviebox_app.ui.screens.SplashScreen

private val bottomNavRoutes = setOf(
    Screen.Home.route,
    Screen.Search.route,
    Screen.Downloads.route,
    Screen.Profile.route,
)

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
                navController  = navController,
                startDestination = Screen.Splash.route,
            ) {
                composable(Screen.Splash.route) {
                    SplashScreen(onEnter = {
                        navController.navigate(Screen.Home.route) {
                            popUpTo(Screen.Splash.route) { inclusive = true }
                        }
                    })
                }

                composable(Screen.Home.route) {
                    HomeScreen(
                        onMovieClick  = { uuid -> navController.navigate(Screen.MovieDetail.createRoute(uuid)) },
                        onSeriesClick = { uuid -> navController.navigate(Screen.SeriesDetail.createRoute(uuid)) },
                        onSearchClick = { navController.navigate(Screen.Search.route) },
                    )
                }

                composable(Screen.Search.route) {
                    SearchScreen(
                        onMovieClick = { uuid -> navController.navigate(Screen.MovieDetail.createRoute(uuid)) },
                    )
                }

                composable(Screen.Downloads.route) { DownloadsScreen() }
                composable(Screen.Profile.route)   { ProfileScreen() }

                composable(
                    route = Screen.MovieDetail.route,
                    arguments = listOf(navArgument("uuid") { type = NavType.StringType }),
                ) { backStack ->
                    val uuid = backStack.arguments?.getString("uuid") ?: return@composable
                    MovieDetailScreen(uuid = uuid, onBack = { navController.popBackStack() })
                }

                composable(
                    route = Screen.SeriesDetail.route,
                    arguments = listOf(navArgument("uuid") { type = NavType.StringType }),
                ) { backStack ->
                    val uuid = backStack.arguments?.getString("uuid") ?: return@composable
                    SeriesDetailScreen(uuid = uuid, onBack = { navController.popBackStack() })
                }
            }
        }
    }
}
