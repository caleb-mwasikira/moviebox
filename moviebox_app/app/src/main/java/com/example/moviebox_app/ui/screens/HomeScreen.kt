package com.example.moviebox_app.ui.screens

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
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.GridItemSpan
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items as gridItems
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.outlined.NotificationsNone
import androidx.compose.material.icons.outlined.Search
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
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
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiSeries
import com.example.moviebox_app.ui.components.TopToast
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.HomeData
import com.example.moviebox_app.ui.viewmodel.HomeViewModel

@Composable
fun HomeScreen(
    onMovieClick: (String) -> Unit,
    onSeriesClick: (String) -> Unit,
    onSearchClick: () -> Unit,
    vm: HomeViewModel = viewModel(),
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

            is UiState.Error -> RetryContent(onRetry = vm::load)

            is UiState.Success -> HomeContent(
                data          = s.data,
                onMovieClick  = onMovieClick,
                onSeriesClick = onSeriesClick,
                onSearchClick = onSearchClick,
            )
        }

        TopToast(message = toastMessage, onDismiss = { toastMessage = null })
    }
}

@Composable
private fun HomeContent(
    data: HomeData,
    onMovieClick: (String) -> Unit,
    onSeriesClick: (String) -> Unit,
    onSearchClick: () -> Unit,
) {
    val movies = (data.nowShowing + data.popular).distinctBy { it.uuid }

    LazyVerticalGrid(
        columns = GridCells.Fixed(2),
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(bottom = 24.dp),
    ) {
        item(span = { GridItemSpan(maxLineSpan) }) {
            HomeTopBar(onSearchClick)
        }

        if (data.featured != null) {
            item(span = { GridItemSpan(maxLineSpan) }) {
                FeaturedHero(data.featured, onClick = { onMovieClick(data.featured.uuid) })
            }
        }

        if (data.trending.isNotEmpty()) {
            item(span = { GridItemSpan(maxLineSpan) }) {
                Spacer(Modifier.height(24.dp))
                SectionRow("Coming Soon", {})
                Spacer(Modifier.height(12.dp))
                LazyRow(
                    contentPadding = PaddingValues(horizontal = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    items(data.trending) { movie ->
                        MoviePosterCard(movie, onClick = { onMovieClick(movie.uuid) })
                    }
                }
            }
        }

        item(span = { GridItemSpan(maxLineSpan) }) {
            Spacer(Modifier.height(28.dp))
            SectionRow("Movies", {})
            Spacer(Modifier.height(12.dp))
        }

        if (movies.isEmpty()) {
            item(span = { GridItemSpan(maxLineSpan) }) { EmptySection("No movies found") }
        } else {
            gridItems(movies, key = { it.uuid }) { movie ->
                PosterGridCard(
                    title        = movie.title,
                    year         = movie.year?.toString(),
                    thumbnailUrl = movie.thumbnailUrl,
                    uuid         = movie.uuid,
                    onClick      = { onMovieClick(movie.uuid) },
                )
            }
        }

        item(span = { GridItemSpan(maxLineSpan) }) {
            Spacer(Modifier.height(28.dp))
            SectionRow("Series", {})
            Spacer(Modifier.height(12.dp))
        }

        if (data.series.isEmpty()) {
            item(span = { GridItemSpan(maxLineSpan) }) { EmptySection("No series found") }
        } else {
            gridItems(data.series, key = { it.uuid }) { series ->
                PosterGridCard(
                    title        = series.title,
                    year         = series.year?.toString(),
                    thumbnailUrl = series.thumbnailUrl,
                    uuid         = series.uuid,
                    onClick      = { onSeriesClick(series.uuid) },
                )
            }
        }
    }
}

@Composable
private fun HomeTopBar(onSearchClick: () -> Unit) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 12.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Box(
            modifier = Modifier.size(38.dp).clip(CircleShape).background(Accent.copy(alpha = 0.25f)),
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
            Icon(Icons.Outlined.Search, "Search", tint = TextSecondary, modifier = Modifier.size(22.dp))
        }
        IconButton(onClick = {}) {
            Icon(Icons.Outlined.NotificationsNone, "Notifications", tint = TextSecondary, modifier = Modifier.size(22.dp))
        }
    }
}

@Composable
private fun FeaturedHero(movie: ApiMovie, onClick: () -> Unit) {
    val accent = posterAccentFor(movie.uuid)
    val bg     = posterBgFor(movie.uuid)

    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(420.dp)
            .clickable(onClick = onClick)
            .drawBehind {
                drawRect(Brush.verticalGradient(listOf(accent.copy(alpha = 0.5f), bg)))
                drawCircle(
                    Brush.radialGradient(listOf(accent.copy(alpha = 0.18f), Color.Transparent), radius = size.minDimension * 0.85f)
                )
            },
    ) {
        movie.thumbnailUrl?.let { url ->
            AsyncImage(model = url, contentDescription = null, contentScale = ContentScale.Crop, modifier = Modifier.fillMaxSize())
        }
        Box(modifier = Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(Color.Transparent, Background), startY = 200f)))

        Column(modifier = Modifier.align(Alignment.BottomStart).padding(horizontal = 20.dp, vertical = 24.dp)) {
            Box(
                modifier = Modifier
                    .clip(RoundedCornerShape(4.dp))
                    .background(Accent.copy(alpha = 0.2f))
                    .padding(horizontal = 8.dp, vertical = 3.dp),
            ) {
                Text("Featured", color = Accent, fontSize = 11.sp, fontWeight = FontWeight.Bold)
            }
            Spacer(Modifier.height(8.dp))
            Text(movie.title, color = TextPrimary, fontSize = 28.sp, fontWeight = FontWeight.ExtraBold, lineHeight = 32.sp)
            Spacer(Modifier.height(6.dp))
            val genres = movie.genreList().take(3)
            Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                genres.forEachIndexed { i, g ->
                    Text(g, color = TextSecondary, fontSize = 12.sp)
                    if (i < genres.lastIndex) Text("·", color = TextSecondary, fontSize = 12.sp)
                }
            }
            if (movie.year != null) {
                Spacer(Modifier.height(4.dp))
                Text(movie.year.toString(), color = TextMuted, fontSize = 12.sp)
            }
            Spacer(Modifier.height(16.dp))
            Button(
                onClick = onClick,
                colors = ButtonDefaults.buttonColors(containerColor = TextPrimary, contentColor = Color.Black),
                shape = RoundedCornerShape(8.dp),
                contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp),
            ) {
                Icon(Icons.Default.PlayArrow, null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(4.dp))
                Text("Play", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            }
        }
    }
}

@Composable
private fun MoviePosterCard(movie: ApiMovie, onClick: () -> Unit) {
    val accent = posterAccentFor(movie.uuid)
    val bg     = posterBgFor(movie.uuid)
    Column(modifier = Modifier.width(120.dp).clickable(onClick = onClick)) {
        Box(
            modifier = Modifier.fillMaxWidth().aspectRatio(0.67f).clip(RoundedCornerShape(10.dp))
                .background(Brush.verticalGradient(listOf(accent.copy(alpha = 0.6f), bg))),
            contentAlignment = Alignment.BottomStart,
        ) {
            movie.thumbnailUrl?.let { url ->
                AsyncImage(model = url, contentDescription = null, contentScale = ContentScale.Crop, modifier = Modifier.fillMaxSize())
            }
            Box(modifier = Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(Color.Transparent, Color.Black.copy(alpha = 0.7f)), startY = 80f)))
            Text(movie.title, color = TextPrimary, fontSize = 11.sp, fontWeight = FontWeight.Bold, maxLines = 2, overflow = TextOverflow.Ellipsis, modifier = Modifier.padding(8.dp))
        }
        Spacer(Modifier.height(6.dp))
        Text(movie.year?.toString() ?: "", color = TextSecondary, fontSize = 11.sp)
    }
}

@Composable
private fun PosterGridCard(
    title: String,
    year: String?,
    thumbnailUrl: String?,
    uuid: String,
    onClick: () -> Unit,
) {
    val accent = posterAccentFor(uuid)
    val bg     = posterBgFor(uuid)
    Column(modifier = Modifier.padding(horizontal = 8.dp, vertical = 6.dp).clickable(onClick = onClick)) {
        Box(
            modifier = Modifier.fillMaxWidth().aspectRatio(0.67f).clip(RoundedCornerShape(10.dp))
                .background(Brush.verticalGradient(listOf(accent.copy(alpha = 0.6f), bg))),
            contentAlignment = Alignment.BottomStart,
        ) {
            thumbnailUrl?.let { url ->
                AsyncImage(model = url, contentDescription = null, contentScale = ContentScale.Crop, modifier = Modifier.fillMaxSize())
            }
            Box(modifier = Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(Color.Transparent, Color.Black.copy(alpha = 0.7f)), startY = 80f)))
            Text(title, color = TextPrimary, fontSize = 11.sp, fontWeight = FontWeight.Bold, maxLines = 2, overflow = TextOverflow.Ellipsis, modifier = Modifier.padding(8.dp))
        }
        Spacer(Modifier.height(5.dp))
        Text(year ?: "", color = TextSecondary, fontSize = 11.sp)
    }
}

@Composable
private fun SectionRow(title: String, onSeeAll: () -> Unit) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Text(title, color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 17.sp)
        Text("See all", color = Accent, fontSize = 13.sp, modifier = Modifier.clickable(onClick = onSeeAll))
    }
}

@Composable
private fun EmptySection(message: String) {
    Box(
        modifier = Modifier.fillMaxWidth().padding(vertical = 24.dp, horizontal = 16.dp),
        contentAlignment = Alignment.Center,
    ) {
        Text(message, color = TextMuted, fontSize = 14.sp)
    }
}

@Composable
private fun RetryContent(onRetry: () -> Unit) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally, verticalArrangement = Arrangement.spacedBy(12.dp)) {
            Text("Could not load content", color = TextSecondary, fontSize = 15.sp)
            Button(
                onClick = onRetry,
                colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
            ) {
                Icon(Icons.Default.Refresh, null, modifier = Modifier.size(16.dp))
                Spacer(Modifier.width(6.dp))
                Text("Retry")
            }
        }
    }
}

private val paletteAccents = listOf(
    Color(0xFFF5C518), Color(0xFF46D369), Color(0xFF3498DB),
    Color(0xFF9B59B6), Color(0xFFE74C3C), Color(0xFFE67E22),
    Color(0xFF1ABC9C), Color(0xFFD4A017),
)
private val paletteBgs = listOf(
    Color(0xFF2C1810), Color(0xFF0A1A0A), Color(0xFF051A2E),
    Color(0xFF1A0A2E), Color(0xFF1A0A0A), Color(0xFF1A1208),
    Color(0xFF051A18), Color(0xFF2E2010),
)

fun posterAccentFor(uuid: String): Color = paletteAccents[Math.abs(uuid.hashCode()) % paletteAccents.size]
fun posterBgFor(uuid: String): Color    = paletteBgs[Math.abs(uuid.hashCode()) % paletteBgs.size]
