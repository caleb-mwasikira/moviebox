package com.example.moviebox_app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.example.moviebox_app.navigation.AppNavGraph
import com.example.moviebox_app.ui.theme.Moviebox_appTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            Moviebox_appTheme {
                AppNavGraph()
            }
        }
    }
}
