package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.foundation.BorderStroke
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Download
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.ThumbUp
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.MatchGreen
import com.example.moviebox_app.ui.theme.Surface
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.components.TopToast
import com.example.moviebox_app.ui.viewmodel.MovieDetailViewModel

@Composable
fun MovieDetailScreen(
    uuid: String,
    onBack: () -> Unit,
    onPlay: (String) -> Unit = {},
    onWatchTrailer: (String) -> Unit = {},
    vm: MovieDetailViewModel = viewModel(factory = MovieDetailViewModel.Factory(uuid)),
) {
    val state by vm.state.collectAsState()
    var toastMessage by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(state) {
        if (state is UiState.Error) toastMessage = (state as UiState.Error).message
    }

    Box(modifier = Modifier.fillMaxSize().background(Background)) {
        when (val s = state) {
            is UiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Accent)
            }

            is UiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally, verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Button(onClick = vm::load, colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black)) {
                        Icon(Icons.Default.Refresh, null, modifier = Modifier.size(16.dp))
                        Spacer(Modifier.width(6.dp))
                        Text("Retry")
                    }
                }
            }

            is UiState.Success -> MovieDetailContent(s.data, onBack, onPlay, onWatchTrailer)
        }

        TopToast(message = toastMessage, onDismiss = { toastMessage = null })
    }
}

@Composable
private fun MovieDetailContent(
    movie: ApiMovie,
    onBack: () -> Unit,
    onPlay: (String) -> Unit,
    onWatchTrailer: (String) -> Unit,
) {
    val accent = posterAccentFor(movie.uuid)
    val bg     = posterBgFor(movie.uuid)

    Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState())) {

        // Poster hero
        Box(
            modifier = Modifier.fillMaxWidth().height(340.dp)
                .drawBehind {
                    drawRect(Brush.verticalGradient(listOf(accent.copy(alpha = 0.5f), bg)))
                    drawCircle(Brush.radialGradient(listOf(accent.copy(alpha = 0.15f), Color.Transparent), radius = size.minDimension * 0.9f))
                },
        ) {
            movie.thumbnailUrl?.let { url ->
                AsyncImage(model = url, contentDescription = null, contentScale = ContentScale.Crop, modifier = Modifier.fillMaxSize())
            }
            Box(modifier = Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(Color.Transparent, Background), startY = 180f)))

            // Preview badge
            Box(
                modifier = Modifier.align(Alignment.BottomStart).padding(16.dp).clip(RoundedCornerShape(6.dp)).background(Color.Black.copy(alpha = 0.5f)).padding(horizontal = 10.dp, vertical = 4.dp),
            ) { Text("Preview", color = TextSecondary, fontSize = 12.sp) }

            // Top bar
            Row(modifier = Modifier.fillMaxWidth().padding(8.dp), horizontalArrangement = Arrangement.SpaceBetween) {
                IconButton(onClick = onBack) {
                    Box(modifier = Modifier.size(36.dp).clip(CircleShape).background(Color.Black.copy(alpha = 0.4f)), contentAlignment = Alignment.Center) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, null, tint = TextPrimary, modifier = Modifier.size(20.dp))
                    }
                }
                IconButton(onClick = {}) {
                    Box(modifier = Modifier.size(36.dp).clip(CircleShape).background(Color.Black.copy(alpha = 0.4f)), contentAlignment = Alignment.Center) {
                        Icon(Icons.Default.MoreVert, null, tint = TextPrimary, modifier = Modifier.size(20.dp))
                    }
                }
            }
        }

        Column(modifier = Modifier.padding(horizontal = 16.dp)) {
            Spacer(Modifier.height(16.dp))

            Text(movie.title, color = accent, fontSize = 26.sp, fontWeight = FontWeight.ExtraBold, lineHeight = 32.sp)

            Spacer(Modifier.height(10.dp))

            // Meta row
            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                if (movie.year != null) { Text(movie.year.toString(), color = TextSecondary, fontSize = 13.sp); Text("·", color = TextMuted, fontSize = 13.sp) }
                if (movie.formattedDuration().isNotEmpty()) { Text(movie.formattedDuration(), color = TextSecondary, fontSize = 13.sp); Text("·", color = TextMuted, fontSize = 13.sp) }
                Text("HD", color = TextSecondary, fontSize = 13.sp)
                if (movie.isFree) {
                    Box(modifier = Modifier.clip(RoundedCornerShape(3.dp)).background(MatchGreen.copy(alpha = 0.2f)).padding(horizontal = 6.dp, vertical = 2.dp)) {
                        Text("FREE", color = MatchGreen, fontSize = 10.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // Genre tags + badge
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                if (movie.featured) {
                    Row(
                        modifier = Modifier.clip(RoundedCornerShape(6.dp)).background(TagBg).padding(horizontal = 8.dp, vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(4.dp),
                    ) {
                        Icon(Icons.Default.ThumbUp, null, tint = MatchGreen, modifier = Modifier.size(12.dp))
                        Text("Featured", color = MatchGreen, fontSize = 11.sp, fontWeight = FontWeight.Medium)
                    }
                }
                movie.genreList().take(2).forEach { g ->
                    Box(modifier = Modifier.clip(RoundedCornerShape(6.dp)).background(TagBg).padding(horizontal = 8.dp, vertical = 4.dp)) {
                        Text(g, color = TextSecondary, fontSize = 11.sp)
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            // Play button
            Button(
                onClick = { movie.streamingUrl?.let(onPlay) },
                enabled = movie.hasStream,
                modifier = Modifier.fillMaxWidth().height(48.dp),
                shape = RoundedCornerShape(10.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Surface, contentColor = TextPrimary),
            ) {
                Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                Spacer(Modifier.width(6.dp))
                Text("Play", fontWeight = FontWeight.Bold, fontSize = 15.sp)
            }

            if (movie.trailerUrl != null) {
                Spacer(Modifier.height(8.dp))
                OutlinedButton(
                    onClick = { onWatchTrailer(movie.trailerUrl) },
                    modifier = Modifier.fillMaxWidth().height(48.dp),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, TextSecondary.copy(alpha = 0.4f)),
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = TextPrimary),
                ) {
                    Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                    Spacer(Modifier.width(6.dp))
                    Text("Watch Trailer", fontWeight = FontWeight.Bold, fontSize = 15.sp)
                }
            }

            Spacer(Modifier.height(8.dp))

            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                ActionIconBtn(icon = { Icon(Icons.Default.Add, null, Modifier.size(20.dp)) }, label = "Watchlist", modifier = Modifier.weight(1f))
                ActionIconBtn(icon = { Icon(Icons.Default.Download, null, Modifier.size(20.dp)) }, label = "Download", modifier = Modifier.weight(1f))
            }

            if (!movie.description.isNullOrBlank()) {
                Spacer(Modifier.height(20.dp))
                Text("Synopsis", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                Spacer(Modifier.height(8.dp))
                Text(movie.description, color = TextSecondary, fontSize = 14.sp, lineHeight = 22.sp)
            }

            Spacer(Modifier.height(32.dp))
        }
    }
}

@Composable
private fun ActionIconBtn(icon: @Composable () -> Unit, label: String, modifier: Modifier = Modifier) {
    Button(
        onClick = {},
        modifier = modifier.height(44.dp),
        shape = RoundedCornerShape(10.dp),
        colors = ButtonDefaults.buttonColors(containerColor = CardBg, contentColor = TextSecondary),
        contentPadding = PaddingValues(horizontal = 12.dp),
    ) {
        icon()
        Spacer(Modifier.width(6.dp))
        Text(label, fontSize = 13.sp)
    }
}
