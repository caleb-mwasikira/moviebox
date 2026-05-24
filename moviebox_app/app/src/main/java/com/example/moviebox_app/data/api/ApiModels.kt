package com.example.moviebox_app.data.api

import com.google.gson.annotations.SerializedName

// ── Search ────────────────────────────────────────────────────────────────────

data class SearchResult(
    val movies: List<ApiMovie>,
    val series: List<ApiSeries>,
)

// ── Pagination wrapper ────────────────────────────────────────────────────────

data class PageMeta(
    @SerializedName("current_page") val currentPage: Int = 1,
    @SerializedName("last_page")    val lastPage: Int    = 1,
    val total: Int = 0,
)

// ── Movies ────────────────────────────────────────────────────────────────────

data class ApiMovieList(
    val data: List<ApiMovie>,
    val meta: PageMeta = PageMeta(),
)

data class ApiMovieDetail(
    val data: ApiMovie,
)

data class ApiMovie(
    val uuid: String,
    val title: String,
    val description: String?                            = null,
    val genre: String?                                  = null,
    val year: Int?                                      = null,
    val duration: Int?                                  = null,
    @SerializedName("is_free")      val isFree: Boolean = false,
    @SerializedName("featured")     val featured: Boolean = false,
    @SerializedName("thumbnail_url") val thumbnailUrl: String? = null,
    @SerializedName("has_stream")   val hasStream: Boolean = false,
    @SerializedName("created_at")   val createdAt: String? = null,
) {
    fun genreList(): List<String> = genre
        ?.split(",")
        ?.map { it.trim() }
        ?.filter { it.isNotEmpty() }
        ?: emptyList()

    fun formattedDuration(): String {
        val mins = duration ?: return ""
        val h = mins / 60
        val m = mins % 60
        return if (h > 0) "${h}h ${m}m" else "${m}m"
    }
}

// ── Series ────────────────────────────────────────────────────────────────────

data class ApiSeriesList(
    val data: List<ApiSeries>,
    val meta: PageMeta = PageMeta(),
)

data class ApiSeriesDetail(
    val data: ApiSeries,
)

data class ApiSeries(
    val uuid: String,
    val title: String,
    val description: String?                              = null,
    val genre: String?                                    = null,
    val year: Int?                                        = null,
    @SerializedName("thumbnail_url") val thumbnailUrl: String? = null,
    val seasons: List<ApiSeason>?                         = null,
    @SerializedName("created_at")   val createdAt: String? = null,
) {
    fun genreList(): List<String> = genre
        ?.split(",")
        ?.map { it.trim() }
        ?.filter { it.isNotEmpty() }
        ?: emptyList()

    fun seasonCount(): Int = seasons?.size ?: 0
}

data class ApiSeason(
    val id: Int,
    @SerializedName("season_number") val seasonNumber: Int,
    val title: String? = null,
    val episodes: List<ApiEpisode>? = null,
)

data class ApiEpisode(
    val uuid: String,
    @SerializedName("episode_number") val episodeNumber: Int,
    val title: String,
    val description: String? = null,
    val duration: Int?       = null,
    @SerializedName("is_free")    val isFree: Boolean    = false,
    @SerializedName("has_stream") val hasStream: Boolean = false,
) {
    fun formattedDuration(): String {
        val mins = duration ?: return ""
        val h = mins / 60
        val m = mins % 60
        return if (h > 0) "${h}h ${m}m" else "${m}m"
    }
}
