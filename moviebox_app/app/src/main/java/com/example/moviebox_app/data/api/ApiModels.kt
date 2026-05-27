package com.example.moviebox_app.data.api

import com.google.gson.annotations.SerializedName

// ── Subscription ──────────────────────────────────────────────────────────────

data class ApiSubscriptionPackage(
    val uuid: String,
    val name: String,
    val description: String?                                   = null,
    val currency: String                                       = "KES",
    val price: Double,
    val interval: String,                                      // monthly, yearly
    @SerializedName("trial_period_days") val trialDays: Int   = 0,
    @SerializedName("max_screens")       val maxScreens: Int  = 1,
    @SerializedName("max_resolution")    val maxResolution: String? = null,
    @SerializedName("has_ads")           val hasAds: Boolean  = true,
    @SerializedName("download_enabled")  val downloadEnabled: Boolean = false,
    @SerializedName("watch_party_enabled") val watchPartyEnabled: Boolean = false,
) {
    fun formattedPrice(): String = "${currency} ${price.toLong()}"
}

data class ApiPackageList(val data: List<ApiSubscriptionPackage>)

data class ApiCurrentSubscription(
    val `package`: ApiSubscriptionPackage,
    val status: String,                   // active, expired, cancelled
    @SerializedName("starts_at") val startsAt: String?,
    @SerializedName("expires_at") val expiresAt: String?,
) {
    val isActive: Boolean get() = status == "active"
}

data class ApiCurrentSubWrapper(val data: ApiCurrentSubscription?)

data class SubscriptionInitiateRequest(
    val phone: String,
    @SerializedName("package_uuid") val packageUuid: String,
)

data class SubscriptionInitiateResponse(
    val success: Boolean,
    @SerializedName("checkout_request_id") val checkoutRequestId: String?,
    val message: String,
)

data class SubscriptionStatusResponse(
    val status: String,           // pending, completed, failed, cancelled
    val receipt: String?,
    val message: String?,
)

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

// ── Subtitles ─────────────────────────────────────────────────────────────────

data class ApiSubtitle(
    val id: Int,
    val label: String,
    val language: String,
    @SerializedName("language_code") val languageCode: String? = null,
    val url: String,
    @SerializedName("is_default") val isDefault: Boolean = false,
) {
    fun displayCode(): String = (languageCode ?: language).take(3).uppercase()
}

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
    @SerializedName("has_stream")    val hasStream: Boolean  = false,
    @SerializedName("streaming_url") val streamingUrl: String? = null,
    @SerializedName("trailer_url")   val trailerUrl: String?   = null,
    @SerializedName("created_at")    val createdAt: String?    = null,
    val subtitles: List<ApiSubtitle>?                         = null,
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
    val description: String?                               = null,
    val genre: String?                                     = null,
    val year: Int?                                         = null,
    @SerializedName("featured")      val featured: Boolean     = false,
    @SerializedName("coming_soon")   val comingSoon: Boolean   = false,
    @SerializedName("thumbnail_url") val thumbnailUrl: String? = null,
    @SerializedName("trailer_url")   val trailerUrl: String?   = null,
    val seasons: List<ApiSeason>?                          = null,
    @SerializedName("created_at")    val createdAt: String?    = null,
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
    @SerializedName("has_stream")    val hasStream: Boolean    = false,
    @SerializedName("streaming_url") val streamingUrl: String? = null,
    val subtitles: List<ApiSubtitle>?                         = null,
) {
    fun formattedDuration(): String {
        val mins = duration ?: return ""
        val h = mins / 60
        val m = mins % 60
        return if (h > 0) "${h}h ${m}m" else "${m}m"
    }
}
