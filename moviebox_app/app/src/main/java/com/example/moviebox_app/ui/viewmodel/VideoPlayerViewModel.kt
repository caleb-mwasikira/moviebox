package com.example.moviebox_app.ui.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewmodel.CreationExtras
import androidx.media3.common.MediaItem
import androidx.media3.datasource.DefaultHttpDataSource
import androidx.media3.exoplayer.ExoPlayer
import androidx.media3.exoplayer.source.DefaultMediaSourceFactory
import com.example.moviebox_app.MovieBoxApplication
import com.example.moviebox_app.data.buildStreamingToken

class VideoPlayerViewModel(
    application: Application,
    url: String,
) : AndroidViewModel(application) {

    val player: ExoPlayer

    init {
        val userId = try { MovieBoxApplication.sessionManager.getUserId() } catch (_: Exception) { null }
        val headers = buildMap<String, String> {
            if (userId != null) put("Authorization", "Bearer ${buildStreamingToken(userId)}")
        }
        val httpDataSourceFactory = DefaultHttpDataSource.Factory()
            .setDefaultRequestProperties(headers)

        player = ExoPlayer.Builder(application)
            .setMediaSourceFactory(DefaultMediaSourceFactory(httpDataSourceFactory))
            .build()
            .apply {
                setMediaItem(MediaItem.fromUri(url))
                prepare()
                playWhenReady = true
            }
    }

    override fun onCleared() {
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
