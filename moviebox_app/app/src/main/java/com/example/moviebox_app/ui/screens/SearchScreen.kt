package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ExperimentalLayoutApi
import androidx.compose.foundation.layout.FlowRow
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
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.GridItemSpan
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.BasicTextField
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.FilterChip
import androidx.compose.material3.FilterChipDefaults
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
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.SolidColor
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.TextStyle
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
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.SearchData
import com.example.moviebox_app.ui.viewmodel.SearchViewModel

private val genres = listOf("All", "Action", "Drama", "Thriller", "Comedy", "Sci-Fi", "Horror", "Biography", "Crime")

@OptIn(ExperimentalLayoutApi::class)
@Composable
fun SearchScreen(
    onMovieClick: (String) -> Unit,
    onSeriesClick: (String) -> Unit = {},
    vm: SearchViewModel = viewModel(),
) {
    val state by vm.state.collectAsState()
    val query by vm.query.collectAsState()
    val genre by vm.genre.collectAsState()
    var toastMessage by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(state) {
        if (state is UiState.Error) toastMessage = (state as UiState.Error).message
    }

    Box(modifier = Modifier.fillMaxSize()) {
        Column(modifier = Modifier.fillMaxSize().background(Background).padding(top = 16.dp)) {

            Text(
                "Search",
                color = TextPrimary,
                fontWeight = FontWeight.ExtraBold,
                fontSize = 22.sp,
                modifier = Modifier.padding(horizontal = 16.dp),
            )

            Spacer(Modifier.height(16.dp))

            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp)
                    .clip(RoundedCornerShape(12.dp))
                    .background(CardBg)
                    .padding(horizontal = 14.dp, vertical = 12.dp),
                contentAlignment = Alignment.CenterStart,
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Search, null, tint = TextMuted, modifier = Modifier.size(18.dp))
                    Spacer(Modifier.width(8.dp))
                    Box(modifier = Modifier.weight(1f)) {
                        if (query.isEmpty()) Text("Search movies, series, genre…", color = TextMuted, fontSize = 14.sp)
                        BasicTextField(
                            value = query,
                            onValueChange = vm::onQueryChange,
                            textStyle = TextStyle(color = TextPrimary, fontSize = 14.sp),
                            cursorBrush = SolidColor(Accent),
                            modifier = Modifier.fillMaxWidth(),
                        )
                    }
                    if (query.isNotEmpty()) {
                        IconButton(onClick = { vm.onQueryChange("") }, modifier = Modifier.size(20.dp)) {
                            Icon(Icons.Default.Close, null, tint = TextMuted, modifier = Modifier.size(16.dp))
                        }
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            FlowRow(
                modifier = Modifier.padding(horizontal = 12.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                genres.forEach { g ->
                    val selected = g == genre
                    FilterChip(
                        selected = selected,
                        onClick  = { vm.onGenreChange(g) },
                        label    = { Text(g, fontSize = 12.sp) },
                        colors   = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = Accent,
                            selectedLabelColor     = Color.Black,
                            containerColor         = TagBg,
                            labelColor             = TextSecondary,
                        ),
                        border = FilterChipDefaults.filterChipBorder(
                            enabled = true, selected = selected,
                            borderColor = Color.Transparent, selectedBorderColor = Color.Transparent,
                        ),
                    )
                }
            }

            Spacer(Modifier.height(16.dp))

            when (val s = state) {
                is UiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = Accent)
                }

                is UiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text("Could not load results", color = TextSecondary, fontSize = 14.sp)
                }

                is UiState.Success -> SearchResults(
                    data         = s.data,
                    query        = query,
                    onMovieClick = onMovieClick,
                    onSeriesClick = onSeriesClick,
                )
            }
        }

        TopToast(message = toastMessage, onDismiss = { toastMessage = null })
    }
}

@Composable
private fun SearchResults(
    data: SearchData,
    query: String,
    onMovieClick: (String) -> Unit,
    onSeriesClick: (String) -> Unit,
) {
    val hasMovies = data.movies.isNotEmpty()
    val hasSeries = data.series.isNotEmpty()

    if (!hasMovies && !hasSeries) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text(
                    "No results${if (query.isNotEmpty()) " for \"$query\"" else ""}",
                    color = TextSecondary,
                    fontSize = 15.sp,
                )
                Spacer(Modifier.height(8.dp))
                Text("Try a different title or genre", color = TextMuted, fontSize = 13.sp)
            }
        }
        return
    }

    LazyVerticalGrid(
        columns = GridCells.Fixed(3),
        contentPadding = PaddingValues(horizontal = 12.dp, vertical = 8.dp),
        horizontalArrangement = Arrangement.spacedBy(10.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp),
    ) {
        if (hasMovies) {
            item(span = { GridItemSpan(maxLineSpan) }) {
                SearchSectionHeader("Movies (${data.movies.size})")
            }
            items(data.movies, key = { it.uuid }) { movie ->
                SearchPosterCard(
                    title        = movie.title,
                    year         = movie.year?.toString(),
                    thumbnailUrl = movie.thumbnailUrl,
                    uuid         = movie.uuid,
                    onClick      = { onMovieClick(movie.uuid) },
                )
            }
        }

        if (hasSeries) {
            item(span = { GridItemSpan(maxLineSpan) }) {
                SearchSectionHeader("Series (${data.series.size})")
            }
            items(data.series, key = { it.uuid }) { series ->
                SearchPosterCard(
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
private fun SearchSectionHeader(title: String) {
    Text(
        title,
        color = TextPrimary,
        fontWeight = FontWeight.Bold,
        fontSize = 15.sp,
        modifier = Modifier.padding(start = 4.dp, top = 4.dp, bottom = 4.dp),
    )
}

@Composable
private fun SearchPosterCard(
    title: String,
    year: String?,
    thumbnailUrl: String?,
    uuid: String,
    onClick: () -> Unit,
) {
    val accent = posterAccentFor(uuid)
    val bg     = posterBgFor(uuid)

    Column(modifier = Modifier.clickable(onClick = onClick)) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .aspectRatio(0.67f)
                .clip(RoundedCornerShape(10.dp))
                .background(Brush.verticalGradient(listOf(accent.copy(alpha = 0.6f), bg))),
            contentAlignment = Alignment.BottomStart,
        ) {
            thumbnailUrl?.let { url ->
                AsyncImage(
                    model = url,
                    contentDescription = null,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxSize(),
                )
            }
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Brush.verticalGradient(listOf(Color.Transparent, Color.Black.copy(alpha = 0.7f)), startY = 80f)),
            )
            Text(
                title,
                color = TextPrimary,
                fontSize = 11.sp,
                fontWeight = FontWeight.Bold,
                maxLines = 2,
                overflow = TextOverflow.Ellipsis,
                modifier = Modifier.padding(8.dp),
            )
        }
        Spacer(Modifier.height(6.dp))
        Text(year ?: "", color = TextSecondary, fontSize = 11.sp)
    }
}
