package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
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
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
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
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.WatchProgressStore
import com.example.moviebox_app.data.api.ApiEpisode
import com.example.moviebox_app.data.api.ApiSeason
import com.example.moviebox_app.data.api.ApiSeries
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.components.TopToast
import com.example.moviebox_app.ui.viewmodel.SeriesDetailViewModel

@Composable
fun SeriesDetailScreen(
    uuid: String,
    onBack: () -> Unit,
    onWatchTrailer: (String, ApiSeries) -> Unit = { _, _ -> },
    onEpisodePlay: (ApiEpisode, List<ApiEpisode>, ApiSeries) -> Unit = { _, _, _ -> },
    vm: SeriesDetailViewModel = viewModel(factory = SeriesDetailViewModel.Factory(uuid)),
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

            is UiState.Success -> SeriesDetailContent(s.data, onBack, onWatchTrailer, onEpisodePlay)
        }

        TopToast(message = toastMessage, onDismiss = { toastMessage = null })
    }
}

@Composable
private fun SeriesDetailContent(
    series: ApiSeries,
    onBack: () -> Unit,
    onWatchTrailer: (String, ApiSeries) -> Unit,
    onEpisodePlay: (ApiEpisode, List<ApiEpisode>, ApiSeries) -> Unit,
) {
    val accent = posterAccentFor(series.uuid)
    val bg     = posterBgFor(series.uuid)
    val seasons = series.seasons ?: emptyList()
    val allEpisodes = seasons.flatMap { it.episodes ?: emptyList() }
    var selectedSeasonIdx by remember { mutableIntStateOf(0) }

    // Find the last-watched episode across all seasons
    val continueEpisode = allEpisodes.firstOrNull { ep ->
        val p = MovieBoxApplication.progressStore.get(ep.uuid)
        p != null && !p.isFinished
    }

    Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState())) {

        // Poster hero
        Box(
            modifier = Modifier.fillMaxWidth().height(300.dp)
                .drawBehind {
                    drawRect(Brush.verticalGradient(listOf(accent.copy(alpha = 0.5f), bg)))
                    drawCircle(Brush.radialGradient(listOf(accent.copy(alpha = 0.15f), Color.Transparent), radius = size.minDimension * 0.9f))
                },
        ) {
            series.thumbnailUrl?.let { url ->
                AsyncImage(model = url, contentDescription = null, contentScale = ContentScale.Crop, modifier = Modifier.fillMaxSize())
            }
            Box(modifier = Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(Color.Transparent, Background), startY = 150f)))

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

            Text(series.title, color = accent, fontSize = 26.sp, fontWeight = FontWeight.ExtraBold)

            Spacer(Modifier.height(8.dp))

            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                series.year?.let { Text(it.toString(), color = TextSecondary, fontSize = 13.sp); Text("·", color = TextMuted) }
                if (seasons.isNotEmpty()) { Text("${seasons.size} Season${if (seasons.size > 1) "s" else ""}", color = TextSecondary, fontSize = 13.sp); Text("·", color = TextMuted) }
                Box(modifier = Modifier.clip(RoundedCornerShape(3.dp)).background(TagBg).padding(horizontal = 6.dp, vertical = 2.dp)) {
                    Text("TV-MA", color = TextSecondary, fontSize = 11.sp)
                }
            }

            Spacer(Modifier.height(10.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                series.genreList().forEach { g ->
                    Box(modifier = Modifier.clip(RoundedCornerShape(6.dp)).background(TagBg).padding(horizontal = 8.dp, vertical = 4.dp)) {
                        Text(g, color = TextSecondary, fontSize = 11.sp)
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            // Continue Watching button
            if (continueEpisode != null) {
                val continueProgress = MovieBoxApplication.progressStore.get(continueEpisode.uuid)
                Button(
                    onClick = { onEpisodePlay(continueEpisode, allEpisodes, series) },
                    modifier = Modifier.fillMaxWidth().height(48.dp),
                    shape = RoundedCornerShape(10.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                ) {
                    Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                    Spacer(Modifier.width(6.dp))
                    Text(
                        "Continue — E${continueEpisode.episodeNumber}: ${continueEpisode.title}",
                        fontWeight = FontWeight.Bold,
                        fontSize = 14.sp,
                        maxLines = 1,
                    )
                }
                if (continueProgress != null) {
                    Spacer(Modifier.height(4.dp))
                    LinearProgressIndicator(
                        progress = { continueProgress.fraction },
                        modifier = Modifier.fillMaxWidth().height(3.dp).clip(RoundedCornerShape(2.dp)),
                        color = Accent,
                        trackColor = Accent.copy(alpha = 0.2f),
                    )
                }
                Spacer(Modifier.height(8.dp))
            }

            val firstEpisode = seasons.firstOrNull()?.episodes?.firstOrNull()
            Button(
                onClick = { firstEpisode?.let { onEpisodePlay(it, allEpisodes, series) } },
                enabled = firstEpisode?.hasStream == true,
                modifier = Modifier.fillMaxWidth().height(48.dp),
                shape = RoundedCornerShape(10.dp),
                colors = ButtonDefaults.buttonColors(containerColor = com.example.moviebox_app.ui.theme.Surface, contentColor = TextPrimary),
            ) {
                Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                Spacer(Modifier.width(6.dp))
                Text(if (firstEpisode != null) "Play S1:E01" else "Play", fontWeight = FontWeight.Bold, fontSize = 15.sp)
            }

            if (series.trailerUrl != null) {
                Spacer(Modifier.height(8.dp))
                OutlinedButton(
                    onClick = { onWatchTrailer(series.trailerUrl, series) },
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

            if (!series.description.isNullOrBlank()) {
                Spacer(Modifier.height(16.dp))
                Text("Synopsis", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                Spacer(Modifier.height(8.dp))
                Text(series.description, color = TextSecondary, fontSize = 14.sp, lineHeight = 22.sp)
            }

            if (seasons.isNotEmpty()) {
                Spacer(Modifier.height(24.dp))
                Text("Episodes", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                Spacer(Modifier.height(12.dp))

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    seasons.forEachIndexed { idx, season ->
                        val selected = idx == selectedSeasonIdx
                        Button(
                            onClick = { selectedSeasonIdx = idx },
                            colors = ButtonDefaults.buttonColors(
                                containerColor = if (selected) Accent else CardBg,
                                contentColor   = if (selected) Color.Black else TextSecondary,
                            ),
                            shape = RoundedCornerShape(8.dp),
                            contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                        ) {
                            Text("S${season.seasonNumber}", fontSize = 13.sp, fontWeight = if (selected) FontWeight.Bold else FontWeight.Normal)
                        }
                    }
                }

                Spacer(Modifier.height(12.dp))

                val episodes = seasons.getOrNull(selectedSeasonIdx)?.episodes ?: emptyList()
                if (episodes.isEmpty()) {
                    Text("No episodes available", color = TextMuted, fontSize = 13.sp)
                } else {
                    episodes.forEachIndexed { i, ep ->
                        val epProgress = MovieBoxApplication.progressStore.get(ep.uuid)
                        EpisodeRow(
                            season      = seasons[selectedSeasonIdx],
                            episode     = ep,
                            accentColor = accent,
                            progress    = epProgress,
                            onClick     = { onEpisodePlay(ep, allEpisodes, series) },
                        )
                        if (i < episodes.lastIndex) {
                            HorizontalDivider(color = DividerColor, thickness = 0.5.dp, modifier = Modifier.padding(vertical = 4.dp))
                        }
                    }
                }
            }

            Spacer(Modifier.height(32.dp))
        }
    }
}

@Composable
private fun EpisodeRow(
    season: ApiSeason,
    episode: ApiEpisode,
    accentColor: Color,
    progress: WatchProgressStore.Progress?,
    onClick: () -> Unit,
) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(enabled = episode.hasStream, onClick = onClick)
            .padding(vertical = 10.dp),
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier.width(100.dp).height(60.dp).clip(RoundedCornerShape(8.dp))
                    .background(Brush.horizontalGradient(listOf(accentColor.copy(alpha = 0.3f), accentColor.copy(alpha = 0.05f)))),
                contentAlignment = Alignment.Center,
            ) {
                Icon(Icons.Default.PlayArrow, null, tint = TextPrimary.copy(alpha = 0.6f), modifier = Modifier.size(24.dp))
            }
            Spacer(Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    "S${season.seasonNumber}:E${episode.episodeNumber.toString().padStart(2, '0')} — ${episode.title}",
                    color = TextPrimary, fontSize = 13.sp, fontWeight = FontWeight.Medium,
                )
                Spacer(Modifier.height(4.dp))
                val dur = episode.formattedDuration()
                Text(if (dur.isNotEmpty()) dur else "~42 min", color = TextMuted, fontSize = 12.sp)
            }
            Icon(Icons.Default.PlayArrow, null, tint = TextSecondary, modifier = Modifier.size(20.dp))
        }
        // Progress bar under the episode row
        if (progress != null && !progress.isFinished) {
            Spacer(Modifier.height(6.dp))
            LinearProgressIndicator(
                progress = { progress.fraction },
                modifier = Modifier.fillMaxWidth().height(2.dp).clip(RoundedCornerShape(1.dp)),
                color = accentColor,
                trackColor = accentColor.copy(alpha = 0.2f),
            )
        }
    }
}
