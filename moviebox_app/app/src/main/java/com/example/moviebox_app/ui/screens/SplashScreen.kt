package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.SplashViewModel
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(
    onAuthenticated: () -> Unit,
    onUnauthenticated: () -> Unit,
    vm: SplashViewModel = viewModel(),
) {
    val featured by vm.featured.collectAsState()
    val isLoggedIn = remember { MovieBoxApplication.sessionManager.isLoggedIn() }

    LaunchedEffect(Unit) {
        if (isLoggedIn) {
            delay(900)
            onAuthenticated()
        }
    }

    Box(modifier = Modifier.fillMaxSize()) {
        // Background: featured movie poster (falls back to atmospheric glow)
        if (featured?.thumbnailUrl != null) {
            val accent = posterAccentFor(featured!!.uuid)
            val bg     = posterBgFor(featured!!.uuid)

            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .drawBehind {
                        drawRect(Brush.verticalGradient(listOf(accent.copy(alpha = 0.5f), bg)))
                        drawCircle(
                            Brush.radialGradient(
                                listOf(accent.copy(alpha = 0.18f), Color.Transparent),
                                radius = size.minDimension * 0.9f,
                            ),
                        )
                    },
            ) {
                AsyncImage(
                    model = featured!!.thumbnailUrl,
                    contentDescription = null,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxSize(),
                )
            }
        } else {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Background)
                    .drawBehind {
                        drawCircle(
                            brush = Brush.radialGradient(
                                colors = listOf(Color(0xFF1A2340).copy(alpha = 0.8f), Color.Transparent),
                                center = Offset(size.width * 0.5f, size.height * 0.3f),
                                radius = size.width * 0.9f,
                            ),
                        )
                        drawCircle(
                            brush = Brush.radialGradient(
                                colors = listOf(Color(0xFF2E1A08).copy(alpha = 0.5f), Color.Transparent),
                                center = Offset(size.width * 0.5f, size.height * 0.85f),
                                radius = size.width * 0.7f,
                            ),
                        )
                    },
            )
        }

        // Gradient fade over poster so text reads cleanly
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        listOf(Color.Transparent, Background.copy(alpha = 0.5f), Background),
                        startY = 200f,
                    )
                )
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 32.dp, vertical = 56.dp),
            verticalArrangement = Arrangement.Bottom,
            horizontalAlignment = Alignment.CenterHorizontally,
        ) {
            if (featured != null) {
                Text(
                    text = featured!!.title,
                    color = TextPrimary,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.SemiBold,
                    textAlign = TextAlign.Center,
                )
                Spacer(Modifier.height(8.dp))
            }

            Text(
                text = "Watch All Movies",
                color = TextPrimary,
                fontSize = 32.sp,
                fontWeight = FontWeight.ExtraBold,
                textAlign = TextAlign.Center,
                lineHeight = 40.sp,
            )

            Spacer(Modifier.height(16.dp))

            Text(
                text = "Stream unlimited movies and series\nanytime, anywhere.",
                color = TextSecondary,
                fontSize = 15.sp,
                textAlign = TextAlign.Center,
                lineHeight = 22.sp,
            )

            Spacer(Modifier.height(48.dp))

            if (!isLoggedIn) {
                Button(
                    onClick = onUnauthenticated,
                    modifier = Modifier.fillMaxWidth().height(54.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                ) {
                    Text("Get Started", fontSize = 16.sp, fontWeight = FontWeight.Bold)
                }

                Spacer(Modifier.height(16.dp))
            }
        }
    }
}
