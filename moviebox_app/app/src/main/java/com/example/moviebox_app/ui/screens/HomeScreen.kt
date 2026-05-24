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
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.NotificationsNone
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.moviebox_app.data.MockData
import com.example.moviebox_app.data.Movie
import com.example.moviebox_app.ui.components.MoviePosterCard
import com.example.moviebox_app.ui.components.SeriesPosterCard
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.MatchGreen
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary

@Composable
fun HomeScreen(
    onMovieClick: (Int) -> Unit,
    onSeriesClick: (Int) -> Unit,
    onSearchClick: () -> Unit,
) {
    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Background),
        contentPadding = PaddingValues(bottom = 16.dp),
    ) {
        // Top bar
        item {
            HomeTopBar(onSearchClick = onSearchClick)
        }

        // Hero featured film
        item {
            HeroSection(
                movie = MockData.featured,
                onPlay = { onMovieClick(MockData.featured.id) },
                onDetails = { onMovieClick(MockData.featured.id) },
            )
        }

        // Trending Now
        item {
            Spacer(Modifier.height(24.dp))
            SectionRow(title = "Trending Now", onSeeAll = {})
            Spacer(Modifier.height(12.dp))
            LazyRow(
                contentPadding = PaddingValues(horizontal = 16.dp),
                horizontalArrangement = Arrangement.spacedBy(12.dp),
            ) {
                items(MockData.trending) { movie ->
                    MoviePosterCard(movie = movie, onClick = { onMovieClick(movie.id) })
                }
            }
        }

        // Series
        item {
            Spacer(Modifier.height(28.dp))
            SectionRow(title = "Series", onSeeAll = {})
            Spacer(Modifier.height(12.dp))
            LazyRow(
                contentPadding = PaddingValues(horizontal = 16.dp),
                horizontalArrangement = Arrangement.spacedBy(12.dp),
            ) {
                items(MockData.seriesList) { series ->
                    SeriesPosterCard(series = series, onClick = { onSeriesClick(series.id) })
                }
            }
        }

        // Now Showing
        item {
            Spacer(Modifier.height(28.dp))
            SectionRow(title = "Now Showing", onSeeAll = {})
            Spacer(Modifier.height(12.dp))
            LazyRow(
                contentPadding = PaddingValues(horizontal = 16.dp),
                horizontalArrangement = Arrangement.spacedBy(12.dp),
            ) {
                items(MockData.nowShowing) { movie ->
                    MoviePosterCard(movie = movie, width = 140.dp, onClick = { onMovieClick(movie.id) })
                }
            }
        }

        // Popular
        item {
            Spacer(Modifier.height(28.dp))
            SectionRow(title = "Popular", onSeeAll = {})
            Spacer(Modifier.height(12.dp))
        }

        items(MockData.popular) { movie ->
            PopularListItem(movie = movie, onClick = { onMovieClick(movie.id) })
            Spacer(Modifier.height(12.dp))
        }
    }
}

@Composable
private fun HomeTopBar(onSearchClick: () -> Unit) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 12.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        // Avatar
        Box(
            modifier = Modifier
                .size(38.dp)
                .clip(CircleShape)
                .background(Accent.copy(alpha = 0.25f)),
            contentAlignment = Alignment.Center,
        ) {
            Text("M", color = Accent, fontWeight = FontWeight.Bold, fontSize = 16.sp)
        }

        Spacer(Modifier.width(12.dp))

        Column(modifier = Modifier.weight(1f)) {
            Text("Welcome back", color = TextMuted, fontSize = 12.sp)
            Text("MovieBox", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 15.sp)
        }

        IconButton(onClick = onSearchClick) {
            Icon(
                imageVector = Icons.Default.Search,
                contentDescription = "Search",
                tint = TextSecondary,
                modifier = Modifier.size(22.dp),
            )
        }

        IconButton(onClick = {}) {
            Icon(
                imageVector = Icons.Outlined.NotificationsNone,
                contentDescription = "Notifications",
                tint = TextSecondary,
                modifier = Modifier.size(22.dp),
            )
        }
    }
}

@Composable
private fun HeroSection(movie: Movie, onPlay: () -> Unit, onDetails: () -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(480.dp)
            .drawBehind {
                drawRect(
                    brush = Brush.verticalGradient(
                        listOf(movie.posterAccent.copy(alpha = 0.4f), movie.posterColor)
                    )
                )
            },
    ) {
        // Inner radial glow for depth
        Box(
            modifier = Modifier
                .fillMaxSize()
                .drawBehind {
                    drawCircle(
                        brush = Brush.radialGradient(
                            colors = listOf(
                                movie.posterAccent.copy(alpha = 0.2f),
                                Color.Transparent,
                            ),
                            radius = size.minDimension * 0.8f,
                        )
                    )
                }
        )

        // Bottom gradient overlay
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        listOf(Color.Transparent, Background),
                        startY = 200f,
                    )
                )
        )

        Column(
            modifier = Modifier
                .align(Alignment.BottomStart)
                .padding(horizontal = 20.dp, vertical = 24.dp),
        ) {
            Text(
                text = movie.title,
                color = movie.posterAccent,
                fontSize = 32.sp,
                fontWeight = FontWeight.ExtraBold,
                lineHeight = 36.sp,
            )

            Spacer(Modifier.height(8.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                movie.genre.forEach { g ->
                    Text(g, color = TextSecondary, fontSize = 12.sp)
                    if (g != movie.genre.last()) {
                        Text("·", color = TextSecondary, fontSize = 12.sp)
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                Button(
                    onClick = onPlay,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = TextPrimary,
                        contentColor   = Color.Black,
                    ),
                    shape = RoundedCornerShape(8.dp),
                    contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp),
                ) {
                    Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(18.dp))
                    Spacer(Modifier.width(4.dp))
                    Text("Play", fontWeight = FontWeight.Bold, fontSize = 14.sp)
                }

                OutlinedButton(
                    onClick = onDetails,
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = TextPrimary),
                    border = BorderStroke(1.dp, TextSecondary),
                    contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp),
                ) {
                    Text("Details", fontSize = 14.sp)
                }
            }
        }
    }
}

@Composable
private fun SectionRow(title: String, onSeeAll: () -> Unit) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Text(title, color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 17.sp)
        Text(
            "See all",
            color = Accent,
            fontSize = 13.sp,
            modifier = Modifier.clickable(onClick = onSeeAll),
        )
    }
}

@Composable
private fun PopularListItem(movie: Movie, onClick: () -> Unit) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
            .clip(RoundedCornerShape(12.dp))
            .background(CardBg)
            .clickable(onClick = onClick)
            .padding(12.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        // Thumbnail
        Box(
            modifier = Modifier
                .width(72.dp)
                .aspectRatio(0.7f)
                .clip(RoundedCornerShape(8.dp))
                .background(
                    Brush.verticalGradient(
                        listOf(movie.posterAccent.copy(alpha = 0.5f), movie.posterColor)
                    )
                ),
            contentAlignment = Alignment.Center,
        ) {
            Text(
                text = movie.title.take(1),
                color = TextPrimary,
                fontSize = 20.sp,
                fontWeight = FontWeight.ExtraBold,
            )
        }

        Spacer(Modifier.width(14.dp))

        Column(modifier = Modifier.weight(1f)) {
            Text(
                movie.title,
                color = TextPrimary,
                fontWeight = FontWeight.SemiBold,
                fontSize = 14.sp,
                maxLines = 1,
                overflow = TextOverflow.Ellipsis,
            )

            Spacer(Modifier.height(6.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                movie.genre.take(3).forEach { tag ->
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(4.dp))
                            .background(TagBg)
                            .padding(horizontal = 8.dp, vertical = 3.dp),
                    ) {
                        Text(tag, color = TextSecondary, fontSize = 11.sp)
                    }
                }
            }

            Spacer(Modifier.height(6.dp))

            Row(verticalAlignment = Alignment.CenterVertically) {
                Text("★ ${movie.matchPercent / 20.0}", color = Accent, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                Spacer(Modifier.width(8.dp))
                Text(movie.duration, color = TextMuted, fontSize = 12.sp)
            }
        }
    }
}
