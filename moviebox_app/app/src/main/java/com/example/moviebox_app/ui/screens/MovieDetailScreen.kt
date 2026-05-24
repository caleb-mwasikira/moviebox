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
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Download
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.ThumbUp
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
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
import com.example.moviebox_app.data.Movie
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.MatchGreen
import com.example.moviebox_app.ui.theme.Surface
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary

@Composable
fun MovieDetailScreen(movieId: Int, onBack: () -> Unit) {
    val movie = MockData.findMovie(movieId) ?: return

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
            .verticalScroll(rememberScrollState()),
    ) {
        // Poster hero
        PosterHero(movie = movie, onBack = onBack)

        // Content
        Column(modifier = Modifier.padding(horizontal = 16.dp)) {
            Spacer(Modifier.height(16.dp))

            // Title
            Text(
                text = movie.title,
                color = movie.posterAccent,
                fontSize = 26.sp,
                fontWeight = FontWeight.ExtraBold,
                lineHeight = 32.sp,
            )

            Spacer(Modifier.height(10.dp))

            // Meta row
            MetaRow(movie = movie)

            Spacer(Modifier.height(12.dp))

            // Badges
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                BadgeChip(icon = { Icon(Icons.Default.ThumbUp, null, tint = MatchGreen, modifier = Modifier.size(12.dp)) }, label = "Most Liked", labelColor = MatchGreen)
                movie.genre.take(2).forEach { g -> GenreTag(g) }
            }

            Spacer(Modifier.height(16.dp))

            // Play button
            Button(
                onClick = {},
                modifier = Modifier
                    .fillMaxWidth()
                    .height(48.dp),
                shape = RoundedCornerShape(10.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = Surface,
                    contentColor   = TextPrimary,
                ),
            ) {
                Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(20.dp))
                Spacer(Modifier.width(6.dp))
                Text("Play", fontWeight = FontWeight.Bold, fontSize = 15.sp)
            }

            Spacer(Modifier.height(8.dp))

            // Action buttons row
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                ActionIconButton(icon = { Icon(Icons.Default.Add, null, modifier = Modifier.size(20.dp)) }, label = "Watchlist", modifier = Modifier.weight(1f))
                ActionIconButton(icon = { Icon(Icons.Default.Download, null, modifier = Modifier.size(20.dp)) }, label = "Download", modifier = Modifier.weight(1f))
            }

            Spacer(Modifier.height(20.dp))

            // Description
            Text("Synopsis", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
            Spacer(Modifier.height(8.dp))
            Text(movie.description, color = TextSecondary, fontSize = 14.sp, lineHeight = 22.sp)

            // Cast
            if (movie.cast.isNotEmpty()) {
                Spacer(Modifier.height(24.dp))
                Text("Top Cast", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                Spacer(Modifier.height(12.dp))

                LazyRow(horizontalArrangement = Arrangement.spacedBy(16.dp)) {
                    items(movie.cast) { name ->
                        CastItem(name = name, accentColor = movie.posterAccent)
                    }
                }
            }

            Spacer(Modifier.height(32.dp))
        }
    }
}

@Composable
private fun PosterHero(movie: Movie, onBack: () -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(340.dp)
            .drawBehind {
                drawRect(
                    brush = Brush.verticalGradient(
                        listOf(movie.posterAccent.copy(alpha = 0.5f), movie.posterColor)
                    )
                )
                // inner glow
                drawCircle(
                    brush = Brush.radialGradient(
                        listOf(movie.posterAccent.copy(alpha = 0.15f), Color.Transparent),
                        radius = size.minDimension * 0.9f,
                    )
                )
            }
    ) {
        // Bottom fade
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        listOf(Color.Transparent, Background),
                        startY = 180f,
                    )
                )
        )

        // Preview label
        Box(
            modifier = Modifier
                .align(Alignment.BottomStart)
                .padding(16.dp)
                .clip(RoundedCornerShape(6.dp))
                .background(Color.Black.copy(alpha = 0.5f))
                .padding(horizontal = 10.dp, vertical = 4.dp),
        ) {
            Text("Preview", color = TextSecondary, fontSize = 12.sp)
        }

        // Top bar
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 8.dp, vertical = 8.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
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
private fun MetaRow(movie: Movie) {
    Row(
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(10.dp),
    ) {
        Text("${movie.matchPercent}% match", color = MatchGreen, fontWeight = FontWeight.Bold, fontSize = 13.sp)
        MetaDot()
        Text(movie.year.toString(), color = TextSecondary, fontSize = 13.sp)
        MetaDot()
        Box(
            modifier = Modifier
                .clip(RoundedCornerShape(3.dp))
                .background(TagBg)
                .padding(horizontal = 6.dp, vertical = 2.dp),
        ) {
            Text(movie.rating, color = TextSecondary, fontSize = 11.sp)
        }
        MetaDot()
        Text(movie.duration, color = TextSecondary, fontSize = 13.sp)
        MetaDot()
        Text("HD", color = TextSecondary, fontSize = 13.sp)
    }
}

@Composable
private fun MetaDot() {
    Text("·", color = TextMuted, fontSize = 13.sp)
}

@Composable
private fun BadgeChip(
    icon: @Composable () -> Unit,
    label: String,
    labelColor: Color,
) {
    Row(
        modifier = Modifier
            .clip(RoundedCornerShape(6.dp))
            .background(TagBg)
            .padding(horizontal = 8.dp, vertical = 4.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(4.dp),
    ) {
        icon()
        Text(label, color = labelColor, fontSize = 11.sp, fontWeight = FontWeight.Medium)
    }
}

@Composable
private fun GenreTag(genre: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(6.dp))
            .background(TagBg)
            .padding(horizontal = 8.dp, vertical = 4.dp),
    ) {
        Text(genre, color = TextSecondary, fontSize = 11.sp)
    }
}

@Composable
private fun ActionIconButton(
    icon: @Composable () -> Unit,
    label: String,
    modifier: Modifier = Modifier,
) {
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

@Composable
private fun CastItem(name: String, accentColor: Color) {
    Column(horizontalAlignment = Alignment.CenterHorizontally) {
        Box(
            modifier = Modifier
                .size(56.dp)
                .clip(CircleShape)
                .background(
                    Brush.verticalGradient(
                        listOf(accentColor.copy(alpha = 0.4f), accentColor.copy(alpha = 0.1f))
                    )
                ),
            contentAlignment = Alignment.Center,
        ) {
            Text(name.take(1), color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 20.sp)
        }
        Spacer(Modifier.height(6.dp))
        Text(name.take(10), color = TextSecondary, fontSize = 11.sp, maxLines = 1)
    }
}
