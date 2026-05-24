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
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.moviebox_app.data.MockData
import com.example.moviebox_app.data.Series
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.Surface
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary

@Composable
fun SeriesDetailScreen(seriesId: Int, onBack: () -> Unit) {
    val series = MockData.findSeries(seriesId) ?: return
    var selectedSeason by remember { mutableIntStateOf(1) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
            .verticalScroll(rememberScrollState()),
    ) {
        // Poster hero
        SeriesPosterHero(series = series, onBack = onBack)

        Column(modifier = Modifier.padding(horizontal = 16.dp)) {
            Spacer(Modifier.height(16.dp))

            // Title
            Text(
                text = series.title,
                color = series.posterAccent,
                fontSize = 26.sp,
                fontWeight = FontWeight.ExtraBold,
            )

            Spacer(Modifier.height(8.dp))

            // Meta
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                Text(series.year.toString(), color = TextSecondary, fontSize = 13.sp)
                Text("·", color = TextMuted, fontSize = 13.sp)
                Text("${series.seasons} Season${if (series.seasons > 1) "s" else ""}", color = TextSecondary, fontSize = 13.sp)
                Text("·", color = TextMuted, fontSize = 13.sp)
                Box(
                    modifier = Modifier
                        .clip(RoundedCornerShape(3.dp))
                        .background(TagBg)
                        .padding(horizontal = 6.dp, vertical = 2.dp),
                ) {
                    Text("TV-MA", color = TextSecondary, fontSize = 11.sp)
                }
            }

            Spacer(Modifier.height(10.dp))

            // Genres
            Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                series.genre.forEach { g ->
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(6.dp))
                            .background(TagBg)
                            .padding(horizontal = 8.dp, vertical = 4.dp),
                    ) {
                        Text(g, color = TextSecondary, fontSize = 11.sp)
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            // Play button
            Button(
                onClick = {},
                modifier = Modifier
                    .fillMaxWidth()
                    .height(48.dp),
                shape = RoundedCornerShape(10.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
            ) {
                Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                Spacer(Modifier.width(6.dp))
                Text("Play S1:E1", fontWeight = FontWeight.Bold, fontSize = 15.sp)
            }

            Spacer(Modifier.height(16.dp))

            // Description
            Text("Synopsis", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
            Spacer(Modifier.height(8.dp))
            Text(series.description, color = TextSecondary, fontSize = 14.sp, lineHeight = 22.sp)

            Spacer(Modifier.height(24.dp))

            // Season picker
            Text("Episodes", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
            Spacer(Modifier.height(12.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                (1..series.seasons).forEach { s ->
                    val selected = s == selectedSeason
                    Button(
                        onClick = { selectedSeason = s },
                        colors = ButtonDefaults.buttonColors(
                            containerColor = if (selected) Accent else CardBg,
                            contentColor   = if (selected) Color.Black else TextSecondary,
                        ),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                    ) {
                        Text("S$s", fontSize = 13.sp, fontWeight = if (selected) FontWeight.Bold else FontWeight.Normal)
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // Mock episode list
            val episodeCount = if (selectedSeason == 1) 8 else 6
            (1..episodeCount).forEach { ep ->
                EpisodeRow(season = selectedSeason, episode = ep, accentColor = series.posterAccent)
                if (ep < episodeCount) {
                    HorizontalDivider(color = DividerColor, thickness = 0.5.dp, modifier = Modifier.padding(vertical = 4.dp))
                }
            }

            Spacer(Modifier.height(32.dp))
        }
    }
}

@Composable
private fun SeriesPosterHero(series: Series, onBack: () -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(300.dp)
            .drawBehind {
                drawRect(
                    brush = Brush.verticalGradient(
                        listOf(series.posterAccent.copy(alpha = 0.5f), series.posterColor)
                    )
                )
                drawCircle(
                    brush = Brush.radialGradient(
                        listOf(series.posterAccent.copy(alpha = 0.15f), Color.Transparent),
                        radius = size.minDimension * 0.9f,
                    )
                )
            }
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        listOf(Color.Transparent, Background),
                        startY = 150f,
                    )
                )
        )

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(8.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
        ) {
            IconButton(onClick = onBack) {
                Box(
                    modifier = Modifier
                        .size(36.dp)
                        .clip(CircleShape)
                        .background(Color.Black.copy(alpha = 0.4f)),
                    contentAlignment = Alignment.Center,
                ) {
                    Icon(Icons.AutoMirrored.Filled.ArrowBack, null, tint = TextPrimary, modifier = Modifier.size(20.dp))
                }
            }
            IconButton(onClick = {}) {
                Box(
                    modifier = Modifier
                        .size(36.dp)
                        .clip(CircleShape)
                        .background(Color.Black.copy(alpha = 0.4f)),
                    contentAlignment = Alignment.Center,
                ) {
                    Icon(Icons.Default.MoreVert, null, tint = TextPrimary, modifier = Modifier.size(20.dp))
                }
            }
        }
    }
}

@Composable
private fun EpisodeRow(season: Int, episode: Int, accentColor: Color) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 10.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        // Thumbnail
        Box(
            modifier = Modifier
                .width(100.dp)
                .height(60.dp)
                .clip(RoundedCornerShape(8.dp))
                .background(
                    Brush.horizontalGradient(
                        listOf(accentColor.copy(alpha = 0.3f), accentColor.copy(alpha = 0.05f))
                    )
                ),
            contentAlignment = Alignment.Center,
        ) {
            Icon(Icons.Default.PlayArrow, null, tint = TextPrimary.copy(alpha = 0.6f), modifier = Modifier.size(24.dp))
        }

        Spacer(Modifier.width(12.dp))

        Column(modifier = Modifier.weight(1f)) {
            Text(
                "S${season}:E${episode.toString().padStart(2, '0')} — Episode $episode",
                color = TextPrimary,
                fontSize = 13.sp,
                fontWeight = FontWeight.Medium,
            )
            Spacer(Modifier.height(4.dp))
            Text("~42 min", color = TextMuted, fontSize = 12.sp)
        }

        Icon(Icons.Default.PlayArrow, null, tint = TextSecondary, modifier = Modifier.size(20.dp))
    }
}
