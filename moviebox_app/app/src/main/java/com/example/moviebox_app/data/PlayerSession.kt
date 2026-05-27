package com.example.moviebox_app.data

import com.example.moviebox_app.data.api.ApiEpisode
import com.example.moviebox_app.data.api.ApiSubtitle

object PlayerSession {
    var title: String = ""
    var subtitles: List<ApiSubtitle> = emptyList()
    var contentUuid: String = ""
    var episodes: List<ApiEpisode> = emptyList()
    var currentEpisodeIdx: Int = -1        // -1 for movies
    var initialPositionMs: Long = 0L
}
