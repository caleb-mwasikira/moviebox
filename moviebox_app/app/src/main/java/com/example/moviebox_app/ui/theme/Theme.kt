package com.example.moviebox_app.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.runtime.Composable

private val AppColorScheme = darkColorScheme(
    primary        = Accent,
    onPrimary      = Background,
    secondary      = AccentDim,
    onSecondary    = TextPrimary,
    background     = Background,
    onBackground   = TextPrimary,
    surface        = Surface,
    onSurface      = TextPrimary,
    surfaceVariant = CardBg,
    onSurfaceVariant = TextSecondary,
)

@Composable
fun Moviebox_appTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = AppColorScheme,
        typography  = Typography,
        content     = content,
    )
}
