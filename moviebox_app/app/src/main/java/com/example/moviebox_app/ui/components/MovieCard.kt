package com.example.moviebox_app.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.moviebox_app.data.Movie
import com.example.moviebox_app.data.Series
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary

@Composable
fun PosterCard(
    title: String,
    year: Int,
    posterColor: Color,
    accentColor: Color,
    width: Dp = 120.dp,
    onClick: () -> Unit,
) {
    Column(
        modifier = Modifier
            .width(width)
            .clickable(onClick = onClick),
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .aspectRatio(0.67f)
                .clip(RoundedCornerShape(10.dp))
                .background(
                    Brush.verticalGradient(
                        listOf(accentColor.copy(alpha = 0.6f), posterColor)
                    )
                ),
            contentAlignment = Alignment.BottomStart,
        ) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        Brush.verticalGradient(
                            listOf(Color.Transparent, Color.Black.copy(alpha = 0.7f)),
                            startY = 80f,
                        )
                    )
            )
            Text(
                text = title,
                color = TextPrimary,
                fontSize = 11.sp,
                fontWeight = FontWeight.Bold,
                maxLines = 2,
                overflow = TextOverflow.Ellipsis,
                modifier = Modifier.padding(8.dp),
            )
        }
        Spacer(Modifier.height(6.dp))
        Text(
            text = year.toString(),
            color = TextSecondary,
            fontSize = 11.sp,
        )
    }
}

@Composable
fun MoviePosterCard(movie: Movie, width: Dp = 120.dp, onClick: () -> Unit) {
    PosterCard(
        title       = movie.title,
        year        = movie.year,
        posterColor = movie.posterColor,
        accentColor = movie.posterAccent,
        width       = width,
        onClick     = onClick,
    )
}

@Composable
fun SeriesPosterCard(series: Series, width: Dp = 120.dp, onClick: () -> Unit) {
    PosterCard(
        title       = series.title,
        year        = series.year,
        posterColor = series.posterColor,
        accentColor = series.posterAccent,
        width       = width,
        onClick     = onClick,
    )
}
