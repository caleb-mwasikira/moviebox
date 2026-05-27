package com.example.moviebox_app.ui.screens

import android.view.ViewGroup
import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.slideInHorizontally
import androidx.compose.animation.slideOutHorizontally
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.itemsIndexed
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.List
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Pause
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Subtitles
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Slider
import androidx.compose.material3.SliderDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableFloatStateOf
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.input.pointer.pointerInput
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.media3.common.Player
import androidx.media3.ui.PlayerView
import com.example.moviebox_app.data.PlayerSession
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.Surface
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.VideoPlayerViewModel
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

private enum class ActiveDrawer { None, Episodes, Subtitles }
private enum class SkipFlash { Left, Right }

@Composable
fun VideoPlayerScreen(
    url: String,
    onBack: () -> Unit,
    vm: VideoPlayerViewModel = viewModel(factory = VideoPlayerViewModel.factory(url)),
) {
    val player = vm.player
    val positionMs by vm.positionMs.collectAsState()
    val durationMs by vm.durationMs.collectAsState()
    val currentEpisodeIdx by vm.currentEpisodeIdx.collectAsState()
    val activeSubtitleIdx by vm.activeSubtitleIdx.collectAsState()

    var isBuffering by remember {
        mutableStateOf(
            player.playbackState == Player.STATE_BUFFERING || player.playbackState == Player.STATE_IDLE
        )
    }
    var isPlaying by remember { mutableStateOf(player.isPlaying) }

    DisposableEffect(player) {
        val listener = object : Player.Listener {
            override fun onPlaybackStateChanged(state: Int) {
                isBuffering = state == Player.STATE_BUFFERING || state == Player.STATE_IDLE
            }
            override fun onIsPlayingChanged(playing: Boolean) { isPlaying = playing }
        }
        player.addListener(listener)
        onDispose { player.removeListener(listener) }
    }

    var showControls by remember { mutableStateOf(true) }
    var activeDrawer by remember { mutableStateOf(ActiveDrawer.None) }
    var skipFlash by remember { mutableStateOf<SkipFlash?>(null) }
    var isDraggingSlider by remember { mutableStateOf(false) }
    var sliderDragFraction by remember { mutableFloatStateOf(0f) }

    val scope = rememberCoroutineScope()
    var hideControlsJob by remember { mutableStateOf<Job?>(null) }

    fun scheduleHide() {
        hideControlsJob?.cancel()
        hideControlsJob = scope.launch {
            delay(3_500)
            if (!isDraggingSlider && activeDrawer == ActiveDrawer.None && isPlaying) showControls = false
        }
    }

    LaunchedEffect(isPlaying) {
        if (isPlaying && showControls) scheduleHide()
    }

    // Skip flash auto-dismiss
    LaunchedEffect(skipFlash) {
        if (skipFlash != null) {
            delay(700)
            skipFlash = null
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.Black),
    ) {
        // ── Video ────────────────────────────────────────────────────────────
        AndroidView(
            factory = { ctx ->
                PlayerView(ctx).apply {
                    this.player = player
                    useController = false
                    layoutParams = ViewGroup.LayoutParams(
                        ViewGroup.LayoutParams.MATCH_PARENT,
                        ViewGroup.LayoutParams.MATCH_PARENT,
                    )
                }
            },
            update = { pv -> pv.player = player },
            modifier = Modifier.fillMaxSize(),
        )

        // ── Tap zones ────────────────────────────────────────────────────────
        Row(modifier = Modifier.fillMaxSize()) {
            // Left zone — tap to seek -15s
            Box(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxHeight()
                    .pointerInput(Unit) {
                        detectTapGestures(
                            onDoubleTap = {
                                vm.seekRelative(-15_000)
                                skipFlash = SkipFlash.Left
                                showControls = true
                                scheduleHide()
                            },
                            onTap = {
                                showControls = !showControls
                                if (showControls) scheduleHide()
                            },
                        )
                    },
            )
            // Right zone — tap to seek +15s
            Box(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxHeight()
                    .pointerInput(Unit) {
                        detectTapGestures(
                            onDoubleTap = {
                                vm.seekRelative(15_000)
                                skipFlash = SkipFlash.Right
                                showControls = true
                                scheduleHide()
                            },
                            onTap = {
                                showControls = !showControls
                                if (showControls) scheduleHide()
                            },
                        )
                    },
            )
        }

        // ── Buffering spinner ─────────────────────────────────────────────────
        if (isBuffering) {
            CircularProgressIndicator(
                color = Accent,
                modifier = Modifier.align(Alignment.Center),
            )
        }

        // ── Skip flash indicators ─────────────────────────────────────────────
        AnimatedVisibility(
            visible = skipFlash == SkipFlash.Left,
            enter = fadeIn(tween(100)),
            exit = fadeOut(tween(400)),
            modifier = Modifier.align(Alignment.CenterStart).padding(start = 32.dp),
        ) {
            SkipIndicator("◄◄  15s")
        }
        AnimatedVisibility(
            visible = skipFlash == SkipFlash.Right,
            enter = fadeIn(tween(100)),
            exit = fadeOut(tween(400)),
            modifier = Modifier.align(Alignment.CenterEnd).padding(end = 32.dp),
        ) {
            SkipIndicator("15s  ►►")
        }

        // ── Controls overlay ──────────────────────────────────────────────────
        AnimatedVisibility(
            visible = showControls || !isPlaying,
            enter = fadeIn(tween(200)),
            exit = fadeOut(tween(300)),
            modifier = Modifier.fillMaxSize(),
        ) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black.copy(alpha = 0.45f)),
            ) {
                // Top bar
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 4.dp, vertical = 4.dp)
                        .align(Alignment.TopStart),
                    verticalAlignment = Alignment.CenterVertically,
                ) {
                    IconButton(onClick = onBack) {
                        Box(
                            modifier = Modifier
                                .size(36.dp)
                                .clip(CircleShape)
                                .background(Color.Black.copy(alpha = 0.5f)),
                            contentAlignment = Alignment.Center,
                        ) {
                            Icon(
                                Icons.AutoMirrored.Filled.ArrowBack,
                                contentDescription = null,
                                tint = Color.White,
                                modifier = Modifier.size(20.dp),
                            )
                        }
                    }
                    Spacer(Modifier.width(8.dp))
                    val title = if (PlayerSession.currentEpisodeIdx >= 0) {
                        val ep = PlayerSession.episodes.getOrNull(currentEpisodeIdx)
                        ep?.let { "E${it.episodeNumber} — ${it.title}" } ?: PlayerSession.title
                    } else {
                        PlayerSession.title
                    }
                    Text(
                        text = title,
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.SemiBold,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis,
                        modifier = Modifier.weight(1f),
                    )
                }

                // Center play/pause button
                Box(
                    modifier = Modifier
                        .size(64.dp)
                        .clip(CircleShape)
                        .background(Color.Black.copy(alpha = 0.5f))
                        .align(Alignment.Center)
                        .clickable {
                            if (isPlaying) player.pause() else player.play()
                            scheduleHide()
                        },
                    contentAlignment = Alignment.Center,
                ) {
                    Icon(
                        if (isPlaying) Icons.Default.Pause else Icons.Default.PlayArrow,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(32.dp),
                    )
                }

                // Bottom bar
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .align(Alignment.BottomStart)
                        .padding(horizontal = 12.dp, vertical = 8.dp),
                ) {
                    // Slider
                    val fraction = if (durationMs > 0 && !isDraggingSlider) {
                        positionMs.toFloat() / durationMs
                    } else {
                        sliderDragFraction
                    }
                    Slider(
                        value = fraction.coerceIn(0f, 1f),
                        onValueChange = { v ->
                            isDraggingSlider = true
                            sliderDragFraction = v
                            hideControlsJob?.cancel()
                        },
                        onValueChangeFinished = {
                            vm.seekTo((sliderDragFraction * durationMs).toLong())
                            isDraggingSlider = false
                            scheduleHide()
                        },
                        colors = SliderDefaults.colors(
                            thumbColor = Accent,
                            activeTrackColor = Accent,
                            inactiveTrackColor = Color.White.copy(alpha = 0.3f),
                        ),
                        modifier = Modifier.fillMaxWidth(),
                    )

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically,
                    ) {
                        Text(
                            text = formatDuration(positionMs),
                            color = TextSecondary,
                            fontSize = 12.sp,
                        )
                        Text(
                            text = formatDuration(durationMs),
                            color = TextSecondary,
                            fontSize = 12.sp,
                        )
                    }
                }
            }
        }

        // ── Persistent action buttons — always visible, above scrubber ─────────
        Row(
            modifier = Modifier
                .align(Alignment.BottomEnd)
                .padding(bottom = 88.dp, end = 12.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            verticalAlignment = Alignment.CenterVertically,
        ) {
            // CC pill — always shown so it stays next to the episodes pill
            val hasSubtitles = PlayerSession.subtitles.isNotEmpty()
            val subActive    = hasSubtitles && activeSubtitleIdx >= 0
            Row(
                modifier = Modifier
                    .clip(RoundedCornerShape(20.dp))
                    .background(
                        when {
                            subActive    -> Accent.copy(alpha = 0.9f)
                            hasSubtitles -> Color.Black.copy(alpha = 0.65f)
                            else         -> Color.Black.copy(alpha = 0.30f)
                        }
                    )
                    .clickable(enabled = hasSubtitles) {
                        activeDrawer = ActiveDrawer.Subtitles
                        hideControlsJob?.cancel()
                    }
                    .padding(horizontal = 12.dp, vertical = 7.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(5.dp),
            ) {
                Icon(
                    Icons.Default.Subtitles,
                    contentDescription = null,
                    tint = when {
                        subActive    -> Color.Black
                        hasSubtitles -> Color.White
                        else         -> Color.White.copy(alpha = 0.35f)
                    },
                    modifier = Modifier.size(15.dp),
                )
                Text(
                    text = when {
                        subActive    -> PlayerSession.subtitles.getOrNull(activeSubtitleIdx)?.displayCode() ?: "CC"
                        else         -> "CC"
                    },
                    color = when {
                        subActive    -> Color.Black
                        hasSubtitles -> Color.White
                        else         -> Color.White.copy(alpha = 0.35f)
                    },
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                )
            }

            // Episodes pill
            if (PlayerSession.episodes.isNotEmpty()) {
                Row(
                    modifier = Modifier
                        .clip(RoundedCornerShape(20.dp))
                        .background(Color.Black.copy(alpha = 0.65f))
                        .clickable {
                            activeDrawer = ActiveDrawer.Episodes
                            hideControlsJob?.cancel()
                        }
                        .padding(horizontal = 12.dp, vertical = 7.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(5.dp),
                ) {
                    Icon(
                        Icons.AutoMirrored.Filled.List,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(15.dp),
                    )
                    Text(
                        "E${(currentEpisodeIdx + 1).coerceAtLeast(1)}",
                        color = Color.White,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                    )
                }
            }
        }

        // ── Drawers ───────────────────────────────────────────────────────────
        AnimatedVisibility(
            visible = activeDrawer != ActiveDrawer.None,
            enter = fadeIn(tween(150)),
            exit = fadeOut(tween(150)),
        ) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black.copy(alpha = 0.5f))
                    .clickable { activeDrawer = ActiveDrawer.None; scheduleHide() },
            )
        }

        AnimatedVisibility(
            visible = activeDrawer == ActiveDrawer.Episodes,
            enter = slideInHorizontally(tween(280)) { it },
            exit = slideOutHorizontally(tween(280)) { it },
            modifier = Modifier.align(Alignment.CenterEnd),
        ) {
            EpisodesDrawer(
                currentIdx = currentEpisodeIdx,
                onSelectEpisode = { idx ->
                    vm.switchEpisode(idx)
                    activeDrawer = ActiveDrawer.None
                    showControls = true
                    scheduleHide()
                },
                onClose = { activeDrawer = ActiveDrawer.None; scheduleHide() },
            )
        }

        AnimatedVisibility(
            visible = activeDrawer == ActiveDrawer.Subtitles,
            enter = slideInHorizontally(tween(280)) { it },
            exit = slideOutHorizontally(tween(280)) { it },
            modifier = Modifier.align(Alignment.CenterEnd),
        ) {
            SubtitlesDrawer(
                currentIdx    = activeSubtitleIdx,
                onSelectTrack = { idx ->
                    vm.selectSubtitleTrack(idx)
                    activeDrawer = ActiveDrawer.None
                    scheduleHide()
                },
                onClose = { activeDrawer = ActiveDrawer.None; scheduleHide() },
            )
        }
    }
}

@Composable
private fun SkipIndicator(label: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(8.dp))
            .background(Color.Black.copy(alpha = 0.6f))
            .padding(horizontal = 12.dp, vertical = 8.dp),
        contentAlignment = Alignment.Center,
    ) {
        Text(label, color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
    }
}

@Composable
private fun EpisodesDrawer(
    currentIdx: Int,
    onSelectEpisode: (Int) -> Unit,
    onClose: () -> Unit,
) {
    Column(
        modifier = Modifier
            .fillMaxHeight()
            .width(280.dp)
            .background(Surface)
            .clickable(enabled = false) {},
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            Text("Episodes", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 16.sp)
            IconButton(onClick = onClose, modifier = Modifier.size(32.dp)) {
                Icon(Icons.Default.Close, null, tint = TextSecondary, modifier = Modifier.size(18.dp))
            }
        }
        HorizontalDivider(color = DividerColor)

        LazyColumn {
            itemsIndexed(PlayerSession.episodes) { idx, ep ->
                val isActive = idx == currentIdx
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable { onSelectEpisode(idx) }
                        .background(if (isActive) Accent.copy(alpha = 0.12f) else Color.Transparent)
                        .padding(horizontal = 16.dp, vertical = 12.dp),
                    verticalAlignment = Alignment.CenterVertically,
                ) {
                    Box(
                        modifier = Modifier
                            .size(32.dp)
                            .clip(CircleShape)
                            .background(if (isActive) Accent else CardBg),
                        contentAlignment = Alignment.Center,
                    ) {
                        Text(
                            ep.episodeNumber.toString(),
                            color = if (isActive) Color.Black else TextSecondary,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                        )
                    }
                    Spacer(Modifier.width(12.dp))
                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            "E${ep.episodeNumber} — ${ep.title}",
                            color = if (isActive) Accent else TextPrimary,
                            fontSize = 13.sp,
                            fontWeight = if (isActive) FontWeight.SemiBold else FontWeight.Normal,
                            maxLines = 2,
                            overflow = TextOverflow.Ellipsis,
                        )
                        val dur = ep.formattedDuration()
                        if (dur.isNotEmpty()) {
                            Spacer(Modifier.height(2.dp))
                            Text(dur, color = TextMuted, fontSize = 11.sp)
                        }
                    }
                }
                if (idx < PlayerSession.episodes.lastIndex) {
                    HorizontalDivider(color = DividerColor.copy(alpha = 0.5f), modifier = Modifier.padding(horizontal = 16.dp))
                }
            }
        }
    }
}

@Composable
private fun SubtitlesDrawer(
    currentIdx: Int,
    onSelectTrack: (Int) -> Unit,
    onClose: () -> Unit,
) {
    val offActive = currentIdx < 0

    Column(
        modifier = Modifier
            .fillMaxHeight()
            .width(260.dp)
            .background(Surface)
            .clickable(enabled = false) {},
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            Text("Subtitles", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 16.sp)
            IconButton(onClick = onClose, modifier = Modifier.size(32.dp)) {
                Icon(Icons.Default.Close, null, tint = TextSecondary, modifier = Modifier.size(18.dp))
            }
        }
        HorizontalDivider(color = DividerColor)

        // Off option
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .clickable { onSelectTrack(-1) }
                .padding(horizontal = 16.dp, vertical = 14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            Text(
                "Off",
                color = if (offActive) Accent else TextSecondary,
                fontSize = 14.sp,
                fontWeight = if (offActive) FontWeight.Bold else FontWeight.Normal,
            )
            if (offActive) {
                Icon(
                    Icons.Default.Check,
                    contentDescription = null,
                    tint = Accent,
                    modifier = Modifier.size(16.dp),
                )
            }
        }

        PlayerSession.subtitles.forEachIndexed { idx, sub ->
            HorizontalDivider(color = DividerColor.copy(alpha = 0.5f), modifier = Modifier.padding(horizontal = 16.dp))
            val isActive = idx == currentIdx
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .clickable { onSelectTrack(idx) }
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically,
            ) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(10.dp),
                    modifier = Modifier.weight(1f),
                ) {
                    // Language code badge — mirrors the web's srclang badge
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(3.dp))
                            .background(if (isActive) Accent else CardBg)
                            .padding(horizontal = 5.dp, vertical = 2.dp),
                        contentAlignment = Alignment.Center,
                    ) {
                        Text(
                            sub.displayCode(),
                            color = if (isActive) Color.Black else TextMuted,
                            fontSize = 9.sp,
                            fontWeight = FontWeight.ExtraBold,
                        )
                    }
                    Text(
                        sub.label,
                        color = if (isActive) Color.White else TextSecondary,
                        fontSize = 13.sp,
                        fontWeight = if (isActive) FontWeight.SemiBold else FontWeight.Normal,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis,
                    )
                }
                if (isActive) {
                    Icon(
                        Icons.Default.Check,
                        contentDescription = null,
                        tint = Accent,
                        modifier = Modifier.size(16.dp),
                    )
                }
            }
        }
    }
}

private fun formatDuration(ms: Long): String {
    if (ms <= 0) return "0:00"
    val totalSeconds = ms / 1000
    val h = totalSeconds / 3600
    val m = (totalSeconds % 3600) / 60
    val s = totalSeconds % 60
    return if (h > 0) "%d:%02d:%02d".format(h, m, s) else "%d:%02d".format(m, s)
}
