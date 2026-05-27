package com.example.moviebox_app.ui.viewmodel

import android.app.Application
import android.net.Uri
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import androidx.lifecycle.viewmodel.CreationExtras
import androidx.media3.common.C
import androidx.media3.common.MediaItem
import androidx.media3.common.TrackSelectionOverride
import androidx.media3.datasource.DefaultHttpDataSource
import androidx.media3.exoplayer.ExoPlayer
import androidx.media3.exoplayer.source.DefaultMediaSourceFactory
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.PlayerSession
import com.example.moviebox_app.data.buildStreamingToken
import com.example.moviebox_app.data.resolveStreamingUrl
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class VideoPlayerViewModel(
    application: Application,
    initialUrl: String,
) : AndroidViewModel(application) {

    val player: ExoPlayer
    private val progressStore = MovieBoxApplication.progressStore

    private val _positionMs = MutableStateFlow(PlayerSession.initialPositionMs)
    val positionMs: StateFlow<Long> = _positionMs

    private val _durationMs = MutableStateFlow(0L)
    val durationMs: StateFlow<Long> = _durationMs

    private val _currentEpisodeIdx = MutableStateFlow(PlayerSession.currentEpisodeIdx)
    val currentEpisodeIdx: StateFlow<Int> = _currentEpisodeIdx

    private val _activeSubtitleIdx = MutableStateFlow(
        PlayerSession.subtitles.indexOfFirst { it.isDefault }.takeIf { it >= 0 } ?: -1
    )
    val activeSubtitleIdx: StateFlow<Int> = _activeSubtitleIdx

    private var currentContentUuid = PlayerSession.contentUuid
    private var lastSaveMs = 0L

    init {
        val httpDataSourceFactory = DefaultHttpDataSource.Factory()
            .setDefaultRequestProperties(buildHeaders())

        player = ExoPlayer.Builder(application)
            .setMediaSourceFactory(DefaultMediaSourceFactory(httpDataSourceFactory))
            .build()
            .apply {
                setMediaItem(buildMediaItem(initialUrl))
                prepare()
                playWhenReady = true
                if (PlayerSession.initialPositionMs > 0) seekTo(PlayerSession.initialPositionMs)
            }

        startPositionTracking()
    }

    private fun buildHeaders(): Map<String, String> {
        val userId = try { MovieBoxApplication.sessionManager.getUserId() } catch (_: Exception) { null }
        return buildMap { if (userId != null) put("Authorization", "Bearer ${buildStreamingToken(userId)}") }
    }

    private fun buildMediaItem(url: String): MediaItem {
        val subtitleConfigs = PlayerSession.subtitles.map { sub ->
            MediaItem.SubtitleConfiguration.Builder(Uri.parse(sub.url))
                .setMimeType("text/vtt")
                .setLanguage(sub.languageCode ?: sub.language)
                .setLabel(sub.label)
                .setSelectionFlags(if (sub.isDefault) C.SELECTION_FLAG_DEFAULT else 0)
                .build()
        }
        return MediaItem.Builder()
            .setUri(url)
            .setSubtitleConfigurations(subtitleConfigs)
            .build()
    }

    private fun startPositionTracking() {
        viewModelScope.launch {
            while (true) {
                delay(500)
                _positionMs.value = player.currentPosition
                val dur = player.duration.takeIf { it > 0 } ?: 0L
                _durationMs.value = dur
                val now = System.currentTimeMillis()
                if (currentContentUuid.isNotEmpty() && dur > 0 && now - lastSaveMs >= 5_000) {
                    progressStore.save(currentContentUuid, player.currentPosition, dur)
                    lastSaveMs = now
                }
            }
        }
    }

    fun seekRelative(offsetMs: Long) {
        player.seekTo((player.currentPosition + offsetMs).coerceAtLeast(0))
    }

    fun seekTo(positionMs: Long) {
        player.seekTo(positionMs)
    }

    fun switchEpisode(episodeIdx: Int) {
        val episode = PlayerSession.episodes.getOrNull(episodeIdx) ?: return
        val rawUrl = episode.streamingUrl ?: return
        val resolvedUrl = resolveStreamingUrl(rawUrl) ?: return

        // Save progress for current episode
        val dur = player.duration.takeIf { it > 0 } ?: 0L
        if (currentContentUuid.isNotEmpty() && dur > 0) {
            progressStore.save(currentContentUuid, player.currentPosition, dur)
        }

        _currentEpisodeIdx.value = episodeIdx
        PlayerSession.currentEpisodeIdx = episodeIdx
        currentContentUuid = episode.uuid
        _activeSubtitleIdx.value = PlayerSession.subtitles.indexOfFirst { it.isDefault }.takeIf { it >= 0 } ?: -1

        // Restore any saved progress for the new episode
        val savedProgress = progressStore.get(episode.uuid)

        player.setMediaItem(buildMediaItem(resolvedUrl))
        player.prepare()
        player.playWhenReady = true
        if (savedProgress != null && !savedProgress.isFinished) {
            player.seekTo(savedProgress.positionMs)
        }
    }

    fun selectSubtitleTrack(idx: Int) {
        val textGroups = player.currentTracks.groups.filter { it.type == C.TRACK_TYPE_TEXT }
        val params = if (idx < 0) {
            player.trackSelectionParameters.buildUpon()
                .setTrackTypeDisabled(C.TRACK_TYPE_TEXT, true)
                .build()
        } else if (idx < textGroups.size) {
            player.trackSelectionParameters.buildUpon()
                .setTrackTypeDisabled(C.TRACK_TYPE_TEXT, false)
                .addOverride(TrackSelectionOverride(textGroups[idx].mediaTrackGroup, 0))
                .build()
        } else return
        player.trackSelectionParameters = params
        _activeSubtitleIdx.value = idx
    }

    override fun onCleared() {
        val dur = player.duration.takeIf { it > 0 } ?: 0L
        if (currentContentUuid.isNotEmpty() && dur > 0) {
            progressStore.save(currentContentUuid, player.currentPosition, dur)
        }
        player.release()
    }

    companion object {
        fun factory(url: String): ViewModelProvider.Factory = object : ViewModelProvider.Factory {
            @Suppress("UNCHECKED_CAST")
            override fun <T : ViewModel> create(modelClass: Class<T>, extras: CreationExtras): T {
                val app = checkNotNull(extras[ViewModelProvider.AndroidViewModelFactory.APPLICATION_KEY])
                return VideoPlayerViewModel(app, url) as T
            }
        }
    }
}
