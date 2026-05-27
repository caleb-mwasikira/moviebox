package com.example.moviebox_app.data

import android.content.Context
import android.content.SharedPreferences

class WatchProgressStore(context: Context) {

    private val prefs: SharedPreferences =
        context.getSharedPreferences("moviebox_progress", Context.MODE_PRIVATE)

    data class Progress(val positionMs: Long, val durationMs: Long) {
        val fraction: Float get() = if (durationMs > 0) positionMs.toFloat() / durationMs else 0f
        val isFinished: Boolean get() = fraction >= 0.95f
    }

    fun save(contentUuid: String, positionMs: Long, durationMs: Long) {
        if (durationMs <= 0) return
        prefs.edit()
            .putLong("${contentUuid}_pos", positionMs)
            .putLong("${contentUuid}_dur", durationMs)
            .apply()
    }

    fun get(contentUuid: String): Progress? {
        val pos = prefs.getLong("${contentUuid}_pos", -1L)
        val dur = prefs.getLong("${contentUuid}_dur", -1L)
        if (pos < 0 || dur <= 0) return null
        return Progress(pos, dur)
    }

    fun clear(contentUuid: String) {
        prefs.edit()
            .remove("${contentUuid}_pos")
            .remove("${contentUuid}_dur")
            .apply()
    }
}
